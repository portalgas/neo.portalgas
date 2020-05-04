<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StatDeliveries Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 *
 * @method \App\Model\Entity\StatDelivery get($primaryKey, $options = [])
 * @method \App\Model\Entity\StatDelivery newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StatDelivery[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StatDelivery|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatDelivery saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatDelivery patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StatDelivery[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StatDelivery findOrCreate($search, callable $callback = null, $options = [])
 */
class StatDeliveriesTable extends Table
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

        $this->setTable('k_stat_deliveries');
        $this->setPrimaryKey(['organization_id', 'id']);
        $this->setPrimaryKey('id');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('luogo')
            ->maxLength('luogo', 156)
            ->requirePresence('luogo', 'create')
            ->notEmptyString('luogo');

        $validator
            ->date('data')
            ->allowEmptyDate('data');

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

        return $rules;
    }
}
