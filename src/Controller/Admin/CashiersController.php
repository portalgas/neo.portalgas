<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CashiersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cashier');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(!$this->Auth->isCassiere($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }    

    public function deliveries()
    {   
        $delivery_id = '5378';

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
        debug($order_ids); 

        if(!empty($order_ids)) {

            $cartsTable = TableRegistry::get('Carts');

            $where = ['ArticlesOrders.order_id IN ' => $order_ids];
            $where = ['Carts.organization_id' => $this->user->organization_id,
                      'Carts.order_id IN ' => $order_ids];

            $cartResults = $cartsTable->find()
                                    ->contain(['Users']) // 'ArticlesOrders', 
                                    ->where($where)
                                    ->order(['Users.name'])
                                    ->all();
            debug($cartResults);        
        } // end if(!empty($order_ids))
        exit;

        $deliveries = $this->Cashier->getListDeliveries($this->user);
        
        $this->set(compact('deliveries'));                  
    }
}