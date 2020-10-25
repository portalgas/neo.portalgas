<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleDecorator;

class OrdersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auths');
        $this->loadComponent('Cart');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /*
     * dettaglio ordine x fe
     */
    public function get() {

        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $order_id = $this->request->getData('order_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.id' => $order_id];

        $results = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries',
                                    'SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->first();

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    /*
     * elenco ordini x fe
     */
    public function gets() {

        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];

        $results = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes', 'SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    /* 
     * front-end - estrae gli articoli associati ad un ordine ed evenuali acquisti per user  
     */
    public function getArticlesOrdersByOrderId() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $results = [];
   
        $order_id = $this->request->getData('order_id');
        $page = $this->request->getData('page');
        $q = $this->request->getData('q');
        if(empty($page)) $page = 1;
        // debug($order_id);

        $ordersTable = TableRegistry::get('Orders');
        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.id' => $order_id];
        $orderResults = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes'])
                                ->where($where)
                                ->first();

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);

        if($articlesOrdersTable!==false) {

            $where['order_id'] = $order_id;
            if(!empty($q)) {
                $where['Articles'] = ['or' => [
                                       // $articlesOrdersTable->alias().'.name LIKE' => '%'.$q.'%',
                                        'Articles.name LIKE' => '%'.$q.'%',
                                        'Articles.nota LIKE' => '%'.$q.'%']
                                    ];
            }

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.limit');
            $options['page'] = $page;
            $results = $articlesOrdersTable->getCarts($user, $organization_id, $user->id, $orderResults, $where, $options);
        
            if(!empty($results)) {
                $results = new ApiArticleDecorator($results);
                //$results = new ArticleDecorator($results);
                $results = $results->results;
            }
        }

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    /* 
     * aggiornamento produttori per gestione chi e' escluso dal prepagato
     */
    public function getByDelivery() {

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

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    public function managementCart() {
        
        $debug = false;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        // debug($article);

        $results = [];
   
        $article = $this->request->getData('article');
        $results = $this->Cart->managementCart($user, $user->organization->id, $article, $debug);
        
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }     
}