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
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    /*
     * testing chiamata ajax
     */
    public function ajax()
    {
        $results = [];
        
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $service_urls = [];
        $service_urls['/admin/api/articles-orders/getCartsByOrder'] = '/admin/api/articles-orders/getCartsByOrder';

        $deliveriesTable = TableRegistry::get('Deliveries');
        $deliveries = $deliveriesTable->getsList($user, $user->organization->id);  
        
        $orderTable = TableRegistry::get('Orders');
        $orders = $orderTable->getsList($user, $user->organization->id, ['Orders.state_code IN ' => ['OPEN', 'PROCESSED-BEFORE-DELIVERY']]);  
    
        $this->set(compact('service_urls', 'deliveries', 'orders'));
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