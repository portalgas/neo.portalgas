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
     * simula chiamata ajax 
     */
    public function queue() {
        
        $debug = true;

        $request['code'] = 'GDXP-PORTALGAS';
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