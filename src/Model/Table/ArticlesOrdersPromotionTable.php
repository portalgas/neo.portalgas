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

        $this->belongsTo('ProdGasArticlesPromotions', [
            'className' => 'ProdGasArticlesPromotions',
            'foreignKey' => ['article_organization_id', 'article_id'], // fields ArticlesOrders
            'bindingKey' => ['organization_id', 'article_id'],         // fields ProdGasArticlesPromotions
            'joinType' => 'INNER',
        ]);      
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }
   
    /*
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug=false) {
        return parent::aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug);
    }
       
    /*
     * implement
     *
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user  
     *  ArticlesOrders.article_id              = Articles.id
     *  ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) { 

        $order_id = $where['order_id'];

        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];

        $where_article_order = [];
        if(isset($where['ArticlesOrders']))
           $where_article_order = $where['ArticlesOrders'];
        $where['ArticlesOrders'] = array_merge([$this->alias().'.organization_id' => $organization_id,
                              $this->alias().'.order_id' => $order_id,
                              $this->alias().'.stato != ' => 'N'], 
                              $where['ArticlesOrders']);
        
        $this->_getOptions($options); // setta sort / limit / page

        /*
         * da Orders chi gestisce listino articoli
         * order_type_id' => (int) 4,
         * owner_articles' => 'REFERENT',
         * owner_organization_id
         * owner_supplier_organization_id
         */
        if(!isset($where['Articles']))
           $where['Articles'] = [];
        $where['Articles'] = array_merge(['Articles.stato' => 'Y'], $where['Articles']);
        // debug($where);

        $results = $this->find()
                        ->contain(['Articles' => ['conditions' => $where['Articles']], 
                                   'ProdGasArticlesPromotions'])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();
        // debug($results);
                        
        /*
         * estraggo eventuali acquisti / promotions
         */ 
        if($results) {

            $cartsTable = TableRegistry::get('Carts');
            $prodGasArticlesPromotionsTable = TableRegistry::get('ProdGasArticlesPromotions');
            foreach($results as $numResult => $result) {

                $results[$numResult]['order'] = $orderResults;

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

    /*
     * implement
     */
    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {    
       return parent::gets($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function getByIds($user, $organization_id, $ids, $debug=false) {    
       return parent::getByIds($user, $organization_id, $ids, $debug);
    }     
}