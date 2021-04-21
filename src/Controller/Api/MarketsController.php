<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class MarketsController extends ApiAppController
{
    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Market');
    }

    public function beforeFilter(Event $event): void {
     
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets', 'getArticles']); 
    }
    
    /*
     * lista di tutti i market
     * 
     * POST /api/social-market/gets
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
         * estraggo i Markets
         */
        $marketsTable = TableRegistry::get('Markets'); 
   
        $where_organizations = ['Organizations.stato' => 'Y', 'Organizations.type IN' => ['PRODGAS', 'PROD']]; 
        $where_market = ['Markets.state_code' => 'OPEN', 'Markets.is_active' => true]; // data_inizio / data_fine

        $marketsResults = $marketsTable->find()
                                ->contain([
                                    'Organizations' => [
                                        'conditions' => $where_organizations, 
                                        'SuppliersOrganizations' => ['Suppliers']],
                                ])
                                ->where($where_market) 
                                ->order(['sort' => 'asc'])
                                ->all();

        $results['results'] = $marketsResults;
        
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