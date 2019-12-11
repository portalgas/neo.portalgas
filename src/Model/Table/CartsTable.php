<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KCarts Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 * @property \App\Model\Table\ArticleOrganizationsTable&\Cake\ORM\Association\BelongsTo $ArticleOrganizations
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 *
 * @method \App\Model\Entity\KCart get($primaryKey, $options = [])
 * @method \App\Model\Entity\KCart newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KCart[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KCart|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KCart saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KCart patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KCart[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KCart findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CartsTable extends Table
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

        $this->setTable('k_carts');
        $this->setDisplayField('organization_id');
        $this->setPrimaryKey(['organization_id', 'user_id', 'order_id', 'article_organization_id', 'article_id']);

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
            'foreignKey' => 'order_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'foreignKey' => 'article_organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => 'article_id',
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

        $validator
            ->scalar('deleteToReferent')
            ->notEmptyString('deleteToReferent');

        $validator
            ->integer('qta_forzato')
            ->notEmptyString('qta_forzato');

        $validator
            ->numeric('importo_forzato')
            ->greaterThanOrEqual('importo_forzato', 0)
            ->notEmptyString('importo_forzato');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('inStoreroom')
            ->notEmptyString('inStoreroom');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

        $validator
            ->dateTime('date')
            ->notEmptyDateTime('date');

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
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['article_organization_id'], 'ArticleOrganizations'));
        $rules->add($rules->existsIn(['article_id'], 'Articles'));

        return $rules;
    }
}
