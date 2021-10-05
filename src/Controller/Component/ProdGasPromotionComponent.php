<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use App\Decorator\ApiProdGasArticlesPromotionDecorator;

class ProdGasPromotionComponent extends Component {

	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = []) {
	}

	/*
	 * organization_id del produttore
	 *
	 * order_id = $prod_gas_promotion_id
	 * order_id necessario perche' key carts / articles_orders
	 * poi in CartProdGasPromotionGasUserComponent disabilito buildRules per $rules->existsIn(['organization_id', 'order_id'], 'Orders')
	 */
	public function getOrderDefault($user, $organization_id, $prod_gas_promotion_id, $debug=false) {
		
        $order = new \stdClass();
        $order->order_state_code = new \stdClass();
        $order->order_type = new \stdClass();

        $order->organization_id = $organization_id;
        $order->id = $prod_gas_promotion_id;
        $order->prod_gas_promotion_id = $prod_gas_promotion_id;
        $order->state_code = 'OPEN';
        $order->order_state_code->code = 'OPEN';
        $order->type_draw = 'PROMOTION';
        $order->order_type->code = 'PROMOTION_GAS_USERS';
		
		return $order;	
	}

    /*
     * POST /admin/api/promotions/gets
     * elenco promozioni / articoli / eventuali acquisti
     */  
	public function gets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code, $debug=false) {

        /* 
         * estraggo le promozioni OPEN / CLOSE legate al GAS dello user
         */
        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations'); 

        $where = ['ProdGasPromotionsOrganizations.organization_id' => $organization_id,
                  'ProdGasPromotionsOrganizations.state_code IN ' => $prod_gas_promotion_organization_state_code];    
        $prodGasPromotionsOrganizationsResults = $prodGasPromotionsOrganizationsTable->find()
                                ->contain(['ProdGasPromotions' =>
                                     ['conditions' => ['ProdGasPromotions.type' => 'GAS-USERS', 'ProdGasPromotions.state_code IN ' => $prod_gas_promotion_state_code]]])
                                ->where($where)
                                ->all();

        // debug($prodGasPromotionsOrganizationsResults);
        if($prodGasPromotionsOrganizationsResults->count()>0) {
            
            $prodGasPromotionsTable = TableRegistry::get('ProdGasPromotions'); 
    
            foreach($prodGasPromotionsOrganizationsResults as $numResult => $prodGasPromotionsOrganizationsResult) {

                $order = $this->getOrderDefault($user, $prodGasPromotionsOrganizationsResult->prod_gas_promotion->organization_id, $prodGasPromotionsOrganizationsResult->prod_gas_promotion_id, $debug);

                $where = ['ProdGasPromotions.id' => $prodGasPromotionsOrganizationsResult->prod_gas_promotion_id,
                          'ProdGasPromotions.type' => 'GAS-USERS',
                          'ProdGasPromotions.state_code IN' => $prod_gas_promotion_state_code];    
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

                $where['ProdGasArticlesPromotions'] = ['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prodGasPromotionsOrganizationsResult->prod_gas_promotion_id];

                $options = [];
                $options['sort'] = [];
                $options['limit'] = Configure::read('sql.no.limit');
                $options['page'] = 1;

                $articlesOrdersResults = $articlesOrdersPromotionTable->getCartsByUser($user, $prodGasPromotionsResults->organization_id, $user_id, $order, $where, $options);
                // debug($articlesOrdersResults);

                $ii=0;
                $newResults2 = [];
                foreach($articlesOrdersResults as  $numResult2 => $articlesOrdersResult) { 
                    $articlesOrdersResult = new ApiProdGasArticlesPromotionDecorator($user, $articlesOrdersResult, $prodGasPromotionsResults); 
                    $newResults2[$ii] = $articlesOrdersResult->results;
                    $ii++;
                }

                $newResults[$numResult]['promotion'] = $prodGasPromotionsResults;
                $newResults[$numResult]['order'] = (array)$order;
                $newResults[$numResult]['article_orders'] = $newResults2;
            } // end foreach($prodGasPromotionsOrganizationsResults as $numResult => $prodGasPromotionsOrganizationsResult)
        } // end if($prodGasPromotionsOrganizationsResults->count()>0)
        $results['results'] = $newResults;

        return $results;
	}

    /*
     * POST /admin/api/promotions/user-cart-gets
     * elenco promozioni / articoli solo con acquisti
     */  
	public function userCartGets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code, $debug=false) {

		$results = $this->gets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code, $debug);
		if(!empty($results)) {
			/*
			 * esculdo articoli non acquistati dallo user
			 */
			foreach($results['results'] as $numResult => $promotion) {
		
				foreach($promotion['article_orders'] as $numResult2 => $article_order) {
				
					if(!isset($article_order['cart']) && empty($article_order['cart'])) {
						unset($results[$numResult]['article_orders'][$numResult2]);
					} 
				}
			}
		}

		return $results;
	}
}