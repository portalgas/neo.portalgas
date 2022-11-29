<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KSuppliersOrganizationsReferents Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent get($primaryKey, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganizationsReferent findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SuppliersOrganizationsReferentsTable extends Table
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

        $this->setTable('k_suppliers_organizations_referents');
        $this->setDisplayField('organization_id');
        $this->setPrimaryKey(['organization_id', 'user_id', 'supplier_organization_id', 'group_id', 'type']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
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
            ->scalar('type')
            ->allowEmptyString('type', null, 'create');

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
        // $rules->add($rules->existsIn(['supplier_organization_id'], 'SuppliersOrganizations'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        // $rules->add($rules->existsIn(['group_id'], 'Groups'));

        return $rules;
    }

    public function gets($user, $where = []) {

        $where = ['SuppliersOrganizationsReferents.organization_id' => $user->organization->id,
                  'SuppliersOrganizationsReferents.user_id' => $user->id
               ];
        // debug($where);
        $results = $this->find()
                                ->where($where)
                                ->contain(['Users', 
                                          'SuppliersOrganizations' => ['Suppliers', 'CategoriesSuppliers']])
                                ->all();

        // debug($results);
        return $results;
    }

    public function getsList($user, $where = []) {

        $where = ['SuppliersOrganizationsReferents.organization_id' => $user->organization->id,
                  'SuppliersOrganizationsReferents.user_id' => $user->id
               ];
        // debug($where);
        $results = $this->find('list', [
                        'keyField' => function ($suppliers_organizations_referents) {
                            return $suppliers_organizations_referents->suppliers_organization->get('id');
                        },
                        'valueField' => function ($suppliers_organizations_referents) {
                            return $suppliers_organizations_referents->suppliers_organization->get('name');
                        }])
                        ->where($where)
                        ->contain(['SuppliersOrganizations'])
                        ->order(['Deliveries.data'])
                        ->all();

        // debug($results);
        return $results;
    }    
}
