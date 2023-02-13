<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class GasGroupDeliveriesController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        
        if(!$this->_user->acl['isGasGroupsManagerParentOrders'] ||
           !$this->_user->acl['isGasGroupsManagerOrders']) {
            $this->_respondWithUnauthorized();
        }         
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);       
    }
  
    /* 
     * elenco consegne del gruppo
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];

        $gas_group_id = $this->request->getData('gas_group_id');

        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
        $results = $gasGroupDeliveriesTable->getsActiveList($this->_user, $this->_organization->id, $gas_group_id);
        
        return $this->_response($results);
    } 

  
    /* 
     * front-end - estrae
     * le consegne legato al carrello dell'user
     * eventuali promozioni
     */
    public function userCartGets() {

        $debug = false;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;
        $user_id = $this->Authentication->getIdentity()->id;

        /*
         * elenco consegne partendo dagli acquisti dello user
         */
        $cartsTable = TableRegistry::get('Carts');

        $where = [];
        $where['Carts'] = ['Carts.user_id' => $user->id,
                            'Carts.organization_id' => $organization_id,
                            'Carts.deleteToReferent' => 'N'];        
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                               /*
                                * imposto + un limite di data al carrello
                                */
                                 'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesCartInTabs') . ' DAY ',
                                ];
        $where['Orders'] = ['Orders.state_code != ' => 'CREATE-INCOMPLETE'];
        // debug($where);
        $carts = $cartsTable->find()
                                    ->select(['Deliveries.id', 'Deliveries.data', 'Deliveries.luogo', 'Deliveries.sys'])
                                    ->contain(['Orders'  => ['fields' => ['Orders.id'], 'conditions' => $where['Orders'],
                                              'Deliveries' => ['fields' => ['Deliveries.id', 'Deliveries.data', 'Deliveries.luogo', 'Deliveries.sys'], 'conditions' => $where['Deliveries']]]])
                                    ->where($where['Carts'])
                                    ->group(['Carts.order_id'])
                                    ->order(['Deliveries.data' => 'desc'])
                                    ->all();
        // dd($carts);
        if($carts->count()>0) {
            $arr_delivery_ids = [];
            foreach($carts as $cart) {

                if(!array_key_exists($cart->order->delivery->id, $arr_delivery_ids)) {

                    if($cart->order->delivery->sys=='Y') {
                        /*
                         * key array non per id, nel json perde l'ordinamento della data
                         * $results[$cart->order->delivery->id] = $cart->order->delivery->luogo;
                         */
                        $results[] = ['id' => $cart->order->delivery->id, 'label' => $cart->order->delivery->luogo];
                    }
                    else {
                        /*
                         * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                         * key array non per id, nel json perde l'ordinamento della data
                         * $results[$cart->order->delivery->id] = $cart->order->delivery->data->i18nFormat('eeee d MMMM Y');
                         */
                        $results[] = ['id' => $cart->order->delivery->id, 'label' => $cart->order->delivery->data->i18nFormat('eeee d MMMM').' - '.$cart->order->delivery->luogo];
                    }

                    $arr_delivery_ids[$cart->order->delivery->id] = $cart->order->delivery->id;

                } // end if(!array_key_exists($arr_delivery_ids, $cart->order->delivery->id)

                // debug($arr_delivery_ids); 
            }
        }

        /*
         * promozioni
         */
        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN', 'PRODGASPROMOTION-GAS-USERS-CLOSE'];
        $prod_gas_promotion_organization_state_code = ['OPEN', 'CLOSE'];

        $prodGasPromotionsResults = $this->ProdGasPromotion->userCartGets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);
        // debug($prodGasPromotionsResults);
        if(!empty($prodGasPromotionsResults))
            $results[] = ['id' => 0, 'label' => __('ProdGasPromotions')];

        return $this->_response($results); 
    }    
}