<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ArticlesOrdersPromotionTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\ArticlesOrder');

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
   * implements
   * 
   * gestione associazione articoli all'ordine
   * return
   *  proprietario listino: per gestione permessi di modifica
   *  article_orders: articoli gia' associati
   *  articles: articoli da associare
   */    
    public function getAssociateToOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        return parent::getAssociateToOrder($user, $organization_id, $order, $where, $options, $debug);
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
    public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) { 

        $order_id = $orderResults->id;
        $prod_gas_promotion_id = $orderResults->prod_gas_promotion_id;

        if(!isset($where['ProdGasArticlesPromotions']))
           $where['ProdGasArticlesPromotions'] = ['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prod_gas_promotion_id];
        else
            $where['ProdGasArticlesPromotions'] = array_merge(['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prod_gas_promotion_id], 
                                                  $where['ProdGasArticlesPromotions']);
      
        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];

        $where_article_order = [];
        if(isset($where['ArticlesOrders']))
           $where_article_order = $where['ArticlesOrders'];
        $where['ArticlesOrders'] = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                                                  $this->getAlias().'.order_id' => $order_id,
                                                  $this->getAlias().'.stato != ' => 'N'], 
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
                                   'ProdGasArticlesPromotions' => ['conditions' => $where['ProdGasArticlesPromotions']]])
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
                          'Carts.deleteToReferent' => 'N',
                          'Carts.user_id' => $user_id,
                          'Carts.stato' => 'Y'];

                $cartResults = $cartsTable->find()
                            ->where($where_cart)
                            ->first();
                if($debug) debug($where_cart);
                if($debug) debug($cartResults);

                $results[$numResult]['cart'] = [];
                if(!empty($cartResults)) {
                    $results[$numResult]['cart'] = $cartResults;
                    $results[$numResult]['cart']['qta'] = $cartResults->qta;
                    $results[$numResult]['cart']['qta_new'] = $cartResults->qta;  // nuovo valore da FE
                } 
                else {
                    $results[$numResult]['cart']['organization_id'] = $result['organization_id'];
                    $results[$numResult]['cart']['user_id'] = $result['user_id'];
                    $results[$numResult]['cart']['order_id'] = $result['order_id'];
                    $results[$numResult]['cart']['article_organization_id'] = $result['article_organization_id'];
                    $results[$numResult]['cart']['article_id'] = $result['article_id'];                  
                    $results[$numResult]['cart']['qta'] = 0;
                    $results[$numResult]['cart']['qta_new'] = 0;  // nuovo valore da FE
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
     *
     * estrae gli articoli associati ad un ordine ed evenuuali acquisti di tutti gli users
     *  ArticlesOrders.article_id              = Articles.id
     *  ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) { 

        $order_id = $orderResults->id;
        $prod_gas_promotion_id = $orderResults->prod_gas_promotion_id;

        if(!isset($where['ProdGasArticlesPromotions']))
           $where['ProdGasArticlesPromotions'] = ['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prod_gas_promotion_id];
        else
            $where['ProdGasArticlesPromotions'] = array_merge(['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prod_gas_promotion_id], 
                                                  $where['ProdGasArticlesPromotions']);
      
        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];

        $where_article_order = [];
        if(isset($where['ArticlesOrders']))
           $where_article_order = $where['ArticlesOrders'];
        $where['ArticlesOrders'] = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                                                  $this->getAlias().'.order_id' => $order_id,
                                                  $this->getAlias().'.stato != ' => 'N'], 
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
                                   'ProdGasArticlesPromotions' => ['conditions' => $where['ProdGasArticlesPromotions']]])
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
                          'Carts.deleteToReferent' => 'N',
                          'Carts.stato' => 'Y'];
 
                $cartResults = $cartsTable->find()
                            ->where($where_cart)
                            ->all();
                if($debug) debug($where_cart);
                if($debug) debug($cartResults);

                $results[$numResult]['cart'] = [];
                if($cartResults->count()>0) {
                    /*
                     * calcolo totali
                     */
                    $qta_tot = 0;
                    $importo_tot = 0;
                    foreach($cartResults as $cartResult) {
                        if($cartResult->qta_forzato > 0)
                            $qta_tot += $cartResult->qta_forzato;
                        else
                            $qta_tot += $cartResult->qta;

                        /*
                         * gestione importi
                         * */
                        if ($cartResult->importo_forzato == 0) {
                            if ($cartResult->qta_forzato > 0)
                                $importo_tot += ($cartResult->qta_forzato * $result->prezzo);
                            else {
                                $importo_tot += ($cartResult->qta * $result->prezzo);
                            }
                        } else {
                            $importo_tot += $cartResult->importo_forzato;
                        }
                    }
                    $results[$numResult]['cart']['qta_tot'] = $qta_tot;
                    $results[$numResult]['cart']['importo_tot'] = $importo_tot;
                } 
                else {
                    $results[$numResult]['cart']['qta_tot'] = 0;
                    $results[$numResult]['cart']['importo_tot'] = 0;
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

        $organization_id = $ids['organization_id'];
        $order_id = $ids['order_id'];
        $article_organization_id = $ids['article_organization_id'];
        $article_id = $ids['article_id'];

        $where = [$this->getAlias().'.organization_id' => $organization_id, 
                  $this->getAlias().'.order_id' => $order_id, 
                  $this->getAlias().'.article_organization_id' => $article_organization_id, 
                  $this->getAlias().'.article_id' => $article_id];
        // debug($where);

        $results = $this->find()
                        ->contain(['Articles', 'ProdGasArticlesPromotions'])
                        ->where($where)
                        ->first();
        // debug($results);
        return $results;

    }     
}