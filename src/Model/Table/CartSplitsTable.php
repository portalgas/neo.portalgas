<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CartSplitsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cart_splits');
        $this->setDisplayField('organization_id');
        $this->setPrimaryKey(['id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticlesOrders', [
            'foreignKey' => ['organization_id', 'order_id', 'article_organization_id', 'article_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Carts', [
            'foreignKey' => ['organization_id', 'order_id', 'article_organization_id', 'article_id', 'user_id'],
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
            ->integer('qta')
            ->notEmptyString('qta');


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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));
        $rules->add($rules->existsIn(['article_organization_id', 'article_id'], 'Articles'));
        $rules->add($rules->existsIn(['organization_id', 'order_id', 'article_organization_id', 'article_id'], 'ArticlesOrders'));
        $rules->add($rules->existsIn(['organization_id', 'order_id', 'article_organization_id', 'article_id', 'user_id'], 'Carts'));

        return $rules;
    }

}