<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Authentication\AuthenticationService;

class CartsController extends ApiAppController
{		
    public function initialize(): void 
    {
        parent::initialize();		
    }

    public function beforeFilter(Event $event): void 
    {
        // parent::beforeFilter($event);

        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $this->viewBuilder()->setClassName('Json'); 

        // $this->Authentication->allowUnauthenticated(['getsByOrder']);
    }

    /* 
     * url: /api/carts/getByOrder
     * front-end - estrae gli articoli associati ad un ordine filtrati per user  
     */
    public function getByOrder() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $where = [];
        $order = [];
   
        $order_id = $this->request->getData('order_id');
        
        $cartsTable = TableRegistry::get('Carts');
        $results = $cartsTable->getByOrder($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id, $this->Authentication->getIdentity()->id, $where, $order);            
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