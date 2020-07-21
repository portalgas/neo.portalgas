<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class QueuesController extends ApiAppController
{   
    public function initialize()
    {
        parent::initialize();        

        if(isset($this->Authentication))
            $this->Authentication->allowUnauthenticated(['queue', 'queues']);
            $this->Authorization->skipAuthorization(['queue', 'queues']);
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);   
   
    }
    
    /*
     * POST /admin/api/queues/queue
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4e6e05e34b94c124977b48663d8b7ff838b13726a
     */
    public function queue() {

        if (!$this->request->is('post')) {
            $this->_respondWithMethodNotAllowed();
            return;
        }  
        
        $request = $this->request->getData();
        if(!$this->_validateRequest($request)) {
            $this->_respondWithBadRequest();
            return;
        } 

        /*
         * queue
         */
        $queuesCode = $request['code'];
        $queuesTable = TableRegistry::get('Queues');
        $queue = $queuesTable->findByCode($queuesCode);  

        // $this->loadComponent($queue->component);  // custom QueueDweeMago
        $this->loadComponent($queue->queue_mapping_type->component); // QueueDatabase QueueXml QueueCsv

        $results = $this->{$queue->queue_mapping_type->component}->queue($request);

        $this->set([
            'esito' => $results['esito'],
            'code' => $results['code'],
            'uuid' => $results['uuid'],
            'msg' => $results['msg'],
            'results' => $results['results']
        ]); 

        $this->set('_serialize', ['esito', 'code', 'uuid', 'msg', 'results']);         
    }

    /*
     * POST /admin/api/queues/queues
     * https://connect.atlanticmoon.it/admin/api/queues/queues
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4e6e05e34b94c124977b48663d8b7ff838b13726a
     *  id xlsx
     */
    public function queues() {

        if (!$this->request->is('post')) {
            $this->_respondWithMethodNotAllowed();
            return;
        }  
        
        $request = $this->request->getData();
        if(!$this->_validateRequest($request)) {
            $this->_respondWithBadRequest();
            return;
        } 

        $extension = $request['id'];

        /*
         * queue
         */
        $queuesCode = $request['code'];
        $queuesTable = TableRegistry::get('Queues');
        $queue = $queuesTable->findByCode($queuesCode);  

        // $this->loadComponent($queue->component);  // custom QueueDweeMago
        $this->loadComponent($queue->queue_mapping_type->component); // QueueRemoteXml 

        $sources = $this->{$queue->queue_mapping_type->component}->getSources($queue, $request);
        if($sources) {
            foreach($sources as $source) {
                /* 
                 * per remote-ftp, id = path locale del file
                 * ex /var/www/.../backup_files/krca/test.xlsx
                */            
               $request['id'] = $source; 
               $results = $this->{$queue->queue_mapping_type->component}->queue($request);

               if($results['esito'])
                    $this->{$queue->queue_mapping_type->component}->setSources($queue->code, $source);
            }
        }
        else {
            $results['esito'] = false;
            $results['code'] = 404;
            $results['uuid'] = '';
            $results['msg'] = 'Nessuna sorgente dati trovata';
            $results['results'] = '';
        }

        $this->set([
            'esito' => $results['esito'],
            'code' => $results['code'],
            'uuid' => $results['uuid'],
            'msg' => $results['msg'],
            'results' => $results['results']
        ]); 

        $this->set('_serialize', ['esito', 'code', 'uuid', 'msg', 'results']);         
    }

    private function _validateRequest($request) {
        if(!isset($request['code']) || empty($request['code'])) {
            return false;
        } 
        if(!isset($request['id']) || empty($request['id'])) {
           return false;
        }   

        return true;       
    }    
}