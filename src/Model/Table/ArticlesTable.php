<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Decorator\CartDecorator;
use App\Decorator\ApiArticleOrderDecorator;
use App\Traits;

class ArticlesTable extends Table
{
    use Traits\SqlTrait;

    const UM_PZ = 'PZ';
    const UM_GR = 'GR';
    const UM_HG = 'HG';
    const UM_KG = 'KG';
    const UM_ML = 'ML';
    const UM_DL = 'DL';
    const UM_LT = 'LT';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_articles');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'id']);

        $this->addBehavior('Timestamp');
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'um' => [
                'strategy' => 'const',
                'prefix' => 'UM'
            ],
        ]]);

        $this->addBehavior('Burzum/Imagine.Imagine');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            'joinType' => 'INNER'
        ]);
        // utilizzato in elenco articoli
        $this->belongsTo('OwnerSupplierOrganizations', [
            // campi in Articles
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            // campi in SupplierOrganizations
            'bindingKey' => ['owner_organization_id', 'owner_supplier_organization_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CategoriesArticles', [
            'foreignKey' => 'category_article_id',
            'joinType' => 'LEFT'
        ]);
        $this->belongsTo('Parent', [
            'className' => 'Articles',
            'foreignKey' => 'parent_id',
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
            ->nonNegativeInteger('id');

        $validator
            ->requirePresence('name', 'create')
            ->maxLength('name', 255)
            ->notEmptyString('name');

        $validator
            ->scalar('codice')
            ->maxLength('codice', 25)
            ->allowEmptyString('codice');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('ingredienti')
            ->allowEmptyString('ingredienti');

        $validator
            ->numeric('prezzo')
            ->greaterThan('prezzo', 0)
            ->notEmptyString('prezzo');

        $validator
            ->numeric('qta')
            ->greaterThan('qta', 0)
            ->notEmptyString('qta');

        $validator
            ->scalar('um')
            ->requirePresence('um', 'create')
            ->inList('um', ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'], "Valore consentito PZ, GR, HG, KG, ML, DL, LT")
            ->notEmptyString('um');

        $validator
            ->scalar('um_riferimento')
            ->requirePresence('um_riferimento', 'create')
            ->inList('um_riferimento', ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'], "Valore consentito PZ, GR, HG, KG, ML, DL, LT")
            ->notEmptyString('um_riferimento');

        $validator
            ->integer('pezzi_confezione')
            ->requirePresence('pezzi_confezione', 'create')
            ->greaterThanOrEqual('pezzi_confezione', 1)
            ->notEmptyString('pezzi_confezione');

        $validator
            ->integer('qta_minima')
            ->requirePresence('qta_minima', 'create')
            ->greaterThanOrEqual('qta_minima', 1)
            ->notEmptyString('qta_minima');

        $validator
            ->integer('qta_massima')
            ->requirePresence('qta_massima', 'create')
            ->greaterThanOrEqual('qta_massima', 0)
            ->notEmptyString('qta_massima');

        $validator
            ->integer('qta_minima_order')
            ->greaterThanOrEqual('qta_minima_order', 0)
            ->notEmptyString('qta_minima_order');

        $validator
            ->integer('qta_massima_order')
            ->requirePresence('qta_massima_order', 'create')
            ->greaterThanOrEqual('qta_massima_order', 0)
            ->notEmptyString('qta_massima_order');

        $validator
            ->integer('qta_multipli')
            ->requirePresence('qta_multipli', 'create')
            ->greaterThanOrEqual('qta_multipli', 1)
            ->notEmptyString('qta_multipli');

        $validator
            ->integer('alert_to_qta')
            ->requirePresence('alert_to_qta', 'create')
            ->greaterThanOrEqual('alert_to_qta', 0)
            ->notEmptyString('alert_to_qta');

        $validator
            ->scalar('bio')
            ->requirePresence('bio', 'create')
            ->inList('bio', ['Y', 'N'], "Valore consentito si, no")
            ->notEmptyString('bio');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 50)
            ->allowEmptyString('img1');

        $validator
            ->scalar('stato')
            ->inList('stato', ['Y', 'N'], "Valore consentito si, no")
            ->notEmptyString('stato');

        $validator
            ->scalar('flag_presente_articlesorders')
            ->inList('flag_presente_articlesorders', ['Y', 'N'], "Valore consentito si, no")
            ->notEmptyString('flag_presente_articlesorders');

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
        $rules->add($rules->existsIn(['organization_id', 'supplier_organization_id'], 'SuppliersOrganizations'));
        /*
         * disabilita perche' all'insert e' 0
        $rules->add($rules->existsIn(['category_article_id'], 'CategoriesArticles'));
        */

        return $rules;
    }

    public function gets($user, $where, $order=[]) {

        if(empty($order))
            $order = ['Articles.name asc'];

        $articles = $this->find()
                        ->where($where)
                        ->order($order)
                        ->contain(['SuppliersOrganizations' => ['Suppliers', 'OwnerOrganizations', 'OwnerSupplierOrganizations']])
                        ->all();
        // debug($articles);
        return $articles;
    }

    /*
     * articoli in base all'ordine: ordine erediata own del produttore
     * per sapere chi gestisce il listino articoli
     */
    public function getsToArticleOrders($user, $organization_id, $supplier_organization_id, $where=[], $debug = false) {

        /*
         * ricerco chi gestisce il listino articoli del produttore del GAS
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $ownArticles = $suppliersOrganizationsTable->getOwnArticles($user, $organization_id, $supplier_organization_id, $debug);

        if(isset($where['Articles']))
            $where = array_merge(['Articles.organization_id' => $ownArticles->owner_organization_id,
                                'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                                'Articles.stato' => 'Y',
                                'Articles.flag_presente_articlesorders' => 'Y'], $where['Articles']);
        else
            $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                    'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                    'Articles.stato' => 'Y',
                    'Articles.flag_presente_articlesorders' => 'Y'];
        if($debug) debug($where);

        $results = $this->gets($user, $where);

        return $results;
    }

    /*
     * articoli in base al produttore: in base al own del produttore
     * so chi gestisce il listino articoli
     */
    public function getsToArticleSupplierOrganization($user, $organization_id, $supplier_organization_id, $where=[], $debug = false) {

        /*
         * ricerco chi gestisce il listino articoli del produttore del GAS
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $ownArticles = $suppliersOrganizationsTable->getOwnArticles($user, $organization_id, $supplier_organization_id, $debug);

        if(isset($where['Articles']))
            $where = array_merge(['Articles.organization_id' => $ownArticles->owner_organization_id,
                                'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                                ], $where['Articles']);
        else
            $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                    'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                    ];
        if($debug) debug($where);

        $results = $this->gets($user, $where);

        return $results;
    }

    /*
     * dato un articolo controllo eventuali acquisti
     *  se associato non posso eliminarlo
     *
     * article_organization_id = puo' essere diverso dal GAS perche' e' chi gestisce l'articolo
     */
    public function getArticleInCarts($user, $organization_id, $article_organization_id, $article_id, $where=[], $orders=[], $debug = false) {

        $where = ['Carts.organization_id' => $organization_id,
                  'Carts.article_organization_id' => $organization_id,
                  'Carts.article_id' => $article_id];

        if($debug) debug($where);

        if(empty($orders))
            $orders = ['Deliveries.data asc'];

        $cartsTable = TableRegistry::get('Carts');
        $carts = $cartsTable->find()
                        ->where($where)
                        ->order($orders)
                        ->contain([
                            'Articles',
                            'ArticlesOrders',
                            'Orders' => [
                                'Deliveries',
                                'SuppliersOrganizations' => ['Suppliers', 'OwnerOrganizations', 'OwnerSupplierOrganizations']
                            ],
                            'Users'])
                        ->all();

        $results = [];
        foreach($carts as $numResult => $cart) {
            $cart2 = new CartDecorator($user, $cart);
            $results[$numResult] = $cart2->results;
        }
        return $results;
    }

    /*
     * dato un articolo controllo se associato ad evenuali ordini
     * article_organization_id = puo' essere diverso dal GAS perche' e' chi gestisce l'articolo
     */
    public function getArticleInOrders($user, $organization_id, $article_organization_id, $article_id, $where=[], $orders=[], $debug = false) {

        $where_orders = [];
        if(isset($where['Orders']))
            $where_orders = $where['Orders'];

        $where = ['ArticlesOrders.organization_id' => $organization_id,
                  'ArticlesOrders.article_organization_id' => $article_organization_id,
                  'ArticlesOrders.article_id' => $article_id];

        if($debug) debug($where);

        if(empty($orders))
            $orders = ['Deliveries.data asc'];

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articles_orders = $articlesOrdersTable->find()
                        ->where($where)
                        ->order($orders)
                        ->contain([
                            'Articles',
                            'Orders' => [
                                'conditions' => $where_orders,
                                'Deliveries',
                                'SuppliersOrganizations' => ['Suppliers']
                            ]])
                        ->all();

        return $articles_orders;
    }
}
