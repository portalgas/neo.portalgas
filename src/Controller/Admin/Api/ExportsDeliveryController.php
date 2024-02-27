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
         * read file config CakePdf.php
         * debug(Configure::read('CakePdf'));   
         */
        define('DOMPDF_ENABLE_AUTOLOAD', false);
        define('DOMPDF_ENABLE_HTML5PARSER', true);
        define('DOMPDF_ENABLE_REMOTE', false);
        define('DEBUG_LAYOUT', true); 
        define("DOMPDF_ENABLE_CSS_FLOAT", true);
        define("DOMPDF_ENABLE_JAVASCRIPT", false);
        define("DEBUGPNG", true);
        define("DEBUGCSS", true);

        Configure::load('CakePdf', 'default');        
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
                
                $this->viewBuilder()->setClassName('CakePdf.Pdf');
                $this->viewBuilder()->setTheme('CakePdf');                     
            break;
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function get($debug=false) { 
        
        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }
                
        $debug = false;
        $delivery_id = $this->request->getData('delivery_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        /*
         * opzioni di stampa
         */
        $opts = $this->request->getData('opts');
        if(empty($opts)) {
            // l'excel effettua una post del form e le options non sono raggruppate dentro opts
            $datas = $this->request->getData();
            unset($datas['delivery_id']);
            unset($datas['print_id']);
            unset($datas['format']);
            if(!empty($datas)) {
                foreach($datas as $key => $value) 
                    $opts[$key] = $value;
            }
        }
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
        $where_orders = ['Orders.organization_id' => $this->_user->organization->id,
                        'Orders.isVisibleBackOffice' => 'Y',
                        'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'OPEN']];
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where_orders += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where_orders += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }
        // $where_orders += ['Orders.id' => 42591];
      
        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'sort' => ['SuppliersOrganizations.name'],
                                    'conditions' => $where_orders,
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();

        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');             

        $delivery_tot_order_only_cart = 0;  
        $delivery_tot_trasport = 0;  
        $delivery_tot_cost_more = 0;
        $delivery_tot_cost_less = 0;  
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
            $results[$numResult]['order']['state_code'] = $order->state_code;
            
            /* 
            * ordine gestito "Gestisci gli acquisti aggregati per l'importo degli utenti"
            *   => ricalcolo totali
            */
            $tot_order = 0;
            if($order->typeGest=='AGGREGATE') {                         
                $importo_aggregate = $summaryOrderAggregatesTable->getByOrderSummaryAggregates($this->_user, $this->_organization->id, $order->id);
                if(!empty($importo_aggregate)) {
                    $tot_order = $importo_aggregate;
                }
            }
            
            if($tot_order===0) {
                /* 
                * totale importo senza costi aggiuntivi
                * */
                $cartsTable = TableRegistry::get('Carts');
                $carts = $cartsTable->getByOrder($this->_user, $this->_organization->id, $order->id);
                foreach($carts as $cart) {
                    $final_price = $this->getCartFinalPrice($cart);
                    $tot_order += $final_price; 
                }
            }
            $results[$numResult]['order']['tot_order_only_cart'] = $tot_order;

            $delivery_tot_order_only_cart += $tot_order;  
            $delivery_tot_trasport += $order->trasport;  
            $delivery_tot_cost_more += $order->cost_more;
            $delivery_tot_cost_less += $order->cost_less;
            
        } // foreach($delivery->orders as $order) 

        $title = 'Doc. con acquisti della consegna raggruppati per produttore<br>';
        $title .= __('Delivery').' '.$this->getDeliveryLabel($delivery, ['year'=> true]).' '.$this->getDeliveryDateLabel($delivery);
        $this->set(compact('delivery', 'results', 'title'));
        $this->set(compact('delivery_tot_order_only_cart', 'delivery_tot_trasport', 'delivery_tot_cost_more', 'delivery_tot_cost_less', 'delivery_tot_importo'));

        $this->_filename = 'acquisti-consegna-raggruppati-produttore';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
                Configure::write('CakePdf.filename', $this->_filename.'.pdf');
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
                    'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'OPEN'],
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
        // $where += ['Orders.id' => 40396]; 

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
        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');          
                            
        $usersTable = TableRegistry::get('Users');
        $where = ['username NOT LIKE' => '%portalgas.it'];    
        $users = $usersTable->gets($this->_user, $this->_user->organization->id, $where);
        if($users->count()>0) {
            $i_user=0;
            $delivery_tot_only_cart = 0;
            $delivery_tot_trasport = 0;
            $delivery_tot_cost_more = 0;
            $delivery_tot_cost_less = 0;
            $delivery_tot_importo = 0; 
            foreach($users as $user) {

                $i_order=0;
                $tot_user_importo_only_cart = 0;
                $tot_user_trasport = 0;
                $tot_user_cost_more = 0;
                $tot_user_cost_less = 0;
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

                        $results[$i_user]['orders'][$i_order]['importo_trasport'] = 0;
                        if($order->hasTrasport=='Y' && $order->trasport>0) {
                            $summaryOrderTrasport = $summaryOrderTrasportsTable->find()->where($where)->first();
                            if(!empty($summaryOrderTrasport) && $summaryOrderTrasport->importo_trasport>0)
                                $results[$i_user]['orders'][$i_order]['importo_trasport'] = $summaryOrderTrasport->importo_trasport;
                            else 
                                $results[$i_user]['orders'][$i_order]['importo_trasport'] = 0;
                        }
                        $tot_user_trasport += $results[$i_user]['orders'][$i_order]['importo_trasport'];

                        $results[$i_user]['orders'][$i_order]['importo_cost_more'] = 0;
                        if($order->hasCostMore=='Y' && $order->cost_more>0) {
                            $summaryOrderCostMore = $summaryOrderCostMoresTable->find()->where($where)->first();
                            if(!empty($summaryOrderCostMore) && $summaryOrderCostMore->importo_cost_more>0)
                                $results[$i_user]['orders'][$i_order]['importo_cost_more'] = $summaryOrderCostMore->importo_cost_more;
                            else 
                                $results[$i_user]['orders'][$i_order]['importo_cost_more'] = 0;
                        }
                        $tot_user_cost_more += $results[$i_user]['orders'][$i_order]['importo_cost_more'];

                        $results[$i_user]['orders'][$i_order]['importo_cost_less'] = 0;
                        if($order->hasCostLess=='Y' && $order->cost_less>0) {
                            $summaryOrderCostLess = $summaryOrderCostLessesTable->find()->where($where)->first();
                            if(!empty($summaryOrderCostLess) && !empty($summaryOrderCostLess->importo_cost_less))
                                $results[$i_user]['orders'][$i_order]['importo_cost_less'] = $summaryOrderCostLess->importo_cost_less;
                            else 
                                $results[$i_user]['orders'][$i_order]['importo_cost_less'] = 0;
                        }
                        $tot_user_cost_less += $results[$i_user]['orders'][$i_order]['importo_cost_less']; 
                        
                        /* 
                        * ordine gestito "Gestisci gli acquisti aggregati per l'importo degli utenti"
                        *   => ricalcolo totali
                        */
                        if($order->typeGest=='AGGREGATE') {
                            $importo_aggregate = $summaryOrderAggregatesTable->getByUserSummaryAggregates($this->_user, $this->_organization->id, $user->id, $order->id);
                            if(!empty($importo_aggregate)) {
                                $tot_importo = $importo_aggregate;
                            }
                        }
                 
                        $results[$i_user]['orders'][$i_order]['tot_importo_only_cart'] = $tot_importo;
                        $results[$i_user]['orders'][$i_order]['tot_importo'] = ($tot_importo + $results[$i_user]['orders'][$i_order]['importo_trasport'] + $results[$i_user]['orders'][$i_order]['importo_cost_more'] + $results[$i_user]['orders'][$i_order]['importo_cost_less']);
                        $tot_user_importo_only_cart += $results[$i_user]['orders'][$i_order]['tot_importo_only_cart'];
                        $tot_user_importo += $results[$i_user]['orders'][$i_order]['tot_importo'];
                        // debug($tot_user_importo);
                                                    
                        $i_order++;
                    } // end if($carts->count()>0)
                } // foreach($orders as $order)
                
                if($tot_user_importo>0) {
                    $results[$i_user]['user']['tot_user_importo_only_cart'] = $tot_user_importo_only_cart;
                    $results[$i_user]['user']['tot_user_trasport'] = $tot_user_trasport;
                    $results[$i_user]['user']['tot_user_cost_more'] = $tot_user_cost_more;
                    $results[$i_user]['user']['tot_user_cost_less'] = $tot_user_cost_less;
                    $results[$i_user]['user']['tot_user_importo'] = $tot_user_importo;

                    $delivery_tot_only_cart += $tot_user_importo_only_cart;
                    $delivery_tot_trasport += $tot_user_trasport;
                    $delivery_tot_cost_more += $tot_user_cost_more;
                    $delivery_tot_cost_less += $tot_user_cost_less;   
                    $delivery_tot_importo += $tot_user_importo;
                    $i_user++;
                }

            }  // foreach($users as $user)          
        } // end if($users->count()>0)
        // dd($results);
        $title = 'Doc. con acquisti della consegna raggruppati per gasista<br>';
        $title .= __('Delivery').' '.$this->getDeliveryLabel($delivery, ['year'=> true]).' '.$this->getDeliveryDateLabel($delivery);
        $this->set(compact('delivery', 'results', 'title'));
        $this->set(compact('delivery_tot_only_cart', 'delivery_tot_trasport', 'delivery_tot_cost_more', 'delivery_tot_cost_less', 'delivery_tot_importo'));

        $this->_filename = 'acquisti-consegna-raggruppati-gasista';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
                Configure::write('CakePdf.filename', $this->_filename.'.pdf');
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
        $where_orders = ['Orders.organization_id' => $this->_user->organization->id,
                        'Orders.isVisibleBackOffice' => 'Y',
                        'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'OPEN']];
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where_orders += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where_orders += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }
        // $where_orders += ['Orders.id' => 41266];

        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'sort' => ['SuppliersOrganizations.name'],
                                    'conditions' => $where_orders,
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();

        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates'); 
                    
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
            $results[$numResult]['order']['state_code'] = $order->state_code;
            
            /* 
             * totale importo senza costi aggiuntivi
             * */
            $cartsTable = TableRegistry::get('Carts');
            $carts = $cartsTable->getByOrder($this->_user, $this->_organization->id, $order->id);
            $tot_order = 0;
            $user_id_old = 0;
            $user_ids = [];
            foreach($carts as $cart) {

                // memorizzo un array con tutti gli user di un ordine
                $user_ids[$cart->user_id] = $cart->user_id;

                $final_price = $this->getCartFinalPrice($cart);
                ($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;
                // debug('final_price '.$final_price);
                $tot_order += $final_price; 

                if(!isset($results[$numResult]['order']['users'][$cart->user_id])) {
                    $results[$numResult]['order']['users'][$cart->user_id]['tot_qta'] = 0;
                    $results[$numResult]['order']['users'][$cart->user_id]['tot_importo'] = 0;    
                }
                $results[$numResult]['order']['users'][$cart->user_id]['tot_qta'] += $final_qta;
                $results[$numResult]['order']['users'][$cart->user_id]['tot_importo'] += $final_price;
                
                if($cart->user_id!=$user_id_old) {
                    if($order->hasTrasport=='Y' && $order->trasport>0) {
                        $results[$numResult]['order']['users'][$cart->user_id]['importo_trasport'] = $this->_getUserImportoTrasport($this->_user, $cart->organization_id, $cart->user_id, $order->id);
                    }
                    if($order->hasCostMore=='Y' && $order->cost_more>0) {
                        $results[$numResult]['order']['users'][$cart->user_id]['importo_cost_more'] = $this->_getUserImportoCostMore($this->_user, $cart->organization_id, $cart->user_id, $order->id);
                    }
                    if($order->hasCostLess=='Y' && $order->cost_less>0) {
                        $results[$numResult]['order']['users'][$cart->user_id]['importo_cost_less'] = $this->_getUserImportoCostLess($this->_user, $cart->organization_id, $cart->user_id, $order->id);
                    }
                } // if($cart->user_id!=$user_id_old)

                $results[$numResult]['order']['carts'][] = $cart;

                $user_id_old = $cart->user_id; 
            } // end foreach($carts as $cart)
      
            $results[$numResult]['order']['tot_order_only_cart'] = $tot_order;
            
            /* 
             * ordine gestito "Gestisci gli acquisti aggregati per l'importo degli utenti"
             *   => ricalcolo totali
             */
            if($order->typeGest=='AGGREGATE') {
                $tot_order = 0;
                foreach($user_ids as $user_id) {
                    $importo_aggregate = $summaryOrderAggregatesTable->getByUserSummaryAggregates($this->_user, $this->_organization->id, $user_id, $order->id);
                    if(!empty($importo_aggregate)) {
                        $results[$numResult]['order']['users'][$user_id]['tot_importo'] = $importo_aggregate; 
                        $tot_order += $importo_aggregate; 
                    }
                }
                $results[$numResult]['order']['tot_order_only_cart'] = $tot_order;
            }

        } // foreach($delivery->orders as $order) 

        $title = 'Doc. con acquisti della consegna raggruppati per produttore e dettaglio acquisti<br>';
        $title .= __('Delivery').' '.$this->getDeliveryLabel($delivery, ['year'=> true]).' '.$this->getDeliveryDateLabel($delivery);
        $this->set(compact('delivery', 'results', 'delivery_tot_importo', 'title'));

        $this->_filename = 'acquisti-consegna-raggruppati-produttore-e-acquisti';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
                Configure::write('CakePdf.filename', $this->_filename.'.pdf');
            break;
        }

        return true;
    } 

    // Doc. con acquisti della consegna raggruppati per gasista e dettaglio acquisti
    private function _toDeliveryByUsersAndCarts($format, $delivery_id, $debug=false) {
        
        $i = 0;
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
        $where_orders = ['Orders.organization_id' => $this->_user->organization->id,
                        'Orders.isVisibleBackOffice' => 'Y',
                        'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'OPEN']];
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            // debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where_orders += ['Orders.supplier_organization_id' => '-1']; // utente senza referenze
            else
                $where_orders += ['Orders.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];
        }
        // $where_orders += ['Orders.id' => 41266];

        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'sort' => ['SuppliersOrganizations.name'],
                                    'conditions' => $where_orders,
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();
        if(!empty($delivery->orders)) {

            /* 
            * elenco users
            */
            $usersTable = TableRegistry::get('Users');
            $where = ['username NOT LIKE' => '%portalgas.it'];  
           // $where += ['id' => 6408];  
            $users = $usersTable->gets($this->_user, $this->_user->organization->id, $where);
            if($users->count()>0) {
                
                $cartsTable = TableRegistry::get('Carts');

                foreach($users as $user) {

                    $results[$i]['user'] = $user;
                    $results[$i]['user']['orders'] = [];

                    /*
                    * per ogni user controllo eventuali acquisti per ordine
                    */
                    foreach($delivery->orders as $numResult => $order) {
                   
                        $carts = $cartsTable->getByOrder($this->_user, $this->_organization->id, $order->id, $user->id);
                        if($carts->count()>0) {
                            
                            if(!isset($results[$i]['user']['orders'][$numResult])) {
                                $results[$i]['user']['orders'][$numResult] = [];
                                $results[$i]['user']['orders'][$numResult]['order']['state_code'] = $order->state_code;
                                $results[$i]['user']['orders'][$numResult]['order']['trasport'] = $order->trasport;
                                $results[$i]['user']['orders'][$numResult]['order']['cost_more'] = $order->cost_more;
                                $results[$i]['user']['orders'][$numResult]['order']['cost_less'] = $order->cost_less;                                
                                $results[$i]['user']['orders'][$numResult]['suppliers_organization'] = $order->suppliers_organization; 
                            }
                            $results[$i]['user']['orders'][$numResult]['carts'] = [];  
                            foreach($carts as $numResult2 => $cart) {
                
                                $results[$i]['user']['orders'][$numResult]['carts'][$numResult2] = $cart; 

                                $final_price = $this->getCartFinalPrice($cart);
                                ($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;
                                // if($cart->user_id==6408) debug('final_price '.$final_price);
                                $results[$i]['user']['orders'][$numResult]['carts'][$numResult2]['final_price'] = $final_price;

                                // totali dell'utente dell'ordine
                                if(!isset($results[$i]['user']['orders'][$numResult]['user_order_tot_importo']))  
                                    $results[$i]['user']['orders'][$numResult]['user_order_tot_importo'] = $final_price;                            
                                else
                                    $results[$i]['user']['orders'][$numResult]['user_order_tot_importo'] += $final_price;

                                if(!isset($results[$i]['user']['orders'][$numResult]['user_order_tot_qta']))  
                                    $results[$i]['user']['orders'][$numResult]['user_order_tot_qta'] = $final_qta;                            
                                else
                                    $results[$i]['user']['orders'][$numResult]['user_order_tot_qta'] += $final_qta;                                    

                            } // end foreach($carts as $cart)

                            /* 
                            * ordine gestito "Gestisci gli acquisti aggregati per l'importo degli utenti"
                            *   => ricalcolo totali
                            */
                            if($order->typeGest=='AGGREGATE') {
                                $summaryOrderAggregatesTable = TableRegistry::get('summaryOrderAggregates');
                                $importo_aggregate = $summaryOrderAggregatesTable->getByUserSummaryAggregates($this->_user, $this->_organization->id, $user->id, $order->id);
                                if(!empty($importo_aggregate)) {
                                    $results[$i]['user']['orders'][$numResult]['user_order_tot_importo'] = $importo_aggregate;
                                }
                            }

                            // totali dell'utente dell'ordine
                            if($order->hasTrasport=='Y' && $order->trasport>0) {
                                $results[$i]['user']['orders'][$numResult]['user_order_importo_trasport'] = $this->_getUserImportoTrasport($this->_user, $user->organization_id, $user->id, $order->id);
                            }
                            if($order->hasCostMore=='Y' && $order->cost_more>0) {
                                $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_more'] = $this->_getUserImportoCostMore($this->_user, $user->organization_id, $user->id, $order->id);
                            }
                            if($order->hasCostLess=='Y' && $order->cost_less>0) {
                                $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_less'] = $this->_getUserImportoCostLess($this->_user, $user->organization_id, $user->id, $order->id);
                            }

                            // totali dell'utente di tutti i suoi ordini
                            if(!isset($results[$i]['user']['user_tot_importo']))  
                                $results[$i]['user']['user_tot_importo'] = $results[$i]['user']['orders'][$numResult]['user_order_tot_importo'];                            
                            else
                                $results[$i]['user']['user_tot_importo'] += $results[$i]['user']['orders'][$numResult]['user_order_tot_importo'];
                            
                            if(!isset($results[$i]['user']['user_tot_qta']))  
                                $results[$i]['user']['user_tot_qta'] = $results[$i]['user']['orders'][$numResult]['user_order_tot_qta'];                            
                            else
                                $results[$i]['user']['user_tot_qta'] += $results[$i]['user']['orders'][$numResult]['user_order_tot_qta'];                

                            if(isset($results[$i]['user']['orders'][$numResult]['user_order_importo_trasport'])) {
                                if(!isset($results[$i]['user']['user_importo_trasport']))  
                                    $results[$i]['user']['user_importo_trasport'] = $results[$i]['user']['orders'][$numResult]['user_order_importo_trasport'];                            
                                else
                                    $results[$i]['user']['user_importo_trasport'] += $results[$i]['user']['orders'][$numResult]['user_order_importo_trasport']; 
                            }

                            if(isset($results[$i]['user']['orders'][$numResult]['user_order_importo_cost_more'])) {
                                if(!isset($results[$i]['user']['user_importo_cost_more']))  
                                    $results[$i]['user']['user_importo_cost_more'] = $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_more'];                            
                                else
                                    $results[$i]['user']['user_importo_cost_more'] += $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_more']; 
                            }

                            if(isset($results[$i]['user']['orders'][$numResult]['user_order_importo_cost_less'])) {
                                if(!isset($results[$i]['user']['user_importo_cost_less']))  
                                    $results[$i]['user']['user_importo_cost_less'] = $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_less'];                            
                                else
                                    $results[$i]['user']['user_importo_cost_less'] += $results[$i]['user']['orders'][$numResult]['user_order_importo_cost_less']; 
                            }


                        }  // end if($carts->count()>0) 
                    } // end foreach($delivery->orders as $numResult => $order) 
                 
                    if(empty($results[$i]['user']['orders'])) 
                        unset($results[$i]);  // gasista senza acquiti per tutti gli ordini della consegna
                    else 
                        $i++;

                } // end foreach($users as $user)
            } // end if($users->count()>0)

        } // end if(!empty($delivery->orders))
        
        $title = 'Doc. con acquisti della consegna raggruppati per produttore e dettaglio acquisti<br>';
        $title .= __('Delivery').' '.$this->getDeliveryLabel($delivery, ['year'=> true]).' '.$this->getDeliveryDateLabel($delivery);
        $this->set(compact('delivery', 'results', 'title'));

        $this->_filename = 'acquisti-consegna-raggruppati-gasista-e-acquisti';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
                Configure::write('CakePdf.filename', $this->_filename.'.pdf');
            break;
        }

        return true;
    }  

    private function _getUserImportoTrasport($_user, $organization_id, $user_id, $order_id) {
        $summaryOrderTable = TableRegistry::get('SummaryOrderTrasports');
        $where = ['organization_id' => $organization_id,
                  'user_id' => $user_id,
                  'order_id' => $order_id];
        $result = $summaryOrderTable->find()
                                    ->select(['importo_trasport'])
                                    ->where($where)
                                    ->first();
        if(!empty($result)) 
            return $result->importo_trasport;
        
        return 0;
    }

    private function _getUserImportoCostMore($_user, $organization_id, $user_id, $order_id) {
        $summaryOrderTable = TableRegistry::get('SummaryOrderCostMores');
        $where = ['organization_id' => $organization_id,
                  'user_id' => $user_id,
                  'order_id' => $order_id];
        $result = $summaryOrderTable->find()
                                    ->select(['importo_cost_more'])
                                    ->where($where)
                                    ->first();
        if(!empty($result)) 
            return $result->importo_cost_more;
        
        return 0;
    }
    
    private function _getUserImportoCostLess($_user, $organization_id, $user_id, $order_id) {
        $summaryOrderTable = TableRegistry::get('SummaryOrderCostLesses');
        $where = ['organization_id' => $organization_id,
                  'user_id' => $user_id,
                  'order_id' => $order_id];
        $result = $summaryOrderTable->find()
                                    ->select(['importo_cost_less'])
                                    ->where($where)
                                    ->first();
        if(!empty($result)) 
            return $result->importo_cost_less;
        
        return 0;
    }

    /*
     * importi aggregati per user
     */
    private function _getUserSummaryAggregates($_user, $organization_id, $user_id, $order_id) {
        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');
        $where = ['SummaryOrderAggregates.organization_id' => $organization_id,
                  'SummaryOrderAggregates.user_id' => $user_id,
                  'SummaryOrderAggregates.order_id' => $order_id];
        $result = $summaryOrderAggregatesTable->find()
                                             ->select(['importo'])
                                             ->where($where)
                                             ->first();
        if(!empty($result)) 
            return $result->importo;
        
        return 0;
    }

    /*
     * importi aggregati per ordine
     */
    private function _getOrderSummaryAggregates($_user, $organization_id, $order_id) {
        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');
        $where = ['SummaryOrderAggregates.organization_id' => $organization_id,
                  'SummaryOrderAggregates.order_id' => $order_id];

        $query = $summaryOrderAggregatesTable->find()->where($where);
        $result = $query->select(['sum' => $query->func()->max('importo')])->first();
        if(!empty($result)) 
            return $result->sum;
        
        return 0;
    }    
}