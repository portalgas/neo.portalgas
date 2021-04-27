<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class DeliveriesController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * front-end - estrae le consegne anche senza ordine e l'eventuale "da definire" con ordini
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        /*
         * elenco consegne
         */
        $deliveriesTable = TableRegistry::get('Deliveries');

        $where = [];
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                                'Deliveries.stato_elaborazione' => 'OPEN',
                                'Deliveries.sys' => 'N',
                                'DATE(Deliveries.data) >= CURDATE()'];
        $where['Orders'] = ['Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];
        $deliveries = $deliveriesTable->gets($user, $organization_id, $where);
        if(!empty($deliveries)) {
            foreach($deliveries as $delivery) {
                /*
                 * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                 * key array non per id, nel json perde l'ordinamento della data
                 * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM Y');
                 */
                $results[] = ['id' => $delivery->id, 'label' => $delivery->data->i18nFormat('eeee d MMMM').' - '.$delivery->luogo];
            }
        }

        /*
         * ctrl se ci sono ordini con la consegna ancora da definire (Delivery.sys = Y)
         */
        $where = [];
        $where['Orders'] = ['Orders.organization_id' => $organization_id,
                            'Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];
        $sysDelivery = $deliveriesTable->getDeliverySys($user, $organization_id, $where);
        // debug($sysDelivery);
        
        
        if($sysDelivery->has('orders') && !empty($sysDelivery->orders)) {
            $results[] = ['id' => $sysDelivery->id, 'label' => $sysDelivery->luogo];
        }

        return $this->_response($results);
    } 

  
    /* 
     * front-end - estrae le consegne legato al carrello dell'user
     */
    public function userCartGets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

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
                                 'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesCartInTabs') . ' DAY ',
                                */
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
        // debug($carts);exit;
        if(!empty($carts)) {
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

        return $this->_response($results); 
    }    
}