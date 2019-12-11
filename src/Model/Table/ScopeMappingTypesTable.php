<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ScopeMappingTypes Model
 *
 * @property \App\Model\Table\ScopesTable&\Cake\ORM\Association\HasMany $Scopes
 *
 * @method \App\Model\Entity\ScopeMappingType get($primaryKey, $options = [])
 * @method \App\Model\Entity\ScopeMappingType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ScopeMappingType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ScopeMappingType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ScopeMappingType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ScopeMappingType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ScopeMappingType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ScopeMappingType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ScopeMappingTypesTable extends Table
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

        $this->setTable('scope_mapping_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Scopes', [
            'foreignKey' => 'scope_mapping_type_id'
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
            ->scalar('code')
            ->maxLength('code', 45)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->integer('sort')
            ->notEmptyString('sort');

        return $validator;
    }
}
