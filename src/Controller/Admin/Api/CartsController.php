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
  
    public function managementCart($is_public=false) {
        
        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        ($is_public) ? $organization_id = Configure::read('public_organization_id'): $organization_id = $user->organization->id;

        $results = [];
   
        $order = $this->request->getData('order');
        $article = $this->request->getData('article');
        // debug($article);
        $results = $this->Cart->managementCart($user, $organization_id, $order, $article, $debug);
        
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
     * url: /admin/api/carts/setNota
     * front-end - salva la nota per il referente
     */
    public function setNota() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $results['esito'] = true;
        $results['msg'] = '';
        $results['results'] = [];

        $user = $this->Authentication->getIdentity();
        $user_id = $user->id;
        $organization_id = $user->organization->id;

        $order_id = $this->request->getData('order_id');
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = $this->request->getData('article_id');
        $nota = $this->request->getData('nota');

        if(empty($user_id) || empty($organization_id) || empty($order_id) || empty($article_organization_id) || empty($article_id)) {
            $results['esito'] = false;
            $results['msg'] = 'Errore nel salvataggio della nota';
            $results['results'] = [];

            return $this->_response($results); 
        }

        $cartsTable = TableRegistry::get('Carts');
        $esito = $cartsTable->setNota($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $nota);
        if($esito===false) {
            $results['esito'] = false;
            $results['msg'] = "L'articolo non è stato ancora acquistato!";
            $results['results'] = [];            
        } 
        else 
        if($esito===true) {
            $results['esito'] = true;
            $results['msg'] = 'Nota al referente salvata';
            $results['results'] = [];            
        }     
        else {
            $results['esito'] = false;
            $results['msg'] = $esito;
            $results['results'] = $esito;            
        } 

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

    /*
     * url: /admin/api/carts/getTotImportByOrderId
     * front-end - estrae il totale importo del carrello di un ordine filtrati per user
     */
    public function getTotImportByOrderId($is_public=false) {

        $debug = false;
        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $order_id = $this->request->getData('order_id');
        if(empty($order_id)) {
            $results['code'] = 500;
            $results['message'] = 'Parametro order_id richiesto';
            $results['errors'] = '';
            $continua = false;
        }

        if($continua) {
            $cartsTable = TableRegistry::get('Carts');
            $user = $this->Authentication->getIdentity();

            ($is_public) ? $organization_id = Configure::read('public_organization_id'): $organization_id = $user->organization->id;

            $where = [];
            $where = ['Carts.order_id' => $order_id,
                      'Carts.user_id' => $user->id];
            $tot_importo = $cartsTable->getTotImporto($user, $organization_id, $where);

            $results['results'] = $tot_importo;
        }

        return $this->_response($results);
    }
}