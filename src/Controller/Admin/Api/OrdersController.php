<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class OrdersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    public function gets() {

        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id];

        $results = $ordersTable->find()
                                ->contain(['SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    /* 
     * aggiornamento produttori per gestione chi e' escluso dal prepagato
     */
    public function getByDelivery() {

        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');
        $orders_state_code = $this->request->getData('orders_state_code');

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id];
        if(!empty($orders_state_code))
            $where += ['Orders.state_code' => $orders_state_code];

        $results = $ordersTable->find()
                                ->contain(['SuppliersOrganizations' => ['Suppliers']])
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}