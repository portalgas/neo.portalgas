<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SummaryOrders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DeliveriesTable&\Cake\ORM\Association\BelongsTo $Deliveries
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\SummaryOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\SummaryOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SummaryOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrder findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SummaryOrdersTable extends Table
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

        $this->setTable('k_summary_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['id', 'organization_id', 'user_id', 'order_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => 'delivery_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
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
            ->numeric('importo')
            ->notEmptyString('importo');

        $validator
            ->numeric('importo_pagato')
            ->notEmptyString('importo_pagato');

        $validator
            ->scalar('saldato_a')
            ->allowEmptyString('saldato_a');

        $validator
            ->scalar('modalita')
            ->notEmptyString('modalita');

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
        $rules->add($rules->existsIn(['delivery_id'], 'Deliveries'));
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }
}
