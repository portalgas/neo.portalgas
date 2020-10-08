<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticleDecorator;
use App\Decorator\ApiArticleDecorator;

class OrdersGasController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Cart');
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * front-end - estrae gli articoli associati ad un ordine ed evenuali acquisti per user  
     */
    public function getCarts() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
   
        $order_id = $this->request->getData('order_id');
        // debug($order_id);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $user->organization->id, $order_id);

        if($articlesOrdersTable!==false) {
            $where['order_id'] = $order_id;
            $order = [];
            $results = $articlesOrdersTable->getCarts($user, $user->organization->id, $user->id, $where, $order);
        
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

    public function managementCart() {
        
        $debug = true;

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

    /* 
     * non ancora utilizzata  
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
        $where = [];
        $order = [];
   
        $order_id = $this->request->getData('order_id');
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $user->organization->id, $order_id);

        if($articlesOrdersTable!==false) {
            $results = $articlesOrdersTable->gets($user, $user->organization->id, $order_id, $where, $order);            
        }
        /*
        if(!empty($results)) {
            // $results = new ApiArticleDecorator($results);
            $results = new ArticleDecorator($results);
            $results = $results->results;
        }
        */
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}