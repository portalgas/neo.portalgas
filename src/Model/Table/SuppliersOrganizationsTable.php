<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SuppliersOrganizations Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SuppliersTable&\Cake\ORM\Association\BelongsTo $Suppliers
 * @property \App\Model\Table\CategorySuppliersTable&\Cake\ORM\Association\BelongsTo $CategorySuppliers
 * @property \App\Model\Table\OwnerOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerOrganizations
 * @property \App\Model\Table\OwnerSupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerSupplierOrganizations
 *
 * @method \App\Model\Entity\KSuppliersOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SuppliersOrganizationsTable extends Table
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

        $this->setTable('k_suppliers_organizations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CategorySuppliers', [
            'foreignKey' => 'category_supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('OwnerOrganizations', [
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('OwnerSupplierOrganizations', [
            'foreignKey' => 'owner_supplier_organization_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 225)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('frequenza')
            ->maxLength('frequenza', 50)
            ->allowEmptyString('frequenza');

        $validator
            ->scalar('owner_articles')
            ->notEmptyString('owner_articles');

        $validator
            ->scalar('can_view_orders')
            ->notEmptyString('can_view_orders');

        $validator
            ->scalar('can_view_orders_users')
            ->notEmptyString('can_view_orders_users');

        $validator
            ->scalar('can_promotions')
            ->notEmptyString('can_promotions');

        $validator
            ->scalar('mail_order_open')
            ->notEmptyString('mail_order_open');

        $validator
            ->scalar('mail_order_close')
            ->notEmptyString('mail_order_close');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

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
        $rules->add($rules->existsIn(['supplier_id'], 'Suppliers'));
        $rules->add($rules->existsIn(['category_supplier_id'], 'CategorySuppliers'));
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));
        $rules->add($rules->existsIn(['owner_supplier_organization_id'], 'OwnerSupplierOrganizations'));

        return $rules;
    }
}
