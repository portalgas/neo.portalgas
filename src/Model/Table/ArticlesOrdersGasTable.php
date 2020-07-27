<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ArticlesOrdersGasTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
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
     * implement
     */
    public function gets($user, $organization_id, $order_id) {
    
/*
        $owner_articles = $order['Order']['owner_articles'];
        $owner_organization_id = $order['Order']['owner_organization_id'];
        $owner_supplier_organization_id = $order['Order']['owner_supplier_organization_id'];
        $article_organization_id = $order['Order']['owner_organization_id'];
        


            $options = [];
            $options['conditions'] = ['Cart.user_id' => $this->Authentication->getIdentity()->id,
                                      'Cart.deleteToReferent' => 'N'];
            if (!empty($filterArticleName))
                $options['conditions'] += ['Article.name' => $filterArticleName];
            if (!empty($filterArticleArticleTypeIds))
                $options['conditions'] += ['ArticleArticleTypeId.article_type_id' => $filterArticleArticleTypeIds];

            $options['order'] = 'Article.name';


     public function getArticoliEventualiAcquistiInOrdine($user, $orderResult, $options=[], $debug = false) {

        if(!is_array($orderResult))
            $orderResult = $this->_getOrderById($user, $orderResult, $debug);
        
        $results = [];

        try {
            if (!isset($options['order']))
                $options['order'] = 'Article.name ASC';

            $sql = "SELECT 
                        ArticlesOrder.*,Article.*";
            if (isset($options['conditions']['Cart.user_id']))
                $sql .= ",Cart.* ";
            if (isset($options['conditions']['ArticleArticleTypeId.article_type_id']))
                $sql .= ",ArticlesArticlesType.article_type_id ";
            $sql .= "FROM " .
                    Configure::read('DB.prefix') . "articles AS Article, ";
            if (isset($options['conditions']['ArticleArticleTypeId.article_type_id']))
                $sql .= Configure::read('DB.prefix') . "articles_articles_types ArticlesArticlesType, ";
            $sql .= Configure::read('DB.prefix') . "articles_orders AS ArticlesOrder ";
            if (isset($options['conditions']['Cart.user_id'])) {
                $sql .= " LEFT JOIN " . Configure::read('DB.prefix') . "carts AS Cart ON " .
                        "(Cart.organization_id = ArticlesOrder.organization_id AND Cart.order_id = ArticlesOrder.order_id AND Cart.article_organization_id = ArticlesOrder.article_organization_id AND Cart.article_id = ArticlesOrder.article_id " .
                        "AND Cart.user_id = " . $options['conditions']['Cart.user_id'] . "
                        AND Cart.deleteToReferent = 'N')";
            }
            $sql .= "WHERE 
                        ArticlesOrder.organization_id = ".$user->organization['Organization']['id']." 
                        AND ArticlesOrder.article_organization_id = Article.organization_id
                        AND ArticlesOrder.article_id = Article.id                       
                        AND ArticlesOrder.order_id = ".$orderResult['Order']['id']." 
                        AND ArticlesOrder.stato != 'N' 
                        AND Article.stato = 'Y' 
                        AND Article.organization_id = ".$orderResult['Order']['owner_organization_id']." 
                        AND Article.supplier_organization_id = ".$orderResult['Order']['owner_supplier_organization_id']."";

            if (isset($conditions['ArticlesOrder.pezzi_confezione']))
                $options['conditions'] += ['ArticlesOrder.pezzi_confezione > ' => $conditions['ArticlesOrder.pezzi_confezione']];

            if (isset($options['conditions']['ArticleArticleTypeId.article_type_id']))
                $sql .= " AND ArticlesArticlesType.organization_id = ".$orderResult['Order']['owner_organization_id']." 
                          AND ArticlesArticlesType.article_type_id IN (" . $options['conditions']['ArticleArticleTypeId.article_type_id'] . ")
                          AND Article.id = ArticlesArticlesType.article_id ";

            if (isset($options['conditions']['Article.name']))
                $sql .= " AND lower(Article.name) LIKE '%" . strtolower(addslashes($options['conditions']['Article.name'])) . "%'";

            // filtro un solo ordine AjaxGasCartComtroller::__managementCart()
             
            if (isset($options['conditions']['Article.id']))
                $sql .= " AND Article.id = " . $options['conditions']['Article.id'];

            // Organization.hasFieldArticleCategoryId
            if (isset($options['conditions']['Article.category_id']))
                $sql .= " AND Article.category_id = " . $options['conditions']['Article.category_id'];

            $sql .= " ORDER BY " . $options['order'];
            self::d('getArticoliEventualiAcquistiInOrdine '.$sql, $debug);
            $results = $this->query($sql);

            // applico metodi afterFind()
             
            foreach ($results as $numResult => $result) {

                // Article
               
                $results[$numResult]['Article']['prezzo_'] = number_format($result['Article']['prezzo'], 2, Configure::read('separatoreDecimali'), Configure::read('separatoreMigliaia'));
                $results[$numResult]['Article']['prezzo_e'] = $results[$numResult]['Article']['prezzo_'] . ' &euro;';

                $qta = str_replace(".", ",", $result['Article']['qta']);
                $arrCtrlTwoZero = explode(",", $qta);
                if ($arrCtrlTwoZero[1] == '00')
                    $qta = $arrCtrlTwoZero[0];
                $results[$numResult]['Article']['qta_'] = $qta;

                // ArticlesOrder
                 
                $results[$numResult]['ArticlesOrder']['prezzo_'] = number_format($result['ArticlesOrder']['prezzo'], 2, Configure::read('separatoreDecimali'), Configure::read('separatoreMigliaia'));
                $results[$numResult]['ArticlesOrder']['prezzo_e'] = $results[$numResult]['ArticlesOrder']['prezzo_'] . ' &euro;';

                // Cart
                 
            } // foreach ($results as $numResult => $result) 

            self::d($results, $debug);
            
        } catch (Exception $e) {
            CakeLog::write('error', $sql);
            CakeLog::write('error', $e);
        }

        return $results;
    }           
*/        
    }    
}
