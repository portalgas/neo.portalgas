<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QueueLogs Model
 *
 * @property \App\Model\Table\QueuesTable&\Cake\ORM\Association\BelongsTo $Queues
 *
 * @method \App\Model\Entity\QueueLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\QueueLog newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\QueueLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\QueueLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\QueueLog[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\QueueLog findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QueueLogsTable extends Table
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

        $this->setTable('queue_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Queues', [
            'foreignKey' => 'queue_id',
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
            ->scalar('uuid')
            ->maxLength('uuid', 25)
            ->requirePresence('uuid', 'create')
            ->notEmptyString('uuid');

        $validator
            ->scalar('message')
            ->allowEmptyString('message');

        $validator
            ->scalar('log')
            ->allowEmptyString('log');

        $validator
            ->scalar('level')
            ->maxLength('level', 10)
            ->requirePresence('level', 'create')
            ->notEmptyString('level');

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
        $rules->add($rules->existsIn(['queue_id'], 'Queues'));

        return $rules;
    }
}
