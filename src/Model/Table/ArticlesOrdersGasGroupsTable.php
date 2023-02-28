<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ArticlesOrdersGasGroupsTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\ArticlesOrder');
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
   *  proprietario listino: per gestione permessi di modifica (eredita da gas_parent_groups)
   *  article_orders: articoli gia' associati
   *  articles: articoli da associare  (eredita da gas_parent_groups)
   */    
    public function getAssociateToOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        
        $results = [];
        $results['esito'] = true;
        $results['article_orders'] = []; // articoli gia' associati, se empty => prima volta => copia da articles
        $results['articles'] = []; // articles: articoli da associare (eredita da gas_parent_groups)
        
        /* 
         * $article_orders => articoli gia' associati 
         */ 
        $where = [];
        $where['ArticlesOrders'] = [$this->getAlias().'.organization_id' => $order->organization_id,
                                    $this->getAlias().'.order_id' => $order->id,
                                ]; 
                
        $options = [];
        $options['sort'] = [];
        $options['limit'] = Configure::read('sql.no.limit');
        $results['article_orders'] = $this->gets($user, $organization_id, $order, $where, $options);
    
        $where2 = [];
        $ids = [];
        if(!empty($results['article_orders'])) {
            /* 
             * escludo gli articoli gia' associati
             * */
            foreach($results['article_orders'] as $article_order) {
                array_push($ids, $article_order->article_id);
            }

            $where2 = ['ArticlesOrders.article_id NOT IN' => $ids];
        }

        /* 
         * $articles => articoli da associare, li prendo dal parent
         */ 
        $where2 += ['ArticlesOrders.organization_id' => $order->organization_id,
                                    'ArticlesOrders.order_id' => $order->parent_id,
                                    'ArticlesOrders.stato !=' => 'N'];
      
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $results['articles'] = $articlesOrdersTable->find()
                                ->where($where2)
                                ->all();

        /* 
         * se non ci sono articoli gia' associati
         * associo tutti gli articoli ordinabili e rileggo articles_orders
         */                                
        if(empty($results['article_orders'])) {
            // articoli gia' associati, se empty => prima volta => copia da articles
            $resultAddsByArticles = $this->addsByArticlesOrders($user, $organization_id, $order, $results['articles']);
            if($resultAddsByArticles!==true) {
                $results['esito'] = false;
                $results['errors'] = $resultAddsByArticles;
            }  

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');
            $results['article_orders'] = $this->gets($user, $organization_id, $order, $where, $options); 
            $results['articles'] = [];
        }

        return $results;
    }

    /*
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article_order, $debug=false) {
        return parent::aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article_order, $debug);
    }

    /*
     * implement
     */
    public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {
        return parent::getCartsByUser($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {
        $this->_getOptions($options); // setta sort
        return parent::getCartsByArticles($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */    
    public function getCartsByOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        return parent::getCartsByOrder($user, $organization_id, $order, $where, $options, $debug);
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
