<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;

class ProdGasPromotionsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('ProdGasPromotion');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
    
    /*
     * POST /admin/api/promotions/gets
     * Content-Type: application/json
     * X-Requested-With: XMLHttpRequest
     * Authorization: Bearer 5056b8cf17f6dea5a65018f4....
     */  
    public function gets() {

        $debug = false;

        $newResults = [];

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
    
        $organization_id = $this->Authentication->getIdentity()->organization->id;
        $user_id = $this->Authentication->getIdentity()->id;
        $user = $this->Authentication->getIdentity();

        $order = $this->ProdGasPromotion->getOrderDefault($user, $debug);

        /* 
         * estraggo le promozioni legate al GAS dello user
         */
        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations'); 

        $where = ['ProdGasPromotionsOrganizations.organization_id' => $organization_id, 
                  'ProdGasPromotionsOrganizations.order_id' => Configure::read('OrderIdPromotionGasUsers')];    
        $prodGasPromotionsOrganizationsResults = $prodGasPromotionsOrganizationsTable->find()
                                ->contain(['ProdGasPromotions' =>
                                     ['conditions' => ['ProdGasPromotions.type' => 'GAS-USERS', 'ProdGasPromotions.state_code' => 'OPEN']]])
                                ->where($where)
                                ->all();
        if($prodGasPromotionsOrganizationsResults->count()>0) {
            
            $prodGasPromotionsTable = TableRegistry::get('ProdGasPromotions'); 
  
            foreach($prodGasPromotionsOrganizationsResults as $numResult => $prodGasPromotionsOrganizationsResult) {

                $where = ['ProdGasPromotions.id' => $prodGasPromotionsOrganizationsResult->prod_gas_promotion_id,
                          'ProdGasPromotions.type' => 'GAS-USERS',
                          'ProdGasPromotions.state_code' => 'OPEN'];    
                if($debug) debug($where);

                $where_org = ['Organizations.type' => 'PRODGAS', 'Organizations.stato' => 'Y'];

                /*
                 * dati promozione / produttore
                 */
                $prodGasPromotionsResults = $prodGasPromotionsTable->find()
                                        ->contain(['Organizations' => ['conditions' =>  $where_org, 'SuppliersOrganizations' => ['Suppliers']]])
                                        ->where($where)
                                        ->order(['ProdGasPromotions.data_inizio'])
                                        ->first();
        
                $articlesOrdersPromotionTable = TableRegistry::get('ArticlesOrdersPromotion');
                   
                $where = [];
                $where['order_id'] = $prodGasPromotionsOrganizationsResult->order_id;

                $where['ProdGasArticlesPromotions'] = ['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prodGasPromotionsOrganizationsResult->prod_gas_promotion_id];

                $options = [];
                $options['sort'] = [];
                $options['limit'] = Configure::read('sql.no.limit');
                $options['page'] = 1;

                $articlesOrdersResults = $articlesOrdersPromotionTable->getCarts($user, $organization_id, $user_id, $order, $where, $options);

                $ii=0;
                $newResults2 = [];
                foreach($articlesOrdersResults as  $numResult2 => $articlesOrdersResult) { 
                    $articlesOrdersResult = new ApiArticleOrderDecorator($user, $articlesOrdersResult, $order); 
                    $newResults2[$ii] = $articlesOrdersResult->results;
                    $ii++;
                }

                $newResults[$numResult]['promotion'] = $prodGasPromotionsResults;
                $newResults[$numResult]['order'] = (array)$order;
                $newResults[$numResult]['article_orders'] = $newResults2;
            } // end foreach($prodGasPromotionsOrganizationsResults as $numResult => $prodGasPromotionsOrganizationsResult)
        } // end if($prodGasPromotionsOrganizationsResults->count()>0)
        $results['results'] = $newResults;

        return $this->_response($results);
    } 
}