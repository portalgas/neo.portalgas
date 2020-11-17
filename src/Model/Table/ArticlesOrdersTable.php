<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;
use Cake\Core\Configure;

class ArticlesOrdersTable extends Table
{
    protected $_sort = null;
    protected $_limit = null;
    protected $_page = null;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_articles_orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'order_id', 'article_organization_id', 'article_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => 'article_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Carts', [
            'foreignKey' => ['organization_id', 'order_id', 'article_organization_id', 'article_id'],
            'joinType' => 'LEFT'
        ]);        
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('qta_cart')
            ->notEmptyString('qta_cart');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->numeric('prezzo')
            ->notEmptyString('prezzo');

        $validator
            ->integer('pezzi_confezione')
            ->requirePresence('pezzi_confezione', 'create')
            ->notEmptyString('pezzi_confezione');

        $validator
            ->integer('qta_minima')
            ->requirePresence('qta_minima', 'create')
            ->notEmptyString('qta_minima');

        $validator
            ->integer('qta_massima')
            ->requirePresence('qta_massima', 'create')
            ->notEmptyString('qta_massima');

        $validator
            ->integer('qta_minima_order')
            ->notEmptyString('qta_minima_order');

        $validator
            ->integer('qta_massima_order')
            ->requirePresence('qta_massima_order', 'create')
            ->notEmptyString('qta_massima_order');

        $validator
            ->integer('qta_multipli')
            ->requirePresence('qta_multipli', 'create')
            ->notEmptyString('qta_multipli');

        $validator
            ->integer('alert_to_qta')
            ->requirePresence('alert_to_qta', 'create')
            ->notEmptyString('alert_to_qta');

        $validator
            ->scalar('send_mail')
            ->notEmptyString('send_mail');

        $validator
            ->scalar('flag_bookmarks')
            ->notEmptyString('flag_bookmarks');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));
        $rules->add($rules->existsIn('article_organization_id', 'ArticleOrganizations'));
        $rules->add($rules->existsIn(['article_organization_id', 'article_id'], 'Articles'));

        return $rules;
    }

    public function factory($user, $organization_id, $orderResults) {

        $table_registry = '';

        $ordersTable = TableRegistry::get('Orders');

        if(!is_object($orderResults) && !is_array($orderResults)) {
            $where = ['Orders.organization_id' => $organization_id,
                      'Orders.id' => $orderResults];
            $orderResults = $ordersTable->find()
                                    ->where($where)
                                    ->first()
                                    ->toArray();
        }

        // debug($where);
        // debug($orderResults);
        if(empty($orderResults))
            return false;
        
        if(is_object($orderResults))
            $order_type_id = $orderResults->order_type_id;
        else
        if(is_array($orderResults))
            $order_type_id = $orderResults['order_type_id'];

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');  

        switch (strtoupper($order_type_id)) {
            case Configure::read('Order.type.des-titolare'):
            case Configure::read('Order.type.des'):
                $table_registry = 'ArticlesOrdersDes';
                break;
            case Configure::read('Order.type.gas'):
                $table_registry = 'ArticlesOrdersGas';
                break;
            case Configure::read('Order.type.promotion'):
                $table_registry = 'ArticlesOrdersPromotion';
                break;
            case Configure::read('Order.type.pact-pre'):
                $table_registry = 'ArticlesOrdersPactPre';
                break;
            case Configure::read('Order.type.pact'):
                $table_registry = 'ArticlesOrdersPact';
                break;
            
            default:
                die('OrdersTable order_type_id ['.$order_type_id.'] non previsto');
                break;
        }

        return TableRegistry::get($table_registry);
    } 

    /*
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article_order, $debug=false) {

        $organization_id = $article_order['ids']['organization_id']; 
        $order_id = $article_order['ids']['order_id']; 
        $article_organization_id = $article_order['ids']['article_organization_id'];
        $article_id = $article_order['ids']['article_id'];
        
        $cartsTable = TableRegistry::get('Carts');
        $qta_cart_new = $cartsTable->getQtaCartByArticle($user, $organization_id, $order_id, $article_organization_id, $article_id, $debug);
        if(Configure::write('Logs.cart')) Log::write('debug', 'aggiornaQtaCart_StatoQtaMax qta_cart_new '.$qta_cart_new);

        $article_order['qta_cart'] = $qta_cart_new;
        $this->_updateArticlesOrderQtaCart_StatoQtaMax($user, $organization_id, $article_order, $debug);
    }

    protected function _updateArticlesOrderQtaCart_StatoQtaMax($user, $organization_id, $article_order, $debug=false) {            

        if($debug) debug("ArticlesOrder.qta_massima_order ".$article_order['qta_massima_order']." ArticlesOrder.qta_cart ".$article_order['qta_cart']);
        if(Configure::write('Logs.cart')) Log::write('debug', '_updateArticlesOrderQtaCart_StatoQtaMax ArticlesOrder.qta_massima_order '.$article_order['qta_massima_order']." ArticlesOrder.qta_cart ".$article_order['qta_cart']);

        $qta_massima_order = intval($article_order['qta_massima_order']);
        $qta_cart = intval($article_order['qta_cart']);

        /*
         * ctrl se ArticlesOrder.qta_massima_order > 0, se SI controllo lo ArticlesOrder.stato
         */
        if ($qta_massima_order > 0) {
            if ($qta_cart >= $qta_massima_order) {
                if ($article_order['stato'] != 'QTAMAXORDER') { // ho gia' settato a QTAMAXORDER e eventualmente inviato la mail
                    $article_order['stato'] = 'QTAMAXORDER';
                    $article_order['send_mail'] = 'N';  // invia mail da Cron::mailReferentiQtaMax
                }
            }
            else
            if ($qta_cart < $qta_massima_order && $article_order['stato'] == 'QTAMAXORDER') {
                $article_order['stato'] = 'Y';
                $article_order['send_mail'] = 'N'; // invia mail da Cron::mailReferentiQtaMax
            }
        } 
        else
        if ($qta_massima_order == 0) {
            if ($article_order['stato'] == 'QTAMAXORDER')
                $article_order['stato'] = 'Y';
            $article_order['send_mail'] = 'N';
        }

        if($debug) debug($article_order);
        
        $ids = [];
        $ids['organization_id'] = $article_order['ids']['organization_id'];
        $ids['order_id'] = $article_order['ids']['order_id'];
        $ids['article_organization_id'] = $article_order['ids']['article_organization_id'];
        $ids['article_id'] = $article_order['ids']['article_id'];

        unset($article_order['article']);
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');

        $articlesOrder = $this->getByIds($user, $organization_id, $ids, $debug);
        $articlesOrder = $this->patchEntity($articlesOrder, $article_order);
        if(Configure::write('Logs.cart')) Log::write('debug', $articlesOrder);


        if (!$articlesOrdersTable->save($articlesOrder)) {
            debug($articlesOrder->getErrors());
            if($debug) debug("ArticleOrder::aggiornaQtaCart_StatoQtaMax() - NO aggiorno l'ArticlesOrder con order_id " . $article_order['order_id'] . " article_organization_id " . $article_order['article_organization_id'] . " article_id " . $article_order['article_id'] . " a qta_cart = " . $qta_cart . " stato " . $article_order['stato']);
        }
        else  {
            if($debug) debug("ArticleOrder::aggiornaQtaCart_StatoQtaMax() - OK aggiorno l'ArticlesOrder con order_id " . $article_order['order_id'] . " article_organization_id " . $article_order['article_organization_id'] . " article_id " . $article_order['article_id'] . " a qta_cart = " . $qta_cart . " stato " . $article_order['stato']);
        }
    }

    /*
     * implement
     *
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user  
     * ArticlesOrders.article_id              = Articles.id
     * ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {

        $order_id = $where['order_id'];

        $order_state_code = $orderResults->state_code;

        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];
        $where['ArticlesOrders'] = array_merge([$this->alias().'.organization_id' => $organization_id,
                              $this->alias().'.order_id' => $order_id,
                              $this->alias().'.stato != ' => 'N'], 
                              $where['ArticlesOrders']);

        switch ($order_state_code) {
            case 'RI-OPEN-VALIDATE':
                $where['ArticlesOrders'] += [$this->alias().'.pezzi_confezione > ' => 1];
                $results = $this->getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug); 
            break;
            default:
                $results = $this->gets($user, $organization_id, $orderResults, $where, $options, $debug);
            break;
        }          
        if($debug) debug($results);

        /*
         * estraggo eventuali acquisti
         */ 
        if($results) {
            
            $cartsTable = TableRegistry::get('Carts');
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
                    $results[$numResult]['cart']['qta'] = $cartResults->qta;
                    $results[$numResult]['cart']['qta_new'] = $cartResults->qta;  // nuovo valore da FE
                } 
                else {
                    $results[$numResult]['cart']['organization_id'] = $result['organization_id'];
                    $results[$numResult]['cart']['user_id'] = $result['user_id'];
                    $results[$numResult]['cart']['order_id'] = $result['order_id'];
                    $results[$numResult]['cart']['article_organization_id'] = $result['article_organization_id'];
                    $results[$numResult]['cart']['article_id'] = $result['article_id'];
                    $results[$numResult]['cart']['stato'] = 'Y';                  
                    $results[$numResult]['cart']['qta'] = 0;
                    $results[$numResult]['cart']['qta_new'] = 0;  // nuovo valore da FE
                }
            }
        } // if($results)

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
       
        $this->_getOptions($options); // setta sort / limit / page

        if(!isset($where['Articles']))
           $where['Articles'] = [];
        $where['Articles'] = array_merge(['Articles.stato' => 'Y'], $where['Articles']);
        // debug($where);

        $results = $this->find()
                        ->contain(['Articles' => ['conditions' => $where['Articles']]])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();

        return $results;
    }   

    /*
     * implement
     */
    public function getByIds($user, $organization_id, $ids, $debug=false) {    
       
        $organization_id = $ids['organization_id'];
        $order_id = $ids['order_id'];
        $article_organization_id = $ids['article_organization_id'];
        $article_id = $ids['article_id'];

        $where = [$this->alias().'.organization_id' => $organization_id, 
                  $this->alias().'.order_id' => $order_id, 
                  $this->alias().'.article_organization_id' => $article_organization_id, 
                  $this->alias().'.article_id' => $article_id];
        // debug($where);

        $results = $this->find()
                        ->contain(['Articles'])
                        ->where($where)
                        ->first();
        // debug($results);
        return $results;
    }   

    public function getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug=false) {

        $this->_getOptions($options); // setta sort / limit / page

        $results = [];
        $resultsArticlesOrders = $this->find()
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();

        /*
         * estraggo eventuali acquisti
         */ 
        if($resultsArticlesOrders) {
            $i=0;
            foreach($resultsArticlesOrders as $numResult => $resultsArticlesOrder) {

                $qta_cart = $resultsArticlesOrder['qta_cart'];

                // se DES non prendo ArticlesOrder.qta_cart perche' e' la somma di tutti i GAS ma lo ricalcolo
                if($orderResults->order_type_id==Configure::read('Order.type.des') ||
                   $orderResults->order_type_id==Configure::read('Order.type.des-titolare')) {
                    
                    $cartsTable = TableRegistry::get('Carts');
                    $qta_cart = $cartsTable->getQtaCartByArticle($user, $organization_id, $orderResults->id, $resultsArticlesOrder['article_organization_id'], $resultsArticlesOrder['article_id'], $debug);
                }
                if($qta_cart!==false) {
                    $resultsArticlesOrder['qta_cart'] = $qta_cart;
                }

                $differenza_da_ordinare = ($qta_cart % $resultsArticlesOrder['pezzi_confezione']);
                
                if($differenza_da_ordinare>0) {
                    $differenza_da_ordinare = ($resultsArticlesOrder['pezzi_confezione'] - $differenza_da_ordinare);
                    $differenza_importo = ($differenza_da_ordinare * $resultsArticlesOrder['prezzo']);
                    
                    $results[$i] = $resultsArticlesOrder;
                    $results[$i]['riopen'] = [];
                    $results[$i]['riopen']['differenza_da_ordinare'] = $differenza_da_ordinare;
                    $results[$i]['riopen']['differenza_importo'] = $differenza_importo;
                    $i++;
                }
                else 
                    unset($resultsArticlesOrders[$numResult]);
            }
        }

        return $results;
    }    

    protected function _getOptions($options) {
        isset($options['sort'])? $this->_sort = $options['sort']: $this->_sort = [$this->alias().'.name'];
        isset($options['limit'])? $this->_limit = $options['limit']: $this->_limit = Configure::read('sql.limit');
        isset($options['page'])? $this->_page = $options['page']: $this->_page = 1; 
    }
}