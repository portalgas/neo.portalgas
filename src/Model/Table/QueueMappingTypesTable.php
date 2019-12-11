<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QueueMappingTypes Model
 *
 * @property \App\Model\Table\QueuesTable&\Cake\ORM\Association\HasMany $Queues
 *
 * @method \App\Model\Entity\QueueMappingType get($primaryKey, $options = [])
 * @method \App\Model\Entity\QueueMappingType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\QueueMappingType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\QueueMappingType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueMappingType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueMappingType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\QueueMappingType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\QueueMappingType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QueueMappingTypesTable extends Table
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

        $this->setTable('queue_mapping_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Queues', [
            'foreignKey' => 'queue_mapping_type_id'
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
            ->scalar('paramConfigs')
            ->allowEmptyString('paramConfigs');

        $validator
            ->scalar('component')
            ->maxLength('component', 100)
            ->requirePresence('component', 'create')
            ->notEmptyString('component');
            
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
