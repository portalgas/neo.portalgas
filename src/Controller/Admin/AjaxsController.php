<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AjaxsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || !isset($this->Authentication->getIdentity()->acl)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $this->viewBuilder()->setLayout('ajax');
    }
	
    public function ViewOrderDetails() {
        $order_id = $this->request->data('order_id');
        
        $ordersTable = TableRegistry::get('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id);
        $delivery_id = $order->delivery_id;
        
        $this->set(compact('delivery_id', 'order_id', 'order'));
    }
}