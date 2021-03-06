<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class RegionsController extends ApiAppController
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

        $regions = Cache::read('regions');
        if ($regions !== false) {
            $results['results'] =  $regions;
        }
        else {
            $geoRegionsTable = TableRegistry::get('GeoRegions'); 

            $geoRegionsResults = $geoRegionsTable->find('list')
                                    ->order(['name' => 'asc'])
                                    ->all();

            Cache::write('regions', $geoRegionsResults);

            $results['results'] = $geoRegionsResults;
        }
        
        return $this->_response($results);
    } 
}