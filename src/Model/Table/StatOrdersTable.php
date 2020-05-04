<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StatOrders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 * @property \App\Model\Table\StatDeliveriesTable&\Cake\ORM\Association\BelongsTo $StatDeliveries
 *
 * @method \App\Model\Entity\StatOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\StatOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StatOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StatOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StatOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StatOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StatOrder findOrCreate($search, callable $callback = null, $options = [])
 */
class StatOrdersTable extends Table
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

        $this->setTable('k_stat_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SupplierOrganizations', [
            'foreignKey' => 'supplier_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('StatDeliveries', [
            'foreignKey' => 'stat_delivery_id',
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
            ->scalar('supplier_organization_name')
            ->maxLength('supplier_organization_name', 225)
            ->allowEmptyString('supplier_organization_name');

        $validator
            ->scalar('supplier_img1')
            ->maxLength('supplier_img1', 50)
            ->allowEmptyString('supplier_img1');

        $validator
            ->integer('stat_delivery_year')
            ->requirePresence('stat_delivery_year', 'create')
            ->notEmptyString('stat_delivery_year');

        $validator
            ->date('data_inizio')
            ->allowEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->allowEmptyDate('data_fine');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

        $validator
            ->numeric('tesoriere_fattura_importo')
            ->allowEmptyString('tesoriere_fattura_importo');

        $validator
            ->scalar('tesoriere_doc1')
            ->maxLength('tesoriere_doc1', 100)
            ->allowEmptyString('tesoriere_doc1');

        $validator
            ->date('tesoriere_data_pay')
            ->allowEmptyDate('tesoriere_data_pay');

        $validator
            ->numeric('tesoriere_importo_pay')
            ->allowEmptyString('tesoriere_importo_pay');

        $validator
            ->scalar('request_payment_num')
            ->maxLength('request_payment_num', 10)
            ->requirePresence('request_payment_num', 'create')
            ->notEmptyString('request_payment_num');

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
        $rules->add($rules->existsIn(['supplier_organization_id'], 'SupplierOrganizations'));
        $rules->add($rules->existsIn(['stat_delivery_id'], 'StatDeliveries'));

        return $rules;
    }
}
