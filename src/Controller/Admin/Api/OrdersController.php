<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;

class OrdersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
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

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        $results = json_encode($orderResults);
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

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    /*
     * elenco ordini con acquisti dell'utente x fe
     */
    public function userCartGets() {

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.state_code != ' => 'CREATE-INCOMPLETE'];

        if(!empty($delivery_id)) {
            /*
             * per gli ordini per produttore non ho la consegna
             */
            $where += ['Orders.delivery_id' => $delivery_id, 
                    // 'Orders.id' => 20240
                      ];
        }

        $results = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries', 'SuppliersOrganizations' => ['Suppliers'],
                                          /* estrae anche gli ordini senza acquisti, perche' query aggiuntiva hasMany
                                          'Carts' => ['conditions' => ['Carts.user_id' => $user->id,
                                                                       'Carts.organization_id' => $organization_id,
                                                                        'Carts.deleteToReferent' => 'N']]
                                            */
                                          ])
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->toArray();

        

        /*
         * elimino ordini senza acquisti
         */
        $i=0;
        $newResults = [];
        foreach($results as $numResult => $result) {

            $found_cart = false;

            $newResults[$i] = $result;
            $newResults[$i]['article_orders'] = [];

            $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
            $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $result);

            if($articlesOrdersTable!==false) {

                $where['order_id'] = $result['id'];

                $options = [];
                $options['sort'] = [];
                $options['limit'] = Configure::read('sql.no.limit');
                $options['page'] = 1;
                $articlesOrdersResults = $articlesOrdersTable->getCarts($user, $organization_id, $user->id, $result, $where, $options);
                // debug($articlesOrdersResults);

                /*
                 * estraggo solo quelli acquistati dallo user
                 */
                $ii=0;
                foreach($articlesOrdersResults as  $numResult2 => $articlesOrdersResult) { 
                    /*
                     * se lo user non ha acquisti e' cmq valorizzato qta / qta_new
                     */
                    if(!isset($articlesOrdersResult['cart']) || !isset($articlesOrdersResult['cart']['user_id']) || 
                        empty($articlesOrdersResult['cart']['user_id'])) { 
                         unset($articlesOrdersResult[$numResult2]);
                         unset($results[$numResult]);
                    }
                    else {
                        $found_cart = true;
                        $articlesOrdersResult = new ApiArticleOrderDecorator($articlesOrdersResult); 
                        $newResults[$i]['article_orders'][$ii] = $articlesOrdersResult->results;
                        $ii++;
                    }
                }
            } // end if($articlesOrdersTable!==false) 

            if($found_cart) {
                $i++;
                $found_cart = false;
            }
            else {
                unset($newResults[$i]);
            }

        } // end foreach($results as $numResult => $result) 

        $results = json_encode($newResults);
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

        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $results = [];
   
        $order_id = $this->request->getData('order_id');
        $order_type_id = $this->request->getData('order_type_id');
        $page = $this->request->getData('page');
        $q = trim($this->request->getData('q'));
        if(empty($page)) $page = 1;
        // debug($order_id);

        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);

        if($articlesOrdersTable!==false) {

            $where['order_id'] = $order_id;
            if(!empty($q)) {
                $where_q = [];
                if(strpos($q, ' ')!==false) {
                    $qs = explode(' ', $q);
                    foreach($qs as  $numResult => $q) {
                        $where_q[$numResult] = ['or' => ['Articles.name LIKE' => '%'.$q.'%',
                                                         'Articles.nota LIKE' => '%'.$q.'%']];
                    }
                }
                else {
                    $where_q = ['or' => ['Articles.name LIKE' => '%'.$q.'%',
                                          'Articles.nota LIKE' => '%'.$q.'%']];
                }
                $where['Articles'] = $where_q;
            }

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.limit');
            $options['page'] = $page;
            $results = $articlesOrdersTable->getCarts($user, $organization_id, $user->id, $orderResults, $where, $options);
        
            if(!empty($results)) {
                $results = new ApiArticleOrderDecorator($results);
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

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }     
}