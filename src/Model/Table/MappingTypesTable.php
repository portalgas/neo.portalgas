<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MappingTypes Model
 *
 * @property \App\Model\Table\MappingsTable&\Cake\ORM\Association\HasMany $Mappings
 *
 * @method \App\Model\Entity\MappingType get($primaryKey, $options = [])
 * @method \App\Model\Entity\MappingType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MappingType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MappingType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MappingType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MappingType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MappingType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MappingType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MappingTypesTable extends Table
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

        $this->setTable('mapping_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Mappings', [
            'foreignKey' => 'mapping_type_id'
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
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->boolean('is_default_ini')
            ->notEmptyString('is_default_ini');

        $validator
            ->boolean('is_default_end')
            ->notEmptyString('is_default_end');

        $validator
            ->integer('sort')
            ->notEmptyString('sort');

        return $validator;
    }
}
