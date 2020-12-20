<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DesOrdersOrganizations Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\DesOrdersTable&\Cake\ORM\Association\BelongsTo $DesOrders
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\DesOrdersOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DesOrdersOrganizationsTable extends Table
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

        $this->setTable('k_des_orders_organizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DesOrders', [
            'foreignKey' => 'des_order_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('luogo')
            ->maxLength('luogo', 225)
            ->requirePresence('luogo', 'create')
            ->notEmptyString('luogo');

        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        $validator
            ->time('orario')
            ->requirePresence('orario', 'create')
            ->notEmptyTime('orario');

        $validator
            ->scalar('contatto_nominativo')
            ->maxLength('contatto_nominativo', 150)
            ->requirePresence('contatto_nominativo', 'create')
            ->notEmptyString('contatto_nominativo');

        $validator
            ->scalar('contatto_telefono')
            ->maxLength('contatto_telefono', 20)
            ->requirePresence('contatto_telefono', 'create')
            ->notEmptyString('contatto_telefono');

        $validator
            ->scalar('contatto_mail')
            ->maxLength('contatto_mail', 100)
            ->requirePresence('contatto_mail', 'create')
            ->notEmptyString('contatto_mail');

        $validator
            ->scalar('nota')
            ->requirePresence('nota', 'create')
            ->notEmptyString('nota');

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
        $rules->add($rules->existsIn(['des_id'], 'Des'));
        $rules->add($rules->existsIn(['des_order_id'], 'DesOrders'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));

        return $rules;
    }
}
