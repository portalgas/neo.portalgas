<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BackupOrdersOrders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 * @property \App\Model\Table\OwnerOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerOrganizations
 * @property \App\Model\Table\OwnerSupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerSupplierOrganizations
 * @property \App\Model\Table\DeliveriesTable&\Cake\ORM\Association\BelongsTo $Deliveries
 * @property \App\Model\Table\ProdGasPromotionsTable&\Cake\ORM\Association\BelongsTo $ProdGasPromotions
 * @property \App\Model\Table\DesOrdersTable&\Cake\ORM\Association\BelongsTo $DesOrders
 *
 * @method \App\Model\Entity\BackupOrdersOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BackupOrdersOrder findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BackupOrdersOrdersTable extends Table
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

        $this->setTable('k_backup_orders_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SupplierOrganizations', [
            'foreignKey' => 'supplier_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OwnerOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OwnerSupplierOrganizations', [
            'className' => 'SupplierOrganizations',
            'foreignKey' => 'owner_supplier_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => 'delivery_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProdGasPromotions', [
            'foreignKey' => 'prod_gas_promotion_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DesOrders', [
            'foreignKey' => 'des_order_id',
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
            ->scalar('owner_articles')
            ->notEmptyString('owner_articles');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->requirePresence('data_fine', 'create')
            ->notEmptyDate('data_fine');

        $validator
            ->date('data_fine_validation')
            ->notEmptyDate('data_fine_validation');

        $validator
            ->date('data_incoming_order')
            ->notEmptyDate('data_incoming_order');

        $validator
            ->date('data_state_code_close')
            ->notEmptyDate('data_state_code_close');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('hasTrasport')
            ->notEmptyString('hasTrasport');

        $validator
            ->scalar('trasport_type')
            ->allowEmptyString('trasport_type');

        $validator
            ->numeric('trasport')
            ->notEmptyString('trasport');

        $validator
            ->scalar('hasCostMore')
            ->notEmptyString('hasCostMore');

        $validator
            ->scalar('cost_more_type')
            ->allowEmptyString('cost_more_type');

        $validator
            ->numeric('cost_more')
            ->notEmptyString('cost_more');

        $validator
            ->scalar('hasCostLess')
            ->notEmptyString('hasCostLess');

        $validator
            ->scalar('cost_less_type')
            ->allowEmptyString('cost_less_type');

        $validator
            ->numeric('cost_less')
            ->notEmptyString('cost_less');

        $validator
            ->scalar('typeGest')
            ->allowEmptyString('typeGest');

        $validator
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->requirePresence('state_code', 'create')
            ->notEmptyString('state_code');

        $validator
            ->scalar('mail_open_send')
            ->notEmptyString('mail_open_send');

        $validator
            ->dateTime('mail_open_data')
            ->notEmptyDateTime('mail_open_data');

        $validator
            ->dateTime('mail_close_data')
            ->notEmptyDateTime('mail_close_data');

        $validator
            ->scalar('mail_open_testo')
            ->requirePresence('mail_open_testo', 'create')
            ->notEmptyString('mail_open_testo');

        $validator
            ->scalar('type_draw')
            ->notEmptyString('type_draw');

        $validator
            ->numeric('tot_importo')
            ->requirePresence('tot_importo', 'create')
            ->notEmptyString('tot_importo');

        $validator
            ->integer('qta_massima')
            ->requirePresence('qta_massima', 'create')
            ->notEmptyString('qta_massima');

        $validator
            ->scalar('qta_massima_um')
            ->allowEmptyString('qta_massima_um');

        $validator
            ->scalar('send_mail_qta_massima')
            ->notEmptyString('send_mail_qta_massima');

        $validator
            ->integer('importo_massimo')
            ->requirePresence('importo_massimo', 'create')
            ->notEmptyString('importo_massimo');

        $validator
            ->scalar('send_mail_importo_massimo')
            ->notEmptyString('send_mail_importo_massimo');

        $validator
            ->scalar('tesoriere_nota')
            ->allowEmptyString('tesoriere_nota');

        $validator
            ->numeric('tesoriere_fattura_importo')
            ->notEmptyString('tesoriere_fattura_importo');

        $validator
            ->scalar('tesoriere_doc1')
            ->maxLength('tesoriere_doc1', 256)
            ->allowEmptyString('tesoriere_doc1');

        $validator
            ->date('tesoriere_data_pay')
            ->notEmptyDate('tesoriere_data_pay');

        $validator
            ->numeric('tesoriere_importo_pay')
            ->notEmptyString('tesoriere_importo_pay');

        $validator
            ->scalar('tesoriere_stato_pay')
            ->notEmptyString('tesoriere_stato_pay');

        $validator
            ->scalar('inviato_al_tesoriere_da')
            ->notEmptyString('inviato_al_tesoriere_da');

        $validator
            ->scalar('isVisibleFrontEnd')
            ->notEmptyString('isVisibleFrontEnd');

        $validator
            ->scalar('isVisibleBackOffice')
            ->notEmptyString('isVisibleBackOffice');

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
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));
        $rules->add($rules->existsIn(['owner_supplier_organization_id'], 'OwnerSupplierOrganizations'));
        $rules->add($rules->existsIn(['delivery_id'], 'Deliveries'));
        $rules->add($rules->existsIn(['prod_gas_promotion_id'], 'ProdGasPromotions'));
        $rules->add($rules->existsIn(['des_order_id'], 'DesOrders'));

        return $rules;
    }
}
