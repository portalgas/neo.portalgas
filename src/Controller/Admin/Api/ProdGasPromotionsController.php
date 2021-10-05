<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ProdGasPromotionsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('ProdGasPromotion');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
    
    /*
     * POST /admin/api/promotions/gets
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4....
     * elenco promozioni / articoli / eventuali acquisti
     */  
    public function gets() {

        $debug = false;

        $newResults = [];

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        $organization_id = $this->Authentication->getIdentity()->organization->id;
        $user_id = $this->Authentication->getIdentity()->id;
        $user = $this->Authentication->getIdentity();

        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN'];
        $prod_gas_promotion_organization_state_code = ['OPEN'];

        $results = $this->ProdGasPromotion->gets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);

        return $this->_response($results); 
     }

    /*
     * POST /admin/api/promotions/user-cart-gets
     * da stampa carrello   
     * front-end - estrae le promozioni legato al carrello dell'user
     * order_id = prod_gas_promotion_id per le promozioni GAS-USERS     
     */  
    public function userCartGets() {

        $newResults = [];

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        $organization_id = $this->Authentication->getIdentity()->organization->id;
        $user_id = $this->Authentication->getIdentity()->id;
        $user = $this->Authentication->getIdentity();

        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN', 'PRODGASPROMOTION-GAS-USERS-CLOSE'];
        $prod_gas_promotion_organization_state_code = ['OPEN', 'CLOSE'];

        $results = $this->ProdGasPromotion->userCartGets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);

        return $this->_response($results); 
    } 
}