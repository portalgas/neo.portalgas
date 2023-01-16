<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DesOrganizations Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 *
 * @method \App\Model\Entity\DesOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\DesOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DesOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DesOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrganization findOrCreate($search, callable $callback = null, $options = [])
 */
class DesOrganizationsTable extends Table
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

        $this->setTable('k_des_organizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
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
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));

        return $rules;
    }
}
