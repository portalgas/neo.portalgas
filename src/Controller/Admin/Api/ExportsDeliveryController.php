<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream; 

class ExportsDeliveryController extends AppController {
    
    use Traits\UtilTrait;

    private $_filename = '';

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Order');

        /*
         * non si puo' disabilitare nel controller
         * settarlo in .env export DEBUG="false" 
         * Configure::write('debug', 0);
         */
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    
        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');
   
        switch($format) {
            case 'HTML':
                $this->viewBuilder()->setLayout('ajax');
            break;
            case 'XLSX':
                $this->viewBuilder()->disableAutoLayout();
                $this->response = $this->response->withDownload($this->_filename);            
            break;
            case 'PDF':
                $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                    ->setTemplate('/Admin/Api/ExportsDelivery/pdf/'.$print_id) 
                    ->setLayout('../../Layout/pdf/default') 
                    ->setClassName('CakePdf.Pdf');             
            break;
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function get($debug=false) { 
        
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }
                
        $debug = false;
        $delivery_id = $this->request->getData('delivery_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        /*
         * opzioni di stampa
         */
        $opts = $this->request->getData('opts');
        $this->set(compact('opts'));

        $method = '_'.$print_id;
        $results = $this->{$method}($format, $delivery_id);

        $this->set(compact('delivery_id', 'format'));
        
        switch($format) {
            case 'HTML':
                $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
                $this->render('/Admin/Api/ExportsDelivery/pdf/'.$print_id);
            break;
            case 'XLSX':
                $this->render('/Admin/Api/ExportsDelivery/xlsx/'.$print_id);
            break;
            case 'PDF':
                Configure::write('CakePdf', [
                    'engine' => 'CakePdf.DomPdf', // 'CakePdf.WkHtmlToPdf',
                    'margin' => [
                        'bottom' => 15,
                        'left' => 50,
                        'right' => 30,
                        'top' => 45
                    ],
                    'orientation' => 'portrait', // landscape (orizzontale) portrait (verticale)
                    'download' => false, // This can be omitted if "filename" is specified.
                ]);
                $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
            break;
        }    
    }   
    
    // Doc. con acquisti della consegna raggruppati per produttore
    private function _toDeliveryBySuppliers($format, $delivery_id, $debug=false) {
        
        $results = [];

        /* 
        * dati consegna
        */
        $deliveriesTable = TableRegistry::get('Deliveries');
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.id' => $delivery_id];
        /* 
         * profilazione $user->acl['isReferentGeneric'] 
         */
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }

        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'sort' => ['SuppliersOrganizations.name'],
                                    'conditions' => ['Orders.organization_id' => $this->_user->organization->id,
                                                    'Orders.isVisibleBackOffice' => 'Y',
                                                    'Orders.state_code != ' => 'CREATE-INCOMPLETE'],
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();

        $delivery_tot_importo = 0;                        
        foreach($delivery->orders as $numResult => $order) {
            
            // debug($order->suppliers_organization->name);
            $results[$numResult] = [];
            $results[$numResult]['suppliers_organization'] = $order->suppliers_organization;

            $results[$numResult]['order']['tot_order'] = $ordersTable->getTotImporto($this->_user, $this->_organization->id, $order);
            $delivery_tot_importo = ($delivery_tot_importo + $results[$numResult]['order']['tot_order']); 
            $results[$numResult]['order']['trasport'] = $order->trasport;
            $results[$numResult]['order']['cost_more'] = $order->cost_more;
            $results[$numResult]['order']['cost_less'] = $order->cost_less;
            
            /* 
             * totale importo senza costi aggiuntivi
             * */
            $cartsTable = TableRegistry::get('Carts');
            $carts = $cartsTable->getByOrder($this->_user, $this->_organization->id, $order->id);
            $tot_order = 0;
            foreach($carts as $cart) {
                $final_price = $this->getCartFinalPrice($cart);
                // debug('final_price '.$final_price);
                $tot_order += $final_price; 
            }
            $results[$numResult]['order']['tot_order_only_cart'] = $tot_order;
            
        } // foreach($delivery->orders as $order) 
        $this->set(compact('delivery', 'results', 'delivery_tot_importo'));

        $this->_filename = 'acquisti-consegna-raggruppati-produttore';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
            break;
        }

        return true;
    } 
    
    // Doc. con acquisti della consegna raggruppati per gasista
    private function _toDeliveryByUsers($format, $delivery_id, $debug=false) {
 
        $results = [];

        /* 
        * dati consegna
        */
        $deliveriesTable = TableRegistry::get('Deliveries');
        $where = ['Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.id' => $delivery_id];
        $delivery = $deliveriesTable->find()
                                ->where($where)
                                ->first();
        /*
         * ordini delle consegna
         */
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Orders.organization_id' => $this->_user->organization->id,
                    'Orders.isVisibleBackOffice' => 'Y',
                    'Orders.state_code != ' => 'CREATE-INCOMPLETE',
                    'Orders.delivery_id' => $delivery->id];
        /* 
         * profilazione $user->acl['isReferentGeneric'] 
         */
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }                    
        $orders = $ordersTable->find()
                                ->contain(['SuppliersOrganizations' => ['Suppliers']])    
                                    ->where($where)
                                    ->order(['SuppliersOrganizations.name'])
                                    ->all();
        /*
         * elenco gasisti
         */
        $cartsTable = TableRegistry::get('Carts');
        $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');
        $summaryOrderCostMoresTable = TableRegistry::get('SummaryOrderCostMores');
        $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');
        
        $usersTable = TableRegistry::get('Users');
        $where = ['username NOT LIKE' => '%portalgas.it'];    
        $users = $usersTable->gets($this->_user, $this->_user->organization->id, $where);
        if($users->count()>0) {
            $i_user=0;
            $delivery_tot_importo = 0;
            foreach($users as $user) {
                $i_order=0;
                $tot_user_importo = 0;
                foreach($orders as $order) {
                    $where = ['Carts.user_id' => $user->id,
                              'Carts.organization_id' => $this->_user->organization->id,
                              'Carts.order_id' => $order->id,
                              'Carts.deleteToReferent' => 'N',
                              'Carts.stato' => 'Y'];
                    $carts = $cartsTable->find()
                                ->contain(['ArticlesOrders' => ['Articles']])
                                ->where($where)
                                ->all();
    
                    if($carts->count()>0) {
                        // debug($carts);
                        if(!isset($results[$i_user])) {
                            $results[$i_user] = [];
                            $results[$i_user]['user']['id'] = $user->id;
                            $results[$i_user]['user']['name'] = $user->name;
                            $results[$i_user]['user']['username'] = $user->username;
                            $results[$i_user]['user']['email'] = $user->email;
                            $results[$i_user]['user']['phone'] = $user->phone;
                        }
                        if($i_order==0) {
                            $results[$i_user]['orders'] = [];
                            $results[$i_user]['orders'][$i_order] = [];
                        }
                        $results[$i_user]['orders'][$i_order]['order'] = $order;
    
                        $tot_importo = 0;
                        foreach($carts as $cart) {
                            $final_price = $this->getCartFinalPrice($cart);
                            $tot_importo += $final_price; 
                        }

                        // costi aggiuntivi
                        $where = ['organization_id' => $this->_user->organization->id,
                                'order_id' => $order->id,
                                'user_id' => $user->id];

                        $summaryOrderTrasport = $summaryOrderTrasportsTable->find()->where($where)->first();
                        if(!empty($summaryOrderTrasport) && $summaryOrderTrasport->importo_trasport>0)
                            $results[$i_user]['orders'][$i_order]['importo_trasport'] = $summaryOrderTrasport->importo_trasport;
                        else 
                            $results[$i_user]['orders'][$i_order]['importo_trasport'] = 0;

                        $summaryOrderCostMore = $summaryOrderCostMoresTable->find()->where($where)->first();
                        if(!empty($summaryOrderCostMore) && $summaryOrderCostMore->importo_cost_more>0)
                            $results[$i_user]['orders'][$i_order]['importo_cost_more'] = $summaryOrderCostMore->importo_cost_more;
                        else 
                            $results[$i_user]['orders'][$i_order]['importo_cost_more'] = 0;
                    
                        $summaryOrderCostLess = $summaryOrderCostLessesTable->find()->where($where)->first();
                        if(!empty($summaryOrderCostLess) && $summaryOrderCostLess->importo_cost_less>0)
                            $results[$i_user]['orders'][$i_order]['importo_cost_less'] = $summaryOrderCostLess->importo_cost_less;
                        else 
                            $results[$i_user]['orders'][$i_order]['importo_cost_less'] = 0;
                   
                        $results[$i_user]['orders'][$i_order]['tot_importo_only_cart'] = $tot_importo;
                        $results[$i_user]['orders'][$i_order]['tot_importo'] = ($tot_importo + $results[$i_user]['orders'][$i_order]['importo_trasport'] + $results[$i_user]['orders'][$i_order]['importo_cost_more'] + (-1 * $results[$i_user]['orders'][$i_order]['importo_cost_less']));
                        $tot_user_importo += $results[$i_user]['orders'][$i_order]['tot_importo'];
                        // debug($results);

                                                    
                        $i_order++;
                    } // end if($carts->count()>0)
                } // foreach($orders as $order)
                
                if($tot_user_importo>0) {
                    $results[$i_user]['user']['tot_user_importo'] = $tot_user_importo;
                    $delivery_tot_importo += $tot_user_importo;
                    $i_user++;
                }

            }  // foreach($users as $user)          
        } // end if($users->count()>0)
        // dd($results);
        $this->set(compact('delivery', 'results', 'delivery_tot_importo'));

        $this->_filename = 'acquisti-consegna-raggruppati-gasista';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
            break;
        }
       
        return true;
    }

    // Doc. con acquisti della consegna raggruppati per produttore e dettagli acquisti
    private function _toDeliveryBySuppliersAndCarts($format, $delivery_id, $debug=false) {
        
        $results = [];

        /* 
        * dati consegna
        */
        $deliveriesTable = TableRegistry::get('Deliveries');
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.id' => $delivery_id];
        /* 
         * profilazione $user->acl['isReferentGeneric'] 
         */
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }

        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'sort' => ['SuppliersOrganizations.name'],
                                    'conditions' => ['Orders.organization_id' => $this->_user->organization->id,
                                                    'Orders.isVisibleBackOffice' => 'Y',
                                                    'Orders.state_code != ' => 'CREATE-INCOMPLETE'],
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();

        $delivery_tot_importo = 0;                        
        foreach($delivery->orders as $numResult => $order) {
            
            // debug($order->suppliers_organization->name);
            $results[$numResult] = [];
            $results[$numResult]['suppliers_organization'] = $order->suppliers_organization;

            $results[$numResult]['order']['tot_order'] = $ordersTable->getTotImporto($this->_user, $this->_organization->id, $order);
            $delivery_tot_importo = ($delivery_tot_importo + $results[$numResult]['order']['tot_order']); 
            $results[$numResult]['order']['trasport'] = $order->trasport;
            $results[$numResult]['order']['cost_more'] = $order->cost_more;
            $results[$numResult]['order']['cost_less'] = $order->cost_less;
            
            /* 
             * totale importo senza costi aggiuntivi
             * */
            $cartsTable = TableRegistry::get('Carts');
            $carts = $cartsTable->getByOrder($this->_user, $this->_organization->id, $order->id);
            $tot_order = 0;
            foreach($carts as $cart) {
                $final_price = $this->getCartFinalPrice($cart);
                // debug('final_price '.$final_price);
                $tot_order += $final_price; 

                $results[$numResult]['order']['carts'][] = $cart;
            }
            $results[$numResult]['order']['tot_order_only_cart'] = $tot_order;
            
        } // foreach($delivery->orders as $order) 
        $this->set(compact('delivery', 'results', 'delivery_tot_importo'));

        $this->_filename = 'acquisti-consegna-raggruppati-produttore-e-acquisti';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
            break;
        }

        return true;
    } 

}