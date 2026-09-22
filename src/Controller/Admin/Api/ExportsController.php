<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;

class ExportsController extends AppController {
    
    use Traits\UtilTrait;

    /*
     * se true non stampa il pdf ma lo vedo a video
     */ 
    private $_debug = false;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Order');
        $this->loadComponent('Storeroom');
        $this->loadComponent('ProdGasPromotion');
        $this->loadComponent('CartSplit');
        $this->loadComponent('Distance');

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
        if(!$this->_debug) {
            $this->viewBuilder()->setClassName('CakePdf.Pdf');
            $this->viewBuilder()->setTheme('CakePdf'); 
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function userCart($delivery_id, $tmpl=null, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $storeroomResults = [];
        $title = '';

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($this->_user, $this->_organization->id, $delivery_id);
        if(!empty($delivery)) {
            
            $title = "Carrello della consegna ".$delivery->label.' <br />di '.$this->_user->username;
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

            $results = $this->Order->userCartGets($this->_user, $this->_organization->id, $delivery_id, [], $debug); 
            // debug($results);

            /*
             * storerooms
             */
            if ($this->_user->organization->paramsConfig['hasStoreroom'] == 'Y' && $this->_organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {
                $storeroomResults = $this->Storeroom->getArticlesByDeliveryId($this->_user, $this->_organization->id, $delivery_id, $options=[], $debug);            
            }

        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'storeroomResults', 'delivery', 'title'));
        $this->set('user', $this->_user);

        switch($tmpl) {
            case 'compact':
                $tmpl = '/Admin/Api/Exports/pdf/user_cart_compact';
                break;
            default:
                $tmpl = '/Admin/Api/Exports/pdf/user_cart';
                break;
        }

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render($tmpl);
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                                // Template/Admin/Api/Exports/pdf/user_cart.ctp 
                                ->setTemplate($tmpl) 
                                // Template/Layout/pdf/default.ctp
                                ->setLayout('../../Layout/pdf/default') 
                                // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
                                ->setClassName('CakePdf.Pdf'); 
                             
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function userCartSplitsByFamilies($order_type_id, $order_id, $tmpl=null, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $order = [];
        $title = '';

        $results = $this->CartSplit->getByOrderGroupByFamilies($this->_user, $this->_organization->id, $order_id, $this->_user->id); 
        if(!empty($results)) {
            
            $ordersTable = TableRegistry::get('Orders');
            $contains = ['OrderStateCodes', 'OrderTypes', 'Deliveries',
                        'SuppliersOrganizations' => [
                            'Suppliers',
                            'SuppliersOrganizationsReferents' =>
                                ['Users' => ['UserProfiles' => ['sort' => ['ordering']]]]
            ]];

            $where = ['Orders.organization_id' => $this->_organization->id, 'Orders.id' => $order_id];
            $order = $ordersTable->find()
                                  ->contain($contains)
                                  ->where($where)->first();

            if(!empty($order)) {
                /*
                 * aggiunge ad un ordine le eventuali
                 *  SummaryOrder
                 *  SummaryOrderTrapsort spese di trasporto
                 *  SummaryOrderMore spese generiche
                 *  SummaryOrderLess sconti
                 */
                $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');
                $summaryOrderPlusTable = TableRegistry::get('SummaryOrderPlus');
                // if($lifeCycleSummaryOrdersTable->canAddSummaryOrder($this->_user, $order->state_code)) {

                    $resultsSummaryOrderPlus = $summaryOrderPlusTable->addSummaryOrder($this->_user, $order, $this->_user->id);
         
                    $order->summary_order = $resultsSummaryOrderPlus->summary_order;
                    $order->summary_order_aggregate = $resultsSummaryOrderPlus->summary_order_aggregate;
                    $order->summary_order_trasport = $resultsSummaryOrderPlus->summary_order_trasport;
                    $order->summary_order_cost_more = $resultsSummaryOrderPlus->summary_order_cost_more;
                    $order->summary_order_cost_less = $resultsSummaryOrderPlus->summary_order_cost_less;

                    // $newResults = $this->ExportDoc->getCartCompliteOrder($order_id, $results, $resultsSummaryOrderAggregate, $resultsSummaryOrderTrasport, $resultsSummaryOrderCostMore, $resultsSummaryOrderCostLess, $debug);
                // }  // if($result->state_code=='PROCESSED-ON-DELIVERY' || $result->state_code=='CLOSE')

                /*
                 * referenti
                 */
                if(isset($order->suppliers_organization->suppliers_organizations_referents)) {
                    $referentsResult = new ApiSuppliersOrganizationsReferentDecorator($this->_user, $order->suppliers_organization->suppliers_organizations_referents, $order);
                    $order->referents = $referentsResult->results;
                    unset($order->suppliers_organization->suppliers_organizations_referents);
                }

            } // end if(!empty($order)) 

            $title = "Carrello dell'ordine ".$order->suppliers_organization->name.' <br />di '.$this->_user->username;
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'order', 'title'));
        $this->set('user', $this->_user);

        $tmpl = '/Admin/Api/Exports/pdf/user_cart_splits_by_families';
        
        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render($tmpl);
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                                // Template/Admin/Api/Exports/pdf/user_cart.ctp 
                                ->setTemplate($tmpl) 
                                // Template/Layout/pdf/default.ctp
                                ->setLayout('../../Layout/pdf/default') 
                                // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
                                ->setClassName('CakePdf.Pdf'); 
                             
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }

    public function userCartSplitsByArticles($order_type_id, $order_id, $tmpl=null, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $order = [];
        $title = '';

        $results = $this->CartSplit->getByOrder($this->_user, $this->_organization->id, $order_id, $this->_user->id); 
        if(!empty($results)) {
            
            $ordersTable = TableRegistry::get('Orders');
            $contains = ['OrderStateCodes', 'OrderTypes', 'Deliveries',
                        'SuppliersOrganizations' => [
                            'Suppliers',
                            'SuppliersOrganizationsReferents' =>
                                ['Users' => ['UserProfiles' => ['sort' => ['ordering']]]]
            ]];

            $where = ['Orders.organization_id' => $this->_organization->id, 'Orders.id' => $order_id];
            $order = $ordersTable->find()
                                  ->contain($contains)
                                  ->where($where)->first();

            if(!empty($order)) {
                /*
                 * aggiunge ad un ordine le eventuali
                 *  SummaryOrder
                 *  SummaryOrderTrapsort spese di trasporto
                 *  SummaryOrderMore spese generiche
                 *  SummaryOrderLess sconti
                 */
                $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');
                $summaryOrderPlusTable = TableRegistry::get('SummaryOrderPlus');
                // if($lifeCycleSummaryOrdersTable->canAddSummaryOrder($this->_user, $order->state_code)) {

                    $resultsSummaryOrderPlus = $summaryOrderPlusTable->addSummaryOrder($this->_user, $order, $this->_user->id);
         
                    $order->summary_order = $resultsSummaryOrderPlus->summary_order;
                    $order->summary_order_aggregate = $resultsSummaryOrderPlus->summary_order_aggregate;
                    $order->summary_order_trasport = $resultsSummaryOrderPlus->summary_order_trasport;
                    $order->summary_order_cost_more = $resultsSummaryOrderPlus->summary_order_cost_more;
                    $order->summary_order_cost_less = $resultsSummaryOrderPlus->summary_order_cost_less;

                    // $newResults = $this->ExportDoc->getCartCompliteOrder($order_id, $results, $resultsSummaryOrderAggregate, $resultsSummaryOrderTrasport, $resultsSummaryOrderCostMore, $resultsSummaryOrderCostLess, $debug);
                // }  // if($result->state_code=='PROCESSED-ON-DELIVERY' || $result->state_code=='CLOSE')

                /*
                 * referenti
                 */
                if(isset($order->suppliers_organization->suppliers_organizations_referents)) {
                    $referentsResult = new ApiSuppliersOrganizationsReferentDecorator($this->_user, $order->suppliers_organization->suppliers_organizations_referents, $order);
                    $order->referents = $referentsResult->results;
                    unset($order->suppliers_organization->suppliers_organizations_referents);
                }

            } // end if(!empty($order)) 

            $title = "Carrello dell'ordine ".$order->suppliers_organization->name.' <br />di '.$this->_user->username;
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'order', 'title'));
        $this->set('user', $this->_user);

        $tmpl = '/Admin/Api/Exports/pdf/user_cart_splits_by_articles';
        
        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render($tmpl);
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                                // Template/Admin/Api/Exports/pdf/user_cart.ctp 
                                ->setTemplate($tmpl) 
                                // Template/Layout/pdf/default.ctp
                                ->setLayout('../../Layout/pdf/default') 
                                // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
                                ->setClassName('CakePdf.Pdf'); 
                             
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }

    public function userPromotionCart($debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $storeroomResults = [];
        $title = '';

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN', 'PRODGASPROMOTION-GAS-USERS-CLOSE'];
        $prod_gas_promotion_organization_state_code = ['OPEN', 'CLOSE'];

        $results = $this->ProdGasPromotion->userCartGets($this->_user, $this->_organization_id, $this->_user->id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);

        if(!empty($results)) {

            $title = "Carrello delle promozioni";
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $results = $results['results'];
   
        } // end if(!empty($results))
        
        $this->set(compact('results', 'title', 'user'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/user_promotion_cart');
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                               ->setClassName('CakePdf.Pdf');            
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }    
}