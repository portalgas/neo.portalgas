<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ArticlesOrdersGasParentGroupsTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
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
    *  proprietario listino: per gestione permessi di modifica
    *  article_orders: articoli gia' associati (con eventuali acquisti => di tutti i GAS)
    *  articles: articoli da associare
    */    
    public function getAssociateToOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
            
        $results = [];
        $results['esito'] = true;
        $results['article_orders'] = []; // articoli gia' associati, se empty => prima volta => copia da articles
        $results['articles'] = []; // articles: articoli da associare
        
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
        /* 
         * aggiungo eventuali acquisti degli ordini figli
         */
        if(!empty($results['article_orders'])) {

            $cartsTable = TableRegistry::get('Carts');
            $ordersTable = TableRegistry::get('Orders');
            $order_childs = $ordersTable->find()
                                        ->where(['Orders.parent_id' => $order->id])
                                        ->all();
            if($order_childs->count()>0) 
            foreach($order_childs as $order_child) { // loop per ogni ordine figlio
                foreach($results['article_orders'] as $numResult => $article_order) { // loop per ogni articolo
                    $where = ['Carts.organization_id' => $order_child->organization_id,
                                'Carts.order_id' => $order_child->id,
                                'Carts.article_organization_id' => $article_order->article_organization_id,
                                'Carts.article_id' => $article_order->article_id];

                    $carts = $cartsTable->find()
                                        ->where($where)
                                        ->all();  
                    if($carts->count()>0) {
                        $results['article_orders'][$numResult]->carts += $carts->toArray();
                    }                              
                }
            }
        }

        $where2 = [];
        $ids = [];
        if(!empty($results['article_orders'])) {
            /* 
            * escludo gli articoli gia' associati
            * */
            foreach($results['article_orders'] as $article_order) {
                array_push($ids, $article_order->article_id);
            }

            $where2['Articles'] = ['Articles.id NOT IN' => $ids];
        }

        /* 
        * $articles => articoli da associare
        */ 
        $owner_articles = $order->owner_articles;
        $supplier_organization_id = $order->supplier_organization_id; 

        $articlesTable = TableRegistry::get('Articles');
        $results['articles'] = $articlesTable->getsToArticleOrders($user, $organization_id, $supplier_organization_id, $where2);
        // debug($results);
    
        /* 
        * se non ci sono articoli gia' associati
        * associo tutti gli articoli ordinabili e rileggo articles_orders
        */
        if(empty($results['article_orders'])) {
            // articoli gia' associati, se empty => prima volta => copia da articles
            $resultAddsByArticles = $this->addsByArticles($user, $organization_id, $order, $results['articles']);
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
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug=false) {
        return parent::aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug);
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
        return parent::getCartsByArticles($user, $organization_id, $orderResults, $where, $options, $debug);
    }   

    /*
     * implement
     * elenco articoli acquistiti dagli utenti
     * l'ordine e' fittizio, ricavo gli ordini (GasGroups) associati
     */    
    public function getCartsByOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        
        $results = [];

        /* 
         * l'ordine e' fittizio, ricavo gli ordini (GasGroups) associati
         */
        $ordersTable = TableRegistry::get('Orders');
        $whereOrder = ['Orders.organization_id' => $organization_id,
                    'Orders.order_type_id' => Configure::read('Order.type.gas_groups'),
                    'Orders.parent_id' => $order->id];
        $orders = $ordersTable->find()
                ->contain(['GasGroups', 'Deliveries', 'SuppliersOrganizations'])
                ->where($whereOrder)
                ->order(['GasGroups.name'])
                ->all();
                
        if($orders->count()==0)
            return $results;

        (isset($where['ArticlesOrders'])) ? $where['ParamsArticlesOrders'] = $where['ArticlesOrders']: $where['ParamsArticlesOrders'] = [];
                        
        foreach($orders as $numResult => $order) {
            
            $where['ArticlesOrders'] = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                                // $this->getAlias().'.article_id' => 142,
                                $this->getAlias().'.order_id' => $order->id,
                                $this->getAlias().'.stato != ' => 'N'], 
                                $where['ParamsArticlesOrders']);
   
            $article_orders = $this->gets($user, $organization_id, $order, $where, $options, $debug);
            if($debug) debug($article_orders);
         
            /*
            * escludo articoli non acquistati
            */ 
            if(!empty($article_orders))
            foreach($article_orders as $numResult2 => $article_order) {
                if(empty($article_order['carts'])) 
                    unset($article_orders[$numResult2]);
            }
 
            if(!empty($article_orders)) {
                $results[$numResult] = $order;
                $results[$numResult]['article_orders'] = $article_orders;

            }

        } // end foreach($orders as $numResult => $order) 

        if($debug) debug($results);

        return $results;        
    }

    /*
     * implement
     *
     * da Orders chi gestisce listino articoli
     * order_type_id' => (int) 4,
     * owner_articles' => 'REFERENT',
     * owner_organization_id
     * owner_supplier_organization_id
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

    /*
     * implement
     * elimino articoli associati
     *  all'ordine (GasParentGropus) 
     *  da quelli che derivano (GasGropus) 
     */    
    public function deleteByIds($user, $organization_id, $order, $ids, $debug=false) {   

        $results = false;
        
        $organization_id = $ids['organization_id'];
        $order_id = $ids['order_id'];
        $article_organization_id = $ids['article_organization_id'];
        $article_id = $ids['article_id'];

        $where = [$this->getAlias().'.organization_id' => $organization_id, 
                  $this->getAlias().'.order_id' => $order_id, 
                  $this->getAlias().'.article_organization_id' => $article_organization_id, 
                  $this->getAlias().'.article_id' => $article_id];
        // debug($where);

        $entity = $this->find()
                        ->where($where)
                        ->first();
        if(!empty($entity))
            $results = $this->delete($entity);                        
        // debug($results);

        /* 
         * ordini figlio
         */
        $ordersTable = TableRegistry::get('Orders');
        $order_childs = $ordersTable->find()
                                    ->where(['Orders.parent_id' => $order->id])
                                    ->all();
        foreach($order_childs as $order_child) {
            $where = [$this->getAlias().'.organization_id' => $order_child->organization_id, 
                        $this->getAlias().'.order_id' => $order_child->id, 
                        $this->getAlias().'.article_organization_id' => $article_organization_id, 
                        $this->getAlias().'.article_id' => $article_id];

            $entity = $this->find()
                            ->where($where)
                            ->first();
                          
            if(!empty($entity))
                $results = $this->delete($entity);  
        }

        return $results;
     }  
}