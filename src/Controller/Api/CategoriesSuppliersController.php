<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CategoriesSuppliersController extends ApiAppController
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
     * lista di tutte le categorie dei produttori
     * 
     * POST /api/categories-suppliers/gets
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4....
     */  
    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /* 
         * estraggo i Suppliers
         */
        $categoriesSuppliersTable = TableRegistry::get('CategoriesSuppliers'); 

        $categoriesSuppliersResults = $categoriesSuppliersTable->find('treeList', ['spacer' => '   ', 'order' => ['CategoriesSuppliers.name' => 'asc']]);

        $results['results'] = $categoriesSuppliersResults;

        /*
         * il json viene ordinato per id
         * debug($categoriesSuppliersResults->toArray());
         */
        return $this->_response($results);
    } 
}