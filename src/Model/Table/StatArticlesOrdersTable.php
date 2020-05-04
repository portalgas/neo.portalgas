<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StatArticlesOrders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\StatOrdersTable&\Cake\ORM\Association\BelongsTo $StatOrders
 * @property \App\Model\Table\ArticleOrganizationsTable&\Cake\ORM\Association\BelongsTo $ArticleOrganizations
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 *
 * @method \App\Model\Entity\StatArticlesOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\StatArticlesOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StatArticlesOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StatArticlesOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatArticlesOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatArticlesOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StatArticlesOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StatArticlesOrder findOrCreate($search, callable $callback = null, $options = [])
 */
class StatArticlesOrdersTable extends Table
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

        $this->setTable('k_stat_articles_orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'stat_order_id', 'article_id', 'article_organization_id']);

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('StatOrders', [
            'foreignKey' => 'stat_order_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('codice')
            ->maxLength('codice', 25)
            ->allowEmptyString('codice');

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
        $rules->add($rules->existsIn(['stat_order_id'], 'StatOrders'));
        $rules->add($rules->existsIn(['article_organization_id'], 'ArticleOrganizations'));
        $rules->add($rules->existsIn(['article_id'], 'Articles'));

        return $rules;
    }
}
