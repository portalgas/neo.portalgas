<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CartsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cart');
        $this->loadComponent('CartProdGasPromotionGasUser');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    public function managementCart() {
        
        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
   
        $order = $this->request->getData('order');
        $article = $this->request->getData('article');
        // debug($article);
        $results = $this->Cart->managementCart($user, $user->organization->id, $order, $article, $debug);
        
        return $this->_response($results); 
    } 

    /* 
     * carrello per promozioni GAS-USERS
     */
    public function managementCartProdGasPromotionGasUser() {
        
        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
   
        $order = $this->request->getData('order');
        $article = $this->request->getData('article');
        $organization_id = $order['organization_id']; // organization_id del produttore
 
        $results = $this->CartProdGasPromotionGasUser->managementCart($user, $organization_id, $order, $article, $debug);
        
        return $this->_response($results); 
    } 

    /* 
     * estrae solo gli users che hanno effettuato acquisti in base alla consegna
     */
    public function getUsersByDelivery() {

        $debug = false;
        $results = [];
    
        $delivery_id = $this->request->getData('delivery_id');
        if(!empty($delivery_id)) {

            $options = [];
            $userResults = $this->Cart->getUsersByDelivery($this->Authentication->getIdentity(), $delivery_id, $options, $debug);

            if(!empty($userResults)) {
                /*
                 * il recordset e' object(App\Model\Entity\Cart) 
                 *    'user' => object(App\Model\Entity\User) => js user.user.name!!
                 */
                foreach($userResults as $numResult => $userResult) {
                    $results[$numResult] = $userResult->user;
                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        return $this->_response($results); 
    } 

    /* 
     * estrae solo gli users + cassa che hanno effettuato acquisti in base alla consegna
     */
    public function getUsersCashByDelivery() {

        $debug = false;
        $results = [];

        $delivery_id = $this->request->getData('delivery_id');
        
        if(!empty($delivery_id)) {

            $cashesTable = TableRegistry::get('Cashes');
            
            $options = [];
            $userResults = $this->Cart->getUsersByDelivery($this->Authentication->getIdentity(), $delivery_id, $options, $debug);

            if(!empty($userResults)) {
                /*
                 * il recordset e' object(App\Model\Entity\Cart) 
                 *    'user' => object(App\Model\Entity\User) => js user.user.name!!
                 */
                foreach($userResults as $numResult => $userResult) {
                    $results[$numResult] = $userResult->user;

                    /*
                     * associo la cassa
                     */
                    $cashResults = $cashesTable->getByUser($this->Authentication->getIdentity(), $userResult->user->organization->id, $userResult->user->id, $options, $debug);                    
                    $results[$numResult]['cash'] = $cashResults;
                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        return $this->_response($results);  
    }  

    /* 
     * url: /admin/api/carts/getByOrder
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
            // $results = new ApiArticleOrderDecorator($results);
            $results = new ArticleDecorator($results);
            $results = $results->results;
        }
        */
        
        return $this->_response($results); 
    }       
}