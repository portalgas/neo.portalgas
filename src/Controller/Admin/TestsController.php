<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class TestsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('MappingGdxpPortalgas');
        $this->loadComponent('SuppliersOrganization');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    /*
     * testing chiamata ajax
     */
    public function ajax()
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $order_type_ids = [];
        $order_type_ids[Configure::read('Order.type.gas')] = 'Order.type.gas';
        $order_type_ids[Configure::read('Order.type.des')] = 'Order.type.des';
        $order_type_ids[Configure::read('Order.type.-titolare')] = 'Order.type.-titolare'; 
        $order_type_ids[Configure::read('Order.type.promotion')] = 'Order.type.promotion';
        $order_type_ids[Configure::read('Order.type.pact-pre')] = 'Order.type.pact-pre';
        $order_type_ids[Configure::read('Order.type.pact')] = 'Order.type.pact';
        $order_type_ids[Configure::read('Order.type.supplier')] = 'Order.type.supplier';

        $order_type_id = 0;
        $order_type_id = $this->request->getData('order_type_id');
        $this->set(compact('order_type_ids', 'order_type_id'));

        if(!empty($order_type_id)) {

            $results = [];
        
            $service_urls = [];
            $service_urls['/admin/api/orders-gas/getCartsByOrder'] = '/admin/api/orders-gas/getCartsByOrder';

            $ordersTable = TableRegistry::get('Orders');
            $ordersTable = $ordersTable->factory($user, $organization_id, $order_type_id);
            $ordersTable->addBehavior('Orders');

            $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($user, $organization_id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $suppliersOrganizations);

            $deliveries = $ordersTable->getDeliveries($user, $organization_id);
            
            $where = [$ordersTable->getAlias().'.state_code IN ' => ['OPEN', 'PROCESSED-BEFORE-DELIVERY']];
            $orders = $ordersTable->getsList($user, $organization_id, $where);  
        
            $this->set(compact('service_urls', 'suppliersOrganizations', 'deliveries', 'orders'));
        } // end if(!empty($order_type_id))
    }

    /*
     * simula chiamata ajax delle queue
     */
    public function queue() {
        
        $debug = true;

        $request['code'] = Configure::read('Gdxp.queue.code');
        $request['id'] = '';
		
        $queuesTable = TableRegistry::get('Queues');
        $queue = $queuesTable->findByCode($request['code']);  

        $this->loadComponent($queue->queue_mapping_type->component);

        $results = $this->{$queue->queue_mapping_type->component}->queue($request, $debug);

        $this->set(compact('results'));
        $this->render('index');
    }

    public function index()
    {
        $results = [];
       
		$organization_id = 1;
		$results = $this->MappingGdxpPortalgas->getMaxArticleId($organization_id);
		
        $this->set(compact('results'));
    }
}