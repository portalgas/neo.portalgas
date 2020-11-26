<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QueueTables Model
 *
 * @property \App\Model\Table\QueuesTable&\Cake\ORM\Association\BelongsTo $Queues
 * @property \App\Model\Table\TablesTable&\Cake\ORM\Association\BelongsTo $Tables
 *
 * @method \App\Model\Entity\QueueTable get($primaryKey, $options = [])
 * @method \App\Model\Entity\QueueTable newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\QueueTable[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\QueueTable|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueTable saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QueueTable patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\QueueTable[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\QueueTable findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QueueTablesTable extends Table
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

        $this->setTable('queue_tables');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Queues', [
            'foreignKey' => 'queue_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Tables', [
            'foreignKey' => 'table_id',
            // devo essere in LEFT se no in mapping con 'contain' => ['QueueTables' => ['Tables']] non trova che non ha QueueTables
            'joinType' => 'LEFT'  
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
            ->scalar('before_save_first')
            ->maxLength('before_save_first', 100)
            ->allowEmptyString('before_save_first');

        $validator
            ->scalar('before_save')
            ->maxLength('before_save', 100)
            ->allowEmptyString('before_save');

        $validator
            ->integer('sort')
            ->notEmptyString('sort');

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
        $rules->add($rules->existsIn(['table_id'], 'Tables'));

        return $rules;
    }
}
