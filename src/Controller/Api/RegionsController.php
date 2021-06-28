<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class SuppliersController extends ApiAppController
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
    public function get() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /* 
         * estraggo i Suppliers
         */
        $suppliersTable = TableRegistry::get('Regions'); 
   
        $where_organizations = ['Organizations.stato' => 'Y', 'Organizations.type IN' => ['GAS']]; 
        $where_suppliers_organizations = ['SuppliersOrganizations.stato' => 'Y']; 
        $where_suppliers = ['Suppliers.stato' => 'Y'];

        $suppliersResults = $suppliersTable->find()
                                ->contain(['Content',
                                            'SuppliersOrganizations' => 
                                              ['conditions' => $where_suppliers_organizations, 'Organizations' => ['conditions' => $where_organizations]],
                                ])
                                ->where($where_suppliers) 
                                ->order(['name' => 'asc'])
                                ->limit(10)
                                ->all();

        $results['results'] = $suppliersResults;
        
        return $this->_response($results);
    } 

    /*
     * dati del produttore
     * 
     * POST /api/suppliers/get
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
        
        $q = $this->request->getData('q');
        if(!empty($q)) {
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
                ->select(['name', 'id', 
                      'relevance' => 'MATCH(Suppliers.name) AGAINST('.$search.' IN BOOLEAN MODE)'])
                ->where($where)
                ->order(['relevance' => 'desc']);
                // ->bind(':search', $search, 'string')
                // ->bind(':search', $search, 'string')                        
        }
        else {
            /*
             * ricerca senza nome
             */ 
            $suppliersResults = $suppliersTable->find() 
                ->select(['name', 'id'])
                ->where($where)
                ->order(['name' => 'asc']);            
        }

        $results['results'] = $suppliersResults;
  
        return $this->_response($results);
    } 

    /*
     * market e i suoi articoli
     *
     * GET /api/social-market/getArticles/:market_id
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4....
     */  
    public function getArticles() {

        $debug = false;

        $market_id = $this->request->getParam('market_id');

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        /* 
         * estraggo il Markets
         */
        $marketsTable = TableRegistry::get('Markets'); 

        $where_organizations = ['Organizations.stato' => 'Y', 'Organizations.type IN' => ['PRODGAS', 'PROD']]; 
        $where_market = ['Markets.id' => $market_id, 'Markets.state_code' => 'OPEN', 'Markets.is_active' => true]; // data_inizio / data_fine

        $marketsResults = $marketsTable->find()
                                ->contain([
                                    'Organizations' => [
                                        'conditions' => $where_organizations, 
                                        'SuppliersOrganizations' => ['Suppliers']],
                                    'MarketArticles' => ['Articles' => ['conditions' => ['Articles.stato' => 'Y']]]
                                ])
                                ->where($where_market)
                                ->first();

        $results['results'] = $marketsResults;
        
        return $this->_response($results);
    } 

}