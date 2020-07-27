<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

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

        $this->Authentication->allowUnauthenticated(['getsByOrder']);
    }
  
    /* 
     * elenco artcicoli associati ad un ordine
     */
    public function getsByOrder() {

        debug($this->Authentication->getIdentity());
        
        $results = [];
   
        $order_id = $this->request->getData('order_id');
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id);

        $results = $articlesOrdersTable->gets($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id);
        
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}