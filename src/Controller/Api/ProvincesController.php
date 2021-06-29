<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ProvincesController extends ApiAppController
{
    public function initialize(): void 
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void {
     
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets']); 
    }
    
    /*
     * lista di tutte le regioni
     * 
     * POST /api/regions/gets
     */  
    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $geo_region_id = $this->request->getData('region_id');
        if($geo_region_id=='') 
            $geo_region_id = 0;

        $geoProvincesTable = TableRegistry::get('GeoProvinces'); 

        $geoProvincesResults = $geoProvincesTable->getList($geo_region_id, $debug);

        $results['results'] = $geoProvincesResults;
        
        return $this->_response($results);
    } 
}