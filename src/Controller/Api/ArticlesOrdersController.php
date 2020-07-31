<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticleDecorator;
use App\Decorator\ApiArticleDecorator;

class ArticlesOrdersController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user  
     */
    public function getCartsByOrder() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $where = [];
        $order = [];
   
        $order_id = $this->request->getData('order_id');
        // debug($order_id);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $results = $articlesOrdersTable->getCartsByOrder($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id, $this->Authentication->getIdentity()->id, $where, $order);
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

    /* 
     * non ancora utilizzata  
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $where = [];
        $order = [];
   
        $order_id = $this->request->getData('order_id');
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id);

        if($articlesOrdersTable!==false) {
            $results = $articlesOrdersTable->gets($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id, $where, $order);            
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