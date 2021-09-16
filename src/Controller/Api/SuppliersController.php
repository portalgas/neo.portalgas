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

        $this->Authentication->allowUnauthenticated(['get', 'prodGasSupplierGets', 'getBySlug', 'gets']); 
    }
    
    /*
     * dati del produttore
     * 
     * POST /api/suppliers/get
     */
    public function get() {

        $debug = false;
        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /*
         * null se non autenticato
         */
        $user = $this->Authentication->getIdentity();

        $supplier_id = $this->request->getData('supplier_id');
        if(empty($supplier_id)) {
            $results['code'] = 500;
            $results['message'] = 'Parametro supplier_id richiesto';
            $results['errors'] = '';
            $continua = false;
        }

        if($continua) {
            $suppliersTable = TableRegistry::get('Suppliers'); 

            $where = [];
            $where['Suppliers'] = ['Suppliers.stato' => 'Y'];
            $suppliersResult = $suppliersTable->getById($user, $supplier_id, $where, $debug);

            if(empty($suppliersResult)) {
                $results['code'] = 500;
                $results['message'] = 'Produttore non trovato con id ['.$supplier_id.']';
                $results['errors'] = '';
                $continua = false;
            }
        } // end if($continua)

        if($continua) {
            $supplier_id = $suppliersResult->id;

            $suppliersResult = new ApiSupplierDecorator($user, $suppliersResult);
            $results['results'] = $suppliersResult->results;

            $results['results']['articles'] = $this->Supplier->getArticles($user, $supplier_id);
        } // end if($continua)

        return $this->_response($results);
    } 

    
    /*
     * dati del produttore cercati per slug
     * 
     * POST /api/suppliers/getBySlug
     */
    public function getBySlug() {

        $debug = false;
        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /*
         * null se non autenticato
         */
        $user = $this->Authentication->getIdentity();

        $slug = $this->request->getData('slug');
        if(empty($slug)) {
            $results['code'] = 500;
            $results['message'] = 'Parametro slug richiesto';
            $results['errors'] = '';
            $continua = false;
        }

        if($continua) {
            $suppliersTable = TableRegistry::get('Suppliers'); 

            $suppliersResult = $suppliersTable->getBySlug($user, $slug, $debug);

            if(empty($suppliersResult)) {
                $results['code'] = 500;
                $results['message'] = 'Produttore non trovato con id ['.$supplier_id.']';
                $results['errors'] = '';
                $continua = false;
            }
        } // end if($continua)

        if($continua) {
            $supplier_id = $suppliersResult->id;

            $suppliersResult = new ApiSupplierDecorator($user, $suppliersResult);
            $results['results'] = $suppliersResult->results;

            $results['results']['articles'] = $this->Supplier->getArticles($user, $supplier_id);
        } // end if($continua)

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
         * elenco produttori publici
         * se autenticato posso importarlo
         */ 
        $user = $this->Authentication->getIdentity();
        // $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        /* 
         * estraggo i Suppliers
         */
        $suppliersTable = TableRegistry::get('Suppliers'); 

        $where = []; 

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
        
        if(empty($where)) // non e' stata effettuata alcuna ricerca
            $order = ['Suppliers.voto' => 'desc'];
        else
            $order = ['Suppliers.name' => 'asc'];   
        $where += ['Suppliers.stato' => 'Y'];

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
                ->select(['name', 'id', 'descrizione', 'slug', 'voto', 'indirizzo', 'localita', 'cap', 'provincia', 'lat', 'lng', 'telefono', 'telefono2', 'fax', 'mail', 'www', 'nota', 'piva', 'img1', 
                      'relevance' => 'MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'])
                ->contain(['CategoriesSuppliers'])
                ->where($where)
                ->limit($limit)
                ->page($page)
                ->order(['relevance' => 'desc'])
                ->toArray();
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
                ->order($order)
                ->toArray();    
        }

        if(!empty($suppliersResults)) {

            /*
             * Decorator
             */             
            $suppliersResults = new ApiSupplierDecorator($user, $suppliersResults);
            $results['results'] = $suppliersResults->results;
        }
  
        return $this->_response($results);
    } 


    /*
     * lista di tutti i produttori che gestiscono il listino 
     * prodGasSuppliers: OrganizationsProdGasTable
     * 
     * POST /api/suppliers/produttoriGets
     */ 
    public function prodGasSupplierGets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /*
         * elenco produttori publici
         * se autenticato posso importarlo
         */ 
        $user = $this->Authentication->getIdentity();
        // $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        /* 
         * estraggo i Suppliers
         */
        $organizationsProdGasTable = TableRegistry::get('OrganizationsProdGas'); 

        $where = []; 
        $where['Organizations'] = ['OrganizationsProdGas.stato' => 'Y']; 
        $where['Suppliers'] = [];
        $where['SuppliersOrganizations'] = [];

        /*
         * tratto solo i produttori che gestiscono il listino
         */
        $where['Suppliers'] += ['Suppliers.owner_organization_id != ' => 0];
        $where['Suppliers'] += ['Suppliers.stato' => 'Y'];
        
        $category_id = $this->request->getData('category_id');
        if(!empty($category_id)) 
            $where['Suppliers'] += ['Suppliers.category_supplier_id' => $category_id];
        $region_id = $this->request->getData('region_id');
        if(!empty($region_id)) {
            /*
             * suppliers non ha il campo region_id
             */
            $provincesTable = TableRegistry::get('GeoProvinces');
            $provinces = $provincesTable->getSiglaByIdGeoRegion($region_id);

            $where['Suppliers'] += ['Suppliers.provincia IN ' => $provinces];
        }
        $province_id = $this->request->getData('province_id');
        if(!empty($province_id)) 
            $where['Suppliers'] += ['Suppliers.provincia' => $province_id];
        
        if(empty($where)) // non e' stata effettuata alcuna ricerca
            $order = ['Suppliers.voto' => 'desc'];
        else
            $order = ['Suppliers.name' => 'asc'];   

        $page = $this->request->getData('page');
        if(empty($page)) $page = '1';
        $limit = Configure::read('sql.limit');
        $sort = ['Suppliers.name']; 

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
            
            $where['Suppliers'] += ['MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'];

            $suppliersTable = TableRegistry::get('Suppliers'); 

            $suppliersResults = $suppliersTable->find() 
                ->select(['name', 'id', 'descrizione', 'slug', 'voto', 'indirizzo', 'localita', 'cap', 'provincia', 'lat', 'lng', 'telefono', 'telefono2', 'fax', 'mail', 'www', 'nota', 'piva', 'img1', 
                      'relevance' => 'MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'])
                ->contain(['CategoriesSuppliers'])
                ->where($where['Suppliers'])
                ->limit($limit)
                ->page($page)
                ->order(['relevance' => 'desc'])
                ->toArray();
                // ->bind(':search', $search, 'string')
                // ->bind(':search', $search, 'string')                                         
        }
        else {
            /*
             * ricerca senza nome
             */ 
            $options = [];
            $options['page'] = $page;
            $options['q'] = $q;
            $options['sort'] = $sort;
            $options['sql_limit'] = Configure::read('sql.limit');

            $suppliersResults = [];
            $organizationsProdGas = $organizationsProdGasTable->gets($user, $where, $options);
            foreach($organizationsProdGas as $organizationsProdGa) {
                $suppliersResults[] = $organizationsProdGa->supplier;
            }        
        }

        if(!empty($suppliersResults)) {
            $suppliersResults = new ApiSupplierDecorator($user, $suppliersResults);
            $results['results'] = $suppliersResults->results;
        }
  
        return $this->_response($results);
    } 
}