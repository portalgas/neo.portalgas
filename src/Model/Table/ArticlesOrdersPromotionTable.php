<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticlesOrdersPromotionTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->entityClass('App\Model\Entity\ArticlesOrder');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }
   
    /*
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user  
     *  ArticlesOrders.article_id              = Articles.id
     *  ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCarts($user, $organization_id, $user_id, $where=[], $order=[], $debug=false) { 

        $order_id = $where['order_id'];

        $where_article_order = [];
        if(isset($where['ArticlesOrders']))
           $where_article_order = $where['ArticlesOrders'];
        $where_article_order = array_merge([$this->alias().'.organization_id' => $organization_id,
                              $this->alias().'.order_id' => $order_id,
                              $this->alias().'.stato != ' => 'N'], 
                              $where_article_order);                  
        if($debug) debug($where_article_order);
        
        $order = [$this->alias().'.name'];

        $results = $this->find()
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->where($where_article_order)
                        ->order($order)
                        // ->limit(2)
                        ->all()
                        ->toArray();

        /*
         * estraggo eventuali acquisti / promotions
         */ 
        if($results) {

            $cartsTable = TableRegistry::get('Carts');
            $prodGasArticlesPromotionsTable = TableRegistry::get('ProdGasArticlesPromotions');
            foreach($results as $numResult => $result) {

                /*
                 * Carts
                 */
                $where_cart = ['Carts.organization_id' => $result['organization_id'],
                          'Carts.order_id' => $result['order_id'],
                          'Carts.article_organization_id' => $result['article_organization_id'],
                          'Carts.article_id' => $result['article_id'],
                          'Carts.user_id' => $user_id, 
                          'Carts.deleteToReferent' => 'N',
                          'Carts.stato' => 'Y'];
                $cartResults = $cartsTable->find()
                            ->where($where_cart)
                            ->first();
                if($debug) debug($where_cart);
                if($debug) debug($cartResults);

                $results[$numResult]['cart'] = [];
                if(!empty($cartResults)) {
                    $results[$numResult]['cart'] = $cartResults;
                    $results[$numResult]['cart']['qty'] = $cartResults->qta;
                    $results[$numResult]['cart']['qty_new'] = $cartResults->qta;  // nuovo valore da FE
                } 
                else {
                    $results[$numResult]['cart']['organization_id'] = $result['organization_id'];
                    $results[$numResult]['cart']['user_id'] = $result['user_id'];
                    $results[$numResult]['cart']['order_id'] = $result['order_id'];
                    $results[$numResult]['cart']['article_organization_id'] = $result['article_organization_id'];
                    $results[$numResult]['cart']['article_id'] = $result['article_id'];                  
                    $results[$numResult]['cart']['qty'] = 0;
                    $results[$numResult]['cart']['qty_new'] = 0;  // nuovo valore da FE
                }

                /*
                 * Promotions
                 */
                $where_promotion = ['ProdGasArticlesPromotions.organization_id' => $result['article']['organization_id'],
                          'ProdGasArticlesPromotions.article_id' => $result['article']['id']];
                $prodGasArticlesPromotionsResults = $prodGasArticlesPromotionsTable->find()
                            ->where($where_promotion)
                            ->first();
                if($debug) debug($where_promotion);
                if($debug) debug($prodGasArticlesPromotionsResults);

                $results[$numResult]['promotion'] = $prodGasArticlesPromotionsResults;
            }
        } // if($results)
        
        if($debug) debug($results);

        return $results;
    } 
}