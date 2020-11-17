<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticleDecorator;
use App\Decorator\ApiArticleOrderDecorator;

class DeliveriesController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Csrf');
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
                $results[$delivery->id] = $delivery->data->format('d F Y');
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
            $results[$sysDelivery->id] = $sysDelivery->luogo;
        }

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
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
                                'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesCartInTabs') . ' DAY '];
        $where['Orders'] = ['Orders.state_code != ' => 'CREATE-INCOMPLETE'];
        // debug($where);
        $carts = $cartsTable->find()
                                    ->select(['Deliveries.id', 'Deliveries.data', 'Deliveries.luogo', 'Deliveries.sys'])
                                    ->contain(['Orders'  => ['fields' => ['Orders.id'], 'conditions' => $where['Orders'],
                                              'Deliveries' => ['fields' => ['Deliveries.id', 'Deliveries.data', 'Deliveries.luogo', 'Deliveries.sys'], 'conditions' => $where['Deliveries']]]])
                                    ->where($where['Carts'])
                                    ->group(['Carts.order_id'])
                                    ->order(['Orders.id'])
                                    ->all();
        // debug($carts);exit;
        if(!empty($carts)) {
            foreach($carts as $cart) {
                if($cart->order->delivery->sys=='Y')
                    $results[$cart->order->delivery->id] = $cart->order->delivery->luogo;
                else
                    $results[$cart->order->delivery->id] = $cart->order->delivery->data->format('d F Y');
            }
        }

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }    
}