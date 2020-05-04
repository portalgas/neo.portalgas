<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StatCarts Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ArticleOrganizationsTable&\Cake\ORM\Association\BelongsTo $ArticleOrganizations
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 * @property \App\Model\Table\StatOrdersTable&\Cake\ORM\Association\BelongsTo $StatOrders
 *
 * @method \App\Model\Entity\StatCart get($primaryKey, $options = [])
 * @method \App\Model\Entity\StatCart newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StatCart[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StatCart|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatCart saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatCart patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StatCart[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StatCart findOrCreate($search, callable $callback = null, $options = [])
 */
class StatCartsTable extends Table
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

        $this->setTable('k_stat_carts');
        $this->setDisplayField('organization_id');
        $this->setPrimaryKey(['organization_id', 'user_id', 'article_id', 'stat_order_id']);

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'foreignKey' => 'article_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => 'article_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('StatOrders', [
            'foreignKey' => 'stat_order_id',
            'joinType' => 'INNER',
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
            ->requirePresence('qta', 'create')
            ->notEmptyString('qta');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

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
        $rules->add($rules->existsIn(['article_organization_id'], 'ArticleOrganizations'));
        $rules->add($rules->existsIn(['article_id'], 'Articles'));
        $rules->add($rules->existsIn(['stat_order_id'], 'StatOrders'));

        return $rules;
    }
}
