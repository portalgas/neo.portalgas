<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DesSuppliersReferents Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\DesSuppliersTable&\Cake\ORM\Association\BelongsTo $DesSuppliers
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \App\Model\Entity\DesSuppliersReferent get($primaryKey, $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DesSuppliersReferent findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DesSuppliersReferentsTable extends Table
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

        $this->setTable('k_des_suppliers_referents');
        $this->setDisplayField('des_id');
        $this->setPrimaryKey(['des_id', 'des_supplier_id', 'organization_id', 'user_id', 'group_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DesSuppliers', [
            'foreignKey' => 'des_supplier_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
        ]);
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
        $rules->add($rules->existsIn(['des_supplier_id'], 'DesSuppliers'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));

        return $rules;
    }
}
