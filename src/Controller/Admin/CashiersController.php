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
        $deliveries = $this->Cashier->getListDeliveries($this->user);
        
        $this->set(compact('deliveries'));                  
    }
}