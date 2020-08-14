<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class PriceTypesController extends ApiAppController
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
    
    /*
     * POST /admin/api/priceTypes/getsByOrderId
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4....
     */  
    public function getsByOrderId() {

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        $order_id = $this->request->getData('order_id');
        if(!empty($order_id)) {

            $priceTypesTable = TableRegistry::get('PriceTypes'); 

            $where = ['PriceTypes.organization_id' => $this->Authentication->getIdentity()->organization->id,
                      'PriceTypes.order_id' => $order_id];    
            if($debug) debug($where);

            $priceTypeResults = $priceTypesTable->find()
                                    ->where($where)
                                    ->order(['PriceTypes.sort'])
                                    ->all();
            if($debug) debug($priceTypeResults);
            $results['results'] = $priceTypeResults;
        } // end if(!empty($order_id))

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}