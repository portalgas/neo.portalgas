<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ApiSupplierDecorator;

class SuppliersController extends ApiAppController
{
    use Traits\SqlTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Supplier');
    }

    public function beforeFilter(Event $event): void {
     
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['get', 'gets']); 
    }
    
    /*
     * dati del produttore
     * 
     * POST /api/suppliers/get
     */
    public function get() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        $user = $this->Authentication->getIdentity();

        $supplier_id = $this->request->getData('supplier_id');

        $suppliersTable = TableRegistry::get('Suppliers'); 

        $where = [];
        $where['Suppliers'] = ['Suppliers.stato' => 'Y'];
        $suppliersResult = $suppliersTable->getById($user, $supplier_id, $where, $debug);

        if(!empty($suppliersResult)) {

            $supplier_id = $suppliersResult->id;

            $suppliersResult = new ApiSupplierDecorator($suppliersResult);
            $results['results'] = $suppliersResult->results;

            $results['results']['articles'] = $this->Supplier->getArticles($user, $supplier_id);
        }

        return $this->_response($results);
    } 

    /*
     * lista di tutti i produttori
     * 
     * POST /api/suppliers/gets
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
        $suppliersTable = TableRegistry::get('Suppliers'); 

        $where = ['Suppliers.stato' => 'Y'];

        $category_id = $this->request->getData('category_id');
        if(!empty($category_id)) 
            $where += ['Suppliers.category_supplier_id' => $category_id];
        $region_id = $this->request->getData('region_id');
        if(!empty($region_id)) {
            /*
             * suppliers non ha il campo region_id
             */
            $provincesTable = TableRegistry::get('GeoProvinces');
            $provinces = $provincesTable->getSiglaByIdGeoRegion($region_id);

            $where += ['Suppliers.provincia IN ' => $provinces];
        }
        $province_id = $this->request->getData('province_id');
        if(!empty($province_id)) 
            $where += ['Suppliers.provincia' => $province_id];
        
        $page = $this->request->getData('page');
        if(empty($page)) $page = '1';
        $limit = Configure::read('sql.limit');
        
        $q = $this->request->getData('q');
        if(!empty($q)) {

            $q = $this->SQLinjection($q);

            /*
             * ricerca per nome
             */ 

            $search = '';
        
            // debug($q);
            if(strpos($q, ' ')!==false) {
                $searchs = explode(' ', $q);
                // debug($searchs);
                foreach($searchs as $s) {
                    $search .= "'$s*' ";
                }
            }
            else
                $search = "'$q*'";
            // debug($search);

            $where += ['MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'];

            $suppliersResults = $suppliersTable->find() 
                ->select(['name', 'id', 'descrizione', 'indirizzo', 'localita', 'cap', 'provincia', 'lat', 'lng', 'telefono', 'telefono2', 'fax', 'mail', 'www', 'nota', 'piva', 'img1', 
                      'relevance' => 'MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'])
                ->contain(['CategoriesSuppliers'])
                ->where($where)
                ->limit($limit)
                ->page($page)
                ->order(['relevance' => 'desc']);
                // ->bind(':search', $search, 'string')
                // ->bind(':search', $search, 'string')                        
        }
        else {
            /*
             * ricerca senza nome
             */ 
            $suppliersResults = $suppliersTable->find() 
                ->contain(['CategoriesSuppliers'])
                ->where($where)
                ->limit($limit)
                ->page($page)
                ->order(['Suppliers.name' => 'asc']);            
        }


        if(!empty($suppliersResults)) {
            $suppliersResults = new ApiSupplierDecorator($suppliersResults);
            $results['results'] = $suppliersResults->results;
        }
  
        return $this->_response($results);
    } 
}