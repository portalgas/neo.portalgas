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
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /*
     * dettaglio ordine x fe
     */
    public function get() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');
        $results = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        return $this->_response($results); 
    } 

    /*
     * elenco ordini x fe
     */
    public function gets() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];

        if(!empty($delivery_id)) {
            /*
             * per gli ordini per produttore non ho la consegna
             */
            $where += ['Orders.delivery_id' => $delivery_id];
        }

        $results = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries', 'SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();

        return $this->_response($results); 
    } 

    /*
     * /admin/api/orders/user-cart-gets
     * elenco ordini con acquisti dell'utente x fe
     */
    public function userCartGets() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;
        $delivery_id = $this->request->getData('delivery_id');

        $results = $this->Order->userCartGets($user, $organization_id, $delivery_id, $debug);

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
        $organization_id = $user->organization->id;

        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');
        $page = $this->request->getData('page');
        $q = trim($this->request->getData('q'));
        // debug($order_id);

        $options = [];
        $options['page'] = $page;
        $options['q'] = $q;
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
                                ->order(['Orders.data_inizio'])
                                ->all();

        return $this->_response($results);  
    }     
}