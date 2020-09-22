<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Queues Model
 *
 * @property \App\Model\Table\QueueMappingTypesTable&\Cake\ORM\Association\BelongsTo $QueueMappingTypes
 * @property \App\Model\Table\MasterScopesTable&\Cake\ORM\Association\BelongsTo $MasterScopes
 * @property \App\Model\Table\SlaveScopesTable&\Cake\ORM\Association\BelongsTo $SlaveScopes
 * @property \App\Model\Table\MappingsTable&\Cake\ORM\Association\HasMany $Mappings
 * @property \App\Model\Table\QueueLogsTable&\Cake\ORM\Association\HasMany $QueueLogs
 * @property \App\Model\Table\QueueTablesTable&\Cake\ORM\Association\HasMany $QueueTables
 *
 * @method \App\Model\Entity\Queue get($primaryKey, $options = [])
 * @method \App\Model\Entity\Queue newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Queue[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Queue|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Queue saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Queue patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Queue[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Queue findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QueuesTable extends Table
{
    const LOG_TYPE_NO = 'Disabilitato';
    const LOG_TYPE_DATABASE = 'DataBase';
    const LOG_TYPE_FILE = 'File di log';
    const LOG_TYPE_SHELL = 'Shell';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('queues');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'log_type' => [
                'strategy' => 'const',
                'prefix' => 'LOG_TYPE'
            ]
        ]]);

        $this->belongsTo('QueueMappingTypes', [
            'foreignKey' => 'queue_mapping_type_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MasterScopes', [
            'foreignKey' => 'master_scope_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SlaveScopes', [
            'foreignKey' => 'slave_scope_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Mappings', [
            'foreignKey' => 'queue_id'
        ]);
        $this->hasMany('QueueLogs', [
            'foreignKey' => 'queue_id'
        ]);
        $this->hasMany('QueueTables', [
            'foreignKey' => 'queue_id'
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
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('component')
            ->maxLength('component', 100)
            ->requirePresence('component', 'create')
            ->notEmptyString('component');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

       $validator
            ->scalar('svale_db_datasource')
            ->maxLength('svale_db_datasource', 45)
            ->allowEmptyString('svale_db_datasource');

       $validator
            ->scalar('master_db_datasource')
            ->maxLength('master_db_datasource', 45)
            ->allowEmptyString('master_db_datasource');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->existsIn(['queue_mapping_type_id'], 'QueueMappingTypes'));
        $rules->add($rules->existsIn(['master_scope_id'], 'MasterScopes'));
        $rules->add($rules->existsIn(['slave_scope_id'], 'SlaveScopes'));

        return $rules;
    }

    public function findByCode($code)
    {
        if (empty($code)) {
            return null;
        }

        $queue = $this->find()  
                        ->where([
                            'Queues.code' => $code,
                            'Queues.is_active' => 1
                        ])
                        ->contain([
                            'MasterScopes',
                            'SlaveScopes',
                            'QueueMappingTypes',
                            'QueueTables' => ['sort' => ['QueueTables.sort' => 'asc'], 'Tables']])
                        ->first();        

        return $queue;
    } 

    public function getDataSourceMaster($queue)
    {
        $datasource = '';
        if(empty($queue->master_db_datasource))
            $datasource = $queue->master_scope->db_datasource;
        else
            $datasource = $queue->master_db_datasource;

        return $datasource;
    }

    public function getDataSourceSlave($queue)
    {
        $datasource = '';
        if(empty($queue->slave_db_datasource))
            $datasource = $queue->slave_scope->db_datasource;
        else
            $datasource = $queue->slave_db_datasource;

        return $datasource;
    }

    /*
     * ottengo il namespace della tabella slave (TableRegistry) 
     * ex PortAlGas/Articoli
     */
    public function getNamespaceTableSlave($queue, $slave_entity) {
        $namespace_table_slave = '';
        if(!empty($queue->slave_scope->namespace))
            $namespace_table_slave = ucfirst($queue->slave_scope->namespace).'/'.$slave_entity;
        else
            $namespace_table_slave = $slave_entity;

        return $namespace_table_slave;
    }
}
