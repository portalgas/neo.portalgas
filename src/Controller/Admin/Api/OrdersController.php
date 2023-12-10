<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class OrdersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Cart');
        $this->loadComponent('Order');
        $this->loadComponent('SocialMarket');
        $this->loadComponent('Distance');
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }
  
    /*
     * dettaglio ordine x fe
     * /admin/api/orders/get
     */
    public function get() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();

        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');

        ($order_type_id==Configure::read('Order.type.socialmarket')) ? $organization_id = Configure::read('social_market_organization_id'): $organization_id = $user->organization->id;

        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);
        if($ordersTable===false) {
            $results['code'] = 500;
            $results['message'] = __('msg_error_param_order_type_id');
            $results['errors'] = '';
            $continua = false;
            return $this->_response($results);
        }

        $ordersTable->addBehavior('Orders');
        $results = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        /*
         * distance
         */
        if(!empty($results)) {
            $distance = $this->Distance->get($user, $results->suppliers_organization);
            $results->distance = $distance;            
        }

        return $this->_response($results); 
    } 

    /*
     * elenco ordini x fe
     */
    public function gets($order_type_id) {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();

        ($order_type_id==Configure::read('Order.type.socialmarket')) ? $organization_id = Configure::read('social_market_organization_id'): $organization_id = $user->organization->id;
        
        $delivery_id = $this->request->getData('delivery_id');

        /* 
         * escludo Order.type.gas_parent_groups perche' li non posso fare acquisti
         * solo Order.type.gas_groups
         */
        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.order_type_id != ' => Configure::read('Order.type.gas_parent_groups'),          
                  'Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];

        if(!empty($delivery_id)) {
            /*
             * per gli ordini per produttore non ho la consegna
             */
            $where += ['Orders.delivery_id' => $delivery_id];
        }

        if(isset($user->organization->paramsConfig['hasGasGroups']) && $user->organization->paramsConfig['hasGasGroups']=='Y') {
            // ctrl che l'utente appartertenga al gruppo 
            $gasGroupsTable = TableRegistry::get('GasGroups');
            $gasGroups = $gasGroupsTable->findMyLists($user, $organization_id, $user->id);
            if(empty($gasGroups))
                $where += ['Orders.gas_group_id' => 0]; // utente non associato in alcun gruppo, prendo ordini non del gruppo 
            else {
                $acls = array_keys($gasGroups);
                // $acls = array_merge($acls, [0]);
                $where += ['Orders.gas_group_id IN ' => $acls];
            }
        } // end if($user->organization->paramsConfig['hasGasGroups']=='Y') 
       
        $ordersTable = TableRegistry::get('Orders');

        /*
         * todo per tutte le tipologie di ordini
         */
        if($order_type_id==Configure::read('Order.type.socialmarket')) {
            $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);
            if($ordersTable===false) {
                $results['code'] = 500;
                $results['message'] = __('msg_error_param_order_type_id');
                $results['errors'] = '';
                $continua = false;
                return $this->_response($results);
            }

            $ordersTable->addBehavior('Orders');
            $results = $ordersTable->gets($user, $organization_id, $where, $debug);
        }
        else {
            $results = $ordersTable->find()
                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries',
                    'SuppliersOrganizations' => [
                        'Suppliers',
                        /* 'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles']]*/
                    ]
                ])
                ->where($where)
                ->order(['Orders.data_inizio', 'SuppliersOrganizations.name'])
                ->all();
        }

        return $this->_response($results); 
    } 

    /*
     * /admin/api/orders/user-cart-gets
     * elenco ordini con acquisti dell'utente x fe
     */
    public function userCartGets($order_type_id)
    {
        $debug = false;
        $results = [];

        $user = $this->Authentication->getIdentity();

        ($order_type_id == Configure::read('Order.type.socialmarket')) ? $organization_id = Configure::read('social_market_organization_id') : $organization_id = $user->organization->id;

        switch ($order_type_id) {
            case Configure::read('Order.type.socialmarket'):
                $order_id = $this->request->getData('order_id');       // ordini socialmarket
                $results = $this->SocialMarket->userCartGets($user, $organization_id, $order_id, $debug);
                break;
            default:
                $delivery_id = $this->request->getData('delivery_id'); // ordini GAS
                $results = $this->Order->userCartGets($user, $organization_id, $delivery_id, [], $debug);
            break;
        }
        // debug($results);

        return $this->_response($results);  
    } 

    /* 
     * /admin/api/orders/getArticlesOrdersByOrderId 
     * front-end - estrae gli articoli associati ad un ordine ed evenuali acquisti per user  
     */
    public function getArticlesOrdersByOrderId() {

        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');

        ($order_type_id==Configure::read('Order.type.socialmarket')) ? $organization_id = Configure::read('social_market_organization_id'): $organization_id = $user->organization->id;

        $page = $this->request->getData('page');
        $q = trim($this->request->getData('q'));
        $search_categories_article_id = $this->request->getData('search_categories_article_id');
        $sort = $this->request->getData('sort');
        // debug($order_id);

        $options = [];
        $options['page'] = $page;
        $options['q'] = $q;
        $options['search_categories_article_id'] = $search_categories_article_id;
        $options['sort'] = $sort;
        $options['sql_limit'] = Configure::read('sql.limit');

        $results = [];
        $results = $this->Order->getArticlesOrdersByOrderId($user, $organization_id, $order_id, $order_type_id, $options, $debug);

        return $this->_response($results); 
    } 

    /* 
     * aggiornamento produttori per gestione chi e' escluso dal prepagato
     */
    public function getByDelivery() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        $orders_state_code = $this->request->getData('orders_state_code');

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id];
        if(!empty($orders_state_code))
            $where += ['Orders.state_code' => $orders_state_code];

        $results = $ordersTable->find()
                                ->contain(['SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->order(['SuppliersOrganizations.name', 'Orders.data_inizio'])
                                ->all();

        return $this->_response($results);  
    }     
}