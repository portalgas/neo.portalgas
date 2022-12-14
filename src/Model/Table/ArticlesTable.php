<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Traits;

class ArticlesTable extends Table
{
    use Traits\SqlTrait;
        
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
        $this->setPrimaryKey(['id', 'organization_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CategoriesArticles', [
            'foreignKey' => 'category_article_id',
            'joinType' => 'INNER'
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

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
            ->notEmptyString('prezzo');

        $validator
            ->numeric('qta')
            ->notEmptyString('qta');

        $validator
            ->scalar('um')
            ->requirePresence('um', 'create')
            ->notEmptyString('um');

        $validator
            ->scalar('um_riferimento')
            ->requirePresence('um_riferimento', 'create')
            ->notEmptyString('um_riferimento');

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
            ->scalar('bio')
            ->requirePresence('bio', 'create')
            ->notEmptyString('bio');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 50)
            ->allowEmptyString('img1');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

        $validator
            ->scalar('flag_presente_articlesorders')
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
}
