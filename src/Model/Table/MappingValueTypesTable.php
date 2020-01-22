<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MappingValueTypes Model
 *
 * @property \App\Model\Table\MappingsTable&\Cake\ORM\Association\HasMany $Mappings
 *
 * @method \App\Model\Entity\MappingValueType get($primaryKey, $options = [])
 * @method \App\Model\Entity\MappingValueType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MappingValueType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MappingValueType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MappingValueType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MappingValueType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MappingValueType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MappingValueType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MappingValueTypesTable extends Table
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

        $this->setTable('mapping_value_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Mappings', [
            'foreignKey' => 'mapping_value_type_id',
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
            ->maxLength('code', 25)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('match')
            ->requirePresence('match', 'create')
            ->notEmptyString('match');

        $validator
            ->scalar('factory_force_value')
            ->maxLength('factory_force_value', 45)
            ->allowEmptyString('factory_force_value');

        $validator
            ->boolean('is_force_value')
            ->notEmptyString('is_force_value');

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
