<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiArticleDecorator;
use App\Decorator\ApiSupplierDecorator;
use App\Traits;

/*
 * sostituisce htmlArticleOrdersController, l'html nella view non poteva contenere js
 */
class ArticleOrdersController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cart');
        $this->loadComponent('Des');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /* 
     * front-end - dettaglio articolo associato ad un ordine   
     */
    public function get() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $datas = [];
        $datas['order'] = [];
        $datas['articlesOrder'] = [];
        $datas['cart'] = [];

        $orderResults = [];
        $articlesOrdersResults = [];

        $order_id = $this->request->getData('order_id');
        $order_type_id = 0;
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = trim($this->request->getData('article_id'));

        $ordersTable = TableRegistry::get('Orders');

        /* 
          * ctrl se l'ordine e' SocialMarket perche' ha un organization_id diverso da quello dell'utente
          */
        $where = ['Orders.organization_id' => Configure::read('social_market_organization_id'), 'Orders.id' => $order_id];
        $orderResults = $ordersTable->find()->where($where)->first();
        if(!empty($orderResults)) {
            $order_type_id = $orderResults->order_type_id; 
            // prendo organization_id del GAS SocialMarket
            $order_organization_id = Configure::read('social_market_organization_id');
        }
        else {
            // prendo organization_id del GAS dell'utente
            $order_organization_id = $this->_organization->id;            
        }
            
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id, $order_id);
        if($ordersTable===false) {
            $results['code'] = 500;
            $results['message'] = __('msg_error_param_order_type_id');
            $results['errors'] = '';
            $continua = false;
            return $this->_response($results);
        }

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($this->_user, $order_organization_id, $order_id, $debug);
        if(!empty($orderResults)) {
            $supplier = $orderResults['suppliers_organization']['supplier'];
            $supplier = new ApiSupplierDecorator($this->_user, $supplier);
            $orderResults['suppliers_organization']['supplier'] = $supplier->results;
        }

        $ids = [];
        $ids['organization_id'] = $order_organization_id;
        $ids['order_id'] = $order_id;
        $ids['article_organization_id'] = $article_organization_id;
        $ids['article_id'] = $article_id;
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $orderResults);

        if($articlesOrdersTable!==false) {
            $articlesOrdersResults = $articlesOrdersTable->getByIds($this->_user, $this->_organization->id, $ids, $debug);

            $articlesOrdersResults2 = new ApiArticleOrderDecorator($this->_user, $articlesOrdersResults, $orderResults);
            $articlesOrdersResults = $articlesOrdersResults2->results;
        }

        $datas['order'] = $orderResults;
        $datas['articlesOrder'] = $articlesOrdersResults;

        /*
         * nota per il referente
         */
        $hasFieldCartNote = $this->_organization->paramsFields['hasFieldCartNote'];
        
        if($hasFieldCartNote=='Y') {
            
            $nota = '';
            $cartsTable = TableRegistry::get('Carts');

            $where = ['Carts.organization_id' => $order_organization_id,
                      'Carts.order_id' => $order_id,
                      'Carts.user_id' => $this->_user->id,
                      'Carts.article_organization_id' => $article_organization_id,
                      'Carts.article_id' => $article_id];
            // debug($where);

            $cartResults = $cartsTable->find()
                                        ->select(['nota'])
                                        ->where($where)
                                        ->first();
            if(!empty($cartResults))
                $datas['cart']['nota'] = $cartResults['nota'];
            else {
                /*
                 * l'articolo dev'essere prima acquitato
                 */
                $hasFieldCartNote = 'N';
                $datas['cart']['nota'] = '';
            }

        } // end if($hasFieldCartNote=='Y')
        $datas['cart']['hasFieldCartNote'] = $hasFieldCartNote;

        $results['results'] = $datas;
        
        return $this->_response($results); 
    } 

  /* 
   * gestione associazione articoli all'ordine
   * return
   *  proprietario listino: per gestione permessi di modifica
   *  article_orders: articoli gia' associati (con eventuali acquisti)
   *  articles: articoli da associare
   */    
    public function getAssociateToOrder() {

        $debug = false;
        $continua = true;

        $results = [];
        $results['esito'] = true;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $datas = [];
        $datas['can_edit'] = false;
        $datas['order'] = [];
        $datas['article_orders'] = [];
        $datas['articles'] = [];

        $organization_id = $this->request->getData('organization_id');
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');

        /* 
         * ordine 
         */
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id, $order_id);
        $ordersTable->addBehavior('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);
        
        $datas['order'] = $order;
 
        /* 
         * permessi di aggiornamento dei dati degli articoli 
         */ 
        $lifeCycleArticlesOrdersTable = TableRegistry::get('LifeCycleArticlesOrders');
        $datas['can_edit'] = $lifeCycleArticlesOrdersTable->canEditByOrder($this->_user, $this->_organization->id, $order, $debug);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
        if($articlesOrdersTable===false) {
            $results['esito'] = false;
            $results['results'] = $datas;
            $results['errors'] = 'ArticlesOrders factory';
            return $this->_response($results); 
        } 

        $dataAssociateToOrder = $articlesOrdersTable->getAssociateToOrder($this->_user, $this->_organization->id, $order);
        if($dataAssociateToOrder['esito']===false) {
            $results['esito'] = false;
            $results['results'] = [];
            $results['errors'] = $dataAssociateToOrder;
            return $this->_response($results); 
        }

        /* 
         * articoli gia' associati
         */         
        $article_orders = $dataAssociateToOrder['article_orders'];
        $article_orders2 = new ApiArticleOrderDecorator($this->_user, $article_orders, $order);
        $datas['article_orders'] = $article_orders2->results;

        /* 
         * articoli da associare
         */         
        $articles = $dataAssociateToOrder['articles'];
        $article2 = new ApiArticleDecorator($this->_user, $articles);
        $datas['articles'] = $article2->results;
            
        $results['results'] = $datas;
        
        return $this->_response($results); 
    }
    
    /* 
    * salvo articoli in articleOrders
    *  delete_article_orders: articoli gia' associati da eliminare
    *  update_article_orders: articoli gia' associati da aggiornare
    *  articles: articoli da associare (quelli is_select da associare)
    *
    * se DES e' il GAS e' titolare propago le modifiche
    */    
    public function setAssociateToOrder() {

        $debug = false;
        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $datas = [];
        $datas['order'] = [];
        $datas['article_orders'] = [];
        $datas['articles'] = [];

        $delete_article_orders = $this->request->getData('delete_article_orders');
        $update_article_orders = $this->request->getData('update_article_orders');
        $articles = $this->request->getData('articles');
        $organization_id = $this->request->getData('organization_id');
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');

        $ordersTable = TableRegistry::get('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
        if($articlesOrdersTable===false) {
            $results['esito'] = false;
            $results['results'] = $datas;
            $results['errors'] = 'ArticlesOrders factory';
            return $this->_response($results); 
        } 

        /* 
         * aggiorno article_orders 
         */
        if(!empty($update_article_orders)) {
            foreach($update_article_orders as $update_article_order) {

                $ids = [];
                $ids['organization_id'] = $update_article_order['organization_id'];
                $ids['order_id'] = $update_article_order['order_id'];
                $ids['article_organization_id'] = $update_article_order['article_organization_id'];
                $ids['article_id'] = $update_article_order['article_id'];
                
                $update_article_order['prezzo'] = $this->convertImport($update_article_order['prezzo_']);
                
                $articlesOrder = $articlesOrdersTable->getByIds($this->_user, $this->_organization->id, $ids, $debug);                
                $articlesOrder = $articlesOrdersTable->patchEntity($articlesOrder, $update_article_order);
                 //  debug($articlesOrder);
                
                /*
                * workaround
                */
                $articlesOrder->organization_id = $organization_id;
                $articlesOrder->order_id = $update_article_order['order_id'];
                $articlesOrder->article_organization_id = $update_article_order['article_organization_id'];
                $articlesOrder->article_id = $update_article_order['article_id'];
                
                if (!$articlesOrdersTable->save($articlesOrder)) {
                    $this->Flash->error($articlesOrder->getErrors());
                }  
            }
        }

        /* 
         * rimuovo article_orders dall'ordine
         */
        if(!empty($delete_article_orders)) {
            foreach($delete_article_orders as $delete_article_order) {

                $ids = [];
                $ids['organization_id'] = $delete_article_order['organization_id'];
                $ids['order_id'] = $delete_article_order['order_id'];
                $ids['article_organization_id'] = $delete_article_order['article_organization_id'];
                $ids['article_id'] = $delete_article_order['article_id'];
                
                $articlesOrder = $articlesOrdersTable->deleteByIds($this->_user, $this->_organization->id, $order, $ids, $debug);
            }
        }

        /*  
         * associo articolo all'ordine 
         */
        if(!empty($articles)) {
            $articlesOrdersTable->addsByArticles($this->_user, $this->_organization->id, $order, $articles);
        }

        /*
        * aggiorno stato ordine 'OPEN' // OPEN-NEXT  
        */ 
        $event = new Event('OrderListener.setStatus', $this, ['user' => $user, 'order' => $order]);
        $this->getEventManager()->dispatch($event);
                
        return $this->_response($results); 
    }        

    /* 
    * salvo articoli in articleOrders presi dall'ordine precedente
    */    
    public function setAssociateToPreviousOrder() {

        $debug = false;
        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $datas = [];

        $organization_id = $this->request->getData('organization_id');
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');

        $ordersTable = TableRegistry::get('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);

        // dati ordine precedente
        $previousOrder = $ordersTable->getPrevious($this->_user, $order);
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
        if($articlesOrdersTable===false) {
            $results['esito'] = false;
            $results['results'] = $datas;
            $results['errors'] = 'ArticlesOrders factory';
            return $this->_response($results); 
        } 

        $where = [];
        $where['organization_id'] = $organization_id;
        $where['order_id'] = $order_id;
        $articlesOrder = $articlesOrdersTable->deleteAll($where);

        /*  
         * associo articoli all'ordine 
         */
        $articlesOrdersTable->addsByArticlesOrders($this->_user, $this->_organization->id, $order, $previousOrder->articles_orders, true);
                
        return $this->_response($results); 
    }        
}