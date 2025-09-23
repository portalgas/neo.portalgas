<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Traits;
use App\Decorator\ApiArticleOrderDecorator;

class ArticlesOrdersTable extends Table
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

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
        // $rules->add($rules->existsIn(['article_organization_id', 'article_id'], 'Articles'));

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
                                    ->first();

            if(!empty($orderResults))
                $orderResults = $orderResults->toArray();
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
            case Configure::read('Order.type.des'):
                $table_registry = 'ArticlesOrdersDes';
            break;
            case Configure::read('Order.type.gas'):
            case Configure::read('Order.type.socialmarket'):
            case Configure::read('Order.type.des_titolare'):
                $table_registry = 'ArticlesOrdersGas';
            break;
            case Configure::read('Order.type.gas_parent_groups'):
                $table_registry = 'ArticlesOrdersGasParentGroups';
            break;
            case Configure::read('Order.type.gas_groups'):
                $table_registry = 'ArticlesOrdersGasGroups';
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
                die('ArticlesOrdersTable order_type_id ['.$order_type_id.'] non previsto');
            break;
        }

        return TableRegistry::get($table_registry);
    }

    /*
    * implements
    *
    * gestione associazione articoli all'ordine
    * return
    *  proprietario listino: per gestione permessi di modifica
    *  article_orders: articoli gia' associati (con eventuali acquisti)
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
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article_order, $debug=false) {

        $organization_id = $article_order['ids']['organization_id'];
        $order_id = $article_order['ids']['order_id'];
        $article_organization_id = $article_order['ids']['article_organization_id'];
        $article_id = $article_order['ids']['article_id'];

        $cartsTable = TableRegistry::get('Carts');
        $qta_cart_new = $cartsTable->getQtaCartByArticle($user, $organization_id, $order_id, $article_organization_id, $article_id, $debug);
        if(Configure::read('Logs.cart')) Log::write('debug', 'aggiornaQtaCart_StatoQtaMax qta_cart_new '.$qta_cart_new);

        $article_order['qta_cart'] = $qta_cart_new;
        $this->_updateArticlesOrderQtaCart_StatoQtaMax($user, $organization_id, $article_order, $debug);
    }

    protected function _updateArticlesOrderQtaCart_StatoQtaMax($user, $organization_id, $article_order, $debug=false) {

        if(Configure::read('Logs.cart')) Log::write('debug', '_updateArticlesOrderQtaCart_StatoQtaMax ArticlesOrder.qta_massima_order '.$article_order['qta_massima_order']." ArticlesOrder.qta_cart ".$article_order['qta_cart']);

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

        $ids = [];
        $ids['organization_id'] = $article_order['ids']['organization_id'];
        $ids['order_id'] = $article_order['ids']['order_id'];
        $ids['article_organization_id'] = $article_order['ids']['article_organization_id'];
        $ids['article_id'] = $article_order['ids']['article_id'];

        unset($article_order['article']);

        $articlesOrder = $this->getByIds($user, $organization_id, $ids, $debug);
        if(!empty($articlesOrder)) {
            unset($article_order['order']); // se no aggiorna anche l'ordine
            $articlesOrder = $this->patchEntity($articlesOrder, $article_order);

            if(Configure::read('Logs.cart')) Log::write('debug', $articlesOrder);

            if (!$this->save($articlesOrder)) {
                Log::write('debug', $articlesOrder->getErrors());
                if(Configure::read('Logs.cart')) Log::write('debug', "ArticleOrder::aggiornaQtaCart_StatoQtaMax() - NO aggiorno l'ArticlesOrder con order_id " . $ids['order_id'] . " article_organization_id " . $ids['article_organization_id'] . " article_id " . $ids['article_id'] . " a qta_cart = " . $qta_cart . " stato " . $article_order['stato']);
            }
            else  {
                if(Configure::read('Logs.cart')) Log::write('debug', "ArticleOrder::aggiornaQtaCart_StatoQtaMax() - OK aggiorno l'ArticlesOrder con order_id " . $ids['order_id'] . " article_organization_id " . $ids['article_organization_id'] . " article_id " . $ids['article_id'] . " a qta_cart = " . $qta_cart . " stato " . $article_order['stato']);
            }
        }
        else {
           if(Configure::read('Logs.cart')) Log::write('debug', "ArticleOrder::aggiornaQtaCart_StatoQtaMax() - ERROR record NON trovato! - NO aggiorno l'ArticlesOrder con order_id " . $ids['order_id'] . " article_organization_id " . $ids['article_organization_id'] . " article_id " . $ids['article_id'] . " a qta_cart = " . $qta_cart . " stato " . $article_order['stato']);
        }
    }

    /*
     * implement
     *
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user
     * ArticlesOrders.article_id              = Articles.id
     * ArticlesOrders.article_organization_id = Articles.organization_id
     *
     * $options['refer']
     *  CART se RI-OPEN-VALIDATE, prendo tutti gli articoli non solo quelli con pezzi_confezione > 1 per riapertura
     *  ACQUISTA se RI-OPEN-VALIDATE, prendo solo gli articoli con pezzi_confezione > 1 per riapertura
     */
    public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {

        $order_id = $orderResults->id;

        $order_state_code = $orderResults->state_code;

        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];
        $where['ArticlesOrders'] = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                             // $this->getAlias().'.article_id' => 142,
                              $this->getAlias().'.order_id' => $order_id,
                              $this->getAlias().'.stato != ' => 'N'],
                              $where['ArticlesOrders']);

        if(!isset($options['refer']))
            $options['refer'] = 'ACQUISTA';

        switch ($options['refer']) {
            case 'CART':
                $results = $this->gets($user, $organization_id, $orderResults, $where, $options, $debug);
                break;
            case 'ACQUISTA':
                switch ($order_state_code) {
                    case 'RI-OPEN-VALIDATE':
                        $where['ArticlesOrders'] += [$this->getAlias().'.pezzi_confezione > ' => 1];
                        $results = $this->getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug);
                        break;
                    default:
                        $results = $this->gets($user, $organization_id, $orderResults, $where, $options, $debug);
                        break;
                }
                break;
        }
        if($debug) debug($results);

        /*
         * estraggo eventuali acquisti
         */
        if($results) {

            $cartsTable = TableRegistry::get('Carts');
            foreach($results as $numResult => $result) {

                /*
                 * per evitare json troppo pesante
                 * Error: Allowed memory size of 268435456 bytes exhausted (tried to allocate 199233536 bytes)
                   $results[$numResult]['order'] = $orderResults->toArray();
                 */

                /*
                 * Carts
                 */
                $where_cart = ['Carts.organization_id' => $result['organization_id'],
                          'Carts.order_id' => $result['order_id'],
                          'Carts.article_organization_id' => $result['article_organization_id'],
                          'Carts.article_id' => $result['article_id'],
                          'Carts.deleteToReferent' => 'N',
                          'Carts.stato' => 'Y'];
                if(!empty($user_id)) {
                    /*
                     * acquisti solo dell'utente passato
                     */
                    $where_cart += ['Carts.user_id' => $user_id];
                }
                $cartResults = $cartsTable->find()
                            // ->contain(['Users'])
                            ->where($where_cart)
                            ->first();
                if($debug) debug($where_cart);
                if($debug) debug($cartResults);

                $results[$numResult]['cart'] = [];
                if(!empty($cartResults)) {
                    $results[$numResult]['cart'] = $cartResults;
                    // $results[$numResult]['cart']['user'] = $cartResults->user;
                    $results[$numResult]['cart']['qta'] = $cartResults->qta;
                    $results[$numResult]['cart']['qta_new'] = $cartResults->qta;  // nuovo valore da FE
                }
                else {
                    $results[$numResult]['cart']['organization_id'] = $result['organization_id'];
                    $results[$numResult]['cart']['user_id'] = 0;
                    $results[$numResult]['cart']['order_id'] = $result['order_id'];
                    $results[$numResult]['cart']['article_organization_id'] = $result['article_organization_id'];
                    $results[$numResult]['cart']['article_id'] = $result['article_id'];
                    $results[$numResult]['cart']['stato'] = 'Y';
                    $results[$numResult]['cart']['qta'] = 0;
                    $results[$numResult]['cart']['qta_new'] = 0;  // nuovo valore da FE
                }

                /*
                 * popolo $results[$i]['riopen'], se $options['refer']=='ACQUISTA gia' fatto in getRiOpenValidate
                 */
                if($order_state_code=='RI-OPEN-VALIDATE' && $options['refer']=='CART') {
                    $qta_cart = $result['qta_cart'];

                    // se DES non prendo ArticlesOrder.qta_cart perche' e' la somma di tutti i GAS ma lo ricalcolo
                    if($orderResults->order_type_id==Configure::read('Order.type.des') ||
                        $orderResults->order_type_id==Configure::read('Order.type.des_titolare')) {

                        $cartsTable = TableRegistry::get('Carts');
                        $qta_cart = $cartsTable->getQtaCartByArticle($user, $organization_id, $orderResults->id, $result['article_organization_id'], $result['article_id'], $debug);
                    }
                    if($qta_cart!==false) {
                        $result['qta_cart'] = $qta_cart;
                    }

                    $differenza_da_ordinare = ($qta_cart % $result['pezzi_confezione']);

                    if($differenza_da_ordinare>0) {
                        $differenza_da_ordinare = ($result['pezzi_confezione'] - $differenza_da_ordinare);
                        $differenza_importo = ($differenza_da_ordinare * $result['prezzo']);

                        $results[$numResult]['riopen'] = [];
                        $results[$numResult]['riopen']['differenza_da_ordinare'] = $differenza_da_ordinare;
                        $results[$numResult]['riopen']['differenza_importo'] = $differenza_importo;
                    }
                } // if($order_state_code=='RI-OPEN-VALIDATE' && $options['refer']=='CART')
            }
        } // if($results)

        if($debug) debug($results);

        return $results;
    }

    /*
     * implement
     *
     * estrae gli articoli associati ad un ordine ed evenuuali acquisti di tutti gli users
     * ArticlesOrders.article_id              = Articles.id
     * ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {

        $order_id = $orderResults->id;

        $order_state_code = $orderResults->state_code;

        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];
        $where['ArticlesOrders'] = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                             // $this->getAlias().'.article_id' => 142,
                              $this->getAlias().'.order_id' => $order_id,
                              $this->getAlias().'.stato != ' => 'N'],
                              $where['ArticlesOrders']);

        switch ($order_state_code) {
            case 'RI-OPEN-VALIDATE':
                $where['ArticlesOrders'] += [$this->getAlias().'.pezzi_confezione > ' => 1];
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
            }
        } // if($results)

        $results = new ApiArticleOrderDecorator($user, $results, $orderResults);
        $results = $results->results;

        if($debug) debug($results);

        return $results;
    }

    /*
     * implement
     */
    public function getCartsByOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        return parent::getCartsByOrder($user, $organization_id, $order, $where, $options, $debug);
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
        if(!isset($where['Carts']))
           $where['Carts'] = [];

        $contains = ['Articles' => ['conditions' => $where['Articles']],
                    'Carts' => [
                        'conditions' => $where['Carts'],
                        'Users']
        ];

        if(isset($where['ArticlesArticlesTypes']))
            $contains['Articles'] += ['ArticlesArticlesTypes' => ['conditions' => $where['ArticlesArticlesTypes']]];

        $results = $this->find()
                        ->contain($contains)
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();
   // debug($where);
        if(isset($where['ArticlesArticlesTypes']) && count($results)>0) {
            $i = 0;
            $new_results = [];
            foreach($results as $numResult => $result) {
                if(!empty($result['article']['articles_articles_types'])) {
                    $new_results[$i] = $results[$numResult];
                    $i++;
                }
            }

            $results = [];
            $results = $new_results;
        }

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

        $where = [$this->getAlias().'.organization_id' => $organization_id,
                  $this->getAlias().'.order_id' => $order_id,
                  $this->getAlias().'.article_organization_id' => $article_organization_id,
                  $this->getAlias().'.article_id' => $article_id];
        // debug($where);

        $results = $this->find()
                        ->contain(['Articles'])
                        ->where($where)
                        ->first();
        // debug($results);
        return $results;
    }

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
        return $results;
    }

    public function getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug=false) {

        $this->_getOptions($options); // setta sort / limit / page

        $results = [];
        $resultsArticlesOrders = $this->find()
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        /*
                         * senza limite perche' sotto ritratto i dati
                         * ->limit($this->_limit)
                         * */
                        ->limit(1000)
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
                   $orderResults->order_type_id==Configure::read('Order.type.des_titolare')) {

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

    /*
     * dato una lista di articoli li associa all'ordine
     */
    public function addsByArticles($user, $organization_id, $order, $articles, $debug=false) {

        foreach($articles as $article) {

            $article_id = 0;
            isset($article['id']) ? $article_id = $article['id']: $article_id = $article['article_id'];

            $datas = [];
            $datas['organization_id'] = $organization_id;
            $datas['order_id'] = $order->id;
            $datas['article_organization_id'] = $order->owner_organization_id; // dati dell owner_ dell'articolo REFERENT / SUPPLIER / DES
            $datas['article_id'] = $article_id;
            $datas['name'] = $article['name'];
            if(isset($article['prezzo_']))
                $datas['prezzo'] = $this->convertImport($article['prezzo_']);
            else
                $datas['prezzo'] = $article['prezzo'];
            $datas['pezzi_confezione'] = $article['pezzi_confezione'];
            $datas['qta_minima'] = $article['qta_minima'];
            $datas['qta_massima'] = $article['qta_massima'];
            $datas['qta_minima_order'] = $article['qta_minima_order'];
            $datas['qta_massima_order'] = $article['qta_massima_order'];
            $datas['qta_multipli'] = $article['qta_multipli'];
            $datas['alert_to_qta'] = $article['alert_to_qta'];

            /*
            * key
            *
            * article_organization_id / article_id
            *      dati dell owner_ dell'articolo REFERENT / SUPPLIER / DES / PACT
            */
            $ids = [];
            $ids['organization_id'] = $organization_id;
            $ids['order_id'] = $order->id;
            $ids['article_organization_id'] = $order->owner_organization_id;
            $ids['article_id'] = $article_id;

            $articlesOrder = $this->getByIds($user, $organization_id, $ids, $debug);
            if(empty($articlesOrder)) {

                $datas['send_mail'] = 'N';
                $datas['qta_cart'] = "0";
                $datas['flag_bookmarks'] = 'N';
                $datas['stato'] = 'Y';

                $articlesOrder = $this->newEntity();
            }

            /*
            * workaround
            */
            $articlesOrder->organization_id = $organization_id;
            $articlesOrder->order_id = $order->id;
            $articlesOrder->article_organization_id = $order->owner_organization_id;
            $articlesOrder->article_id = $article_id;
            $articlesOrder = $this->patchEntity($articlesOrder, $datas);
          //  debug($datas);
            if (!$this->save($articlesOrder)) {
                return $articlesOrder->getErrors();
            }
        } // loop articles

        /*
        * aggiorno stato ordine 'OPEN' // OPEN-NEXT
        */
        $event = new Event('OrderListener.setStatus', $this, ['user' => $user, 'order' => $order]);
        $this->getEventManager()->dispatch($event);

        return true;
    }

    /*
     * dato una lista di ArticlesOrders li associa all'ordine
     * ordini che ereditano dal parent des / gas_groups)
     */
    public function addsByArticlesOrders($user, $organization_id, $order, $article_orders, $debug=false) {

        foreach($article_orders as $article_order) {

            $datas = [];
            $datas['organization_id'] = $organization_id;
            $datas['order_id'] = $order->id;
            $datas['article_organization_id'] = $article_order->article_organization_id; // dati dell owner_ dell'articolo REFERENT / SUPPLIER / DES
            $datas['article_id'] = $article_order->article_id;
            $datas['name'] = $article_order->name;
            if(isset($article_order->prezzo_))
                $datas['prezzo'] = $this->convertImport($article_order->prezzo_);
            else
                $datas['prezzo'] = $article_order->prezzo;
            $datas['pezzi_confezione'] = $article_order->pezzi_confezione;
            $datas['qta_minima'] = $article_order->qta_minima;
            $datas['qta_massima'] = $article_order->qta_massima;
            $datas['qta_minima_order'] = $article_order->qta_minima_order;
            $datas['qta_massima_order'] = $article_order->qta_massima_order;
            $datas['qta_multipli'] = $article_order->qta_multipli;
            $datas['alert_to_qta'] = $article_order->alert_to_qta;

            /*
            * key
            *
            * article_organization_id / article_id
            *      dati dell owner_ dell'articolo REFERENT / SUPPLIER / DES / PACT
            */
            $ids = [];
            $ids['organization_id'] = $organization_id;
            $ids['order_id'] = $order->id;
            $ids['article_organization_id'] = $article_order->article_organization_id;
            $ids['article_id'] = $article_order->article_id;

            $articlesOrder = $this->getByIds($user, $organization_id, $ids, $debug);
            if(empty($articlesOrder)) {

                $datas['send_mail'] = 'N';
                $datas['qta_cart'] = "0";
                $datas['flag_bookmarks'] = 'N';
                $datas['stato'] = 'Y';

                $articlesOrder = $this->newEntity();
            }

            $articlesOrder = $this->patchEntity($articlesOrder, $datas);
          //  debug($datas);

            /*
            * workaround
            */
            $articlesOrder->organization_id = $organization_id;
            $articlesOrder->order_id = $order->id;
            $articlesOrder->article_organization_id = $article_order->article_organization_id;
            $articlesOrder->article_id = $article_order->article_id;
            if (!$this->save($articlesOrder)) {
                return $articlesOrder->getErrors();
            }
        } // loop article_orders

        /*
        * aggiorno stato ordine 'OPEN' // OPEN-NEXT
        */
        $event = new Event('OrderListener.setStatus', $this, ['user' => $user, 'order' => $order]);
        $this->eventManager()->dispatch($event);

        return true;
    }

    protected function _getOptions($options) {
        isset($options['limit']) && !empty($options['limit']) ? $this->_limit = $options['limit']: $this->_limit = Configure::read('sql.limit');
        isset($options['page']) && !empty($options['page']) ? $this->_page = $options['page']: $this->_page = 1;

        isset($options['sort']) && !empty($options['sort']) ? $this->_sort = $options['sort']: $this->_sort = ['Articles.codice', $this->getAlias().'.name'];

        if($this->_sort=='name-desc') $this->_sort = [$this->getAlias().'.name' => 'desc'];
        else if($this->_sort=='name-asc') $this->_sort = [$this->getAlias().'.name' => 'asc'];
        else if($this->_sort=='codice-desc') $this->_sort = ['Articles.codice' => 'desc'];
        else if($this->_sort=='codice-asc') $this->_sort = ['Articles.codice' => 'asc'];
        else if($this->_sort=='prezzo-desc') $this->_sort = [$this->getAlias().'.prezzo' => 'desc'];
        else if($this->_sort=='prezzo-asc') $this->_sort = [$this->getAlias().'.prezzo' => 'asc'];
    }
}
