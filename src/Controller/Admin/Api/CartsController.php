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
        $this->loadComponent('Csrf');
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * estrae solo gli users che hanno effettuato acquisti in base alla consegna
     */
    public function getUsersByDelivery() {

        $results = [];
    
        $delivery_id = $this->request->getData('delivery_id');

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.delivery_id' => $delivery_id];
        if(!empty($orders_state_code))
            $where = ['Orders.state_code' => $orders_state_code];

        $orderResults = $ordersTable->find()
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();
        $order_ids = [];
        if(!empty($orderResults)) {
            foreach($orderResults as $orderResult) {
                $order_ids[] = $orderResult->id;
            }
        }

        if(!empty($order_ids)) {

            $cartsTable = TableRegistry::get('Carts');

            $where = ['ArticlesOrders.order_id IN ' => $order_ids];

            $cartResults = $cartsTable->find()
                                    ->contain(['ArticlesOrders', 'Users'])
                                    ->where($where)
                                    ->order(['Users.name'])
                                    ->all();
            debug($cartResults);        
        } // end if(!empty($order_ids))            
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        
        return $this->response; 
    } 
}