<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Mappings Model
 *
 * @property \App\Model\Table\QueuesTable&\Cake\ORM\Association\BelongsTo $Queues
 * @property \App\Model\Table\MasterScopesTable&\Cake\ORM\Association\BelongsTo $MasterScopes
 * @property \App\Model\Table\MasterTablesTable&\Cake\ORM\Association\BelongsTo $MasterTables
 * @property \App\Model\Table\SlaveScopesTable&\Cake\ORM\Association\BelongsTo $SlaveScopes
 * @property \App\Model\Table\SlaveTablesTable&\Cake\ORM\Association\BelongsTo $SlaveTables
 * @property \App\Model\Table\MappingTypesTable&\Cake\ORM\Association\BelongsTo $MappingTypes
 * @property \App\Model\Table\QueueTablesTable&\Cake\ORM\Association\BelongsTo $QueueTables
 *
 * @method \App\Model\Entity\Mapping get($primaryKey, $options = [])
 * @method \App\Model\Entity\Mapping newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Mapping[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mapping|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mapping saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mapping patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Mapping[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mapping findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MappingsTable extends Table
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

        $this->setTable('mappings');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Queues', [
            'foreignKey' => 'queue_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MasterScopes', [
            'foreignKey' => 'master_scope_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MasterTables', [
            'foreignKey' => 'master_table_id'
        ]);
        $this->belongsTo('SlaveScopes', [
            'foreignKey' => 'slave_scope_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SlaveTables', [
            'foreignKey' => 'slave_table_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MappingTypes', [
            'foreignKey' => 'mapping_type_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MappingValueTypes', [
            'foreignKey' => 'mapping_value_type_id',
            'joinType' => 'LEFT'
        ]);
        $this->belongsTo('QueueTables', [
            'foreignKey' => 'queue_table_id'
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
            ->scalar('name')
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->scalar('master_column')
            ->maxLength('master_column', 100)
            ->allowEmptyString('master_column');

        $validator
            ->scalar('master_json_path')
            ->maxLength('master_json_path', 255)
            ->allowEmptyString('master_json_path');
            
        $validator
            ->scalar('master_json_path_extra')
            ->maxLength('master_json_path_extra', 255)
            ->allowEmptyString('master_json_path_extra');
            
        $validator
            ->scalar('master_xml_xpath')
            ->maxLength('master_xml_xpath', 255)
            ->allowEmptyString('master_xml_xpath');

        $validator
            ->integer('master_csv_num_col')
            ->allowEmptyString('master_csv_num_col');

        $validator
            ->scalar('slave_column')
            ->maxLength('slave_column', 100)
            ->requirePresence('slave_column', 'create')
            ->notEmptyString('slave_column');

        $validator
            ->scalar('value')
            ->maxLength('value', 100)
            ->allowEmptyString('value');

        $validator
            ->scalar('value_default')
            ->maxLength('value_default', 100)
            ->allowEmptyString('value_default');

        $validator
            ->scalar('parameters')
            ->maxLength('parameters', 256)
            ->allowEmptyString('parameters');

        $validator
            ->boolean('is_required')
            ->notEmptyString('is_required');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->integer('sort')
            ->allowEmptyString('sort');

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
        $rules->add($rules->existsIn(['master_scope_id'], 'MasterScopes'));
        $rules->add($rules->existsIn(['master_table_id'], 'MasterTables'));
        $rules->add($rules->existsIn(['slave_scope_id'], 'SlaveScopes'));
        $rules->add($rules->existsIn(['slave_table_id'], 'SlaveTables'));
        $rules->add($rules->existsIn(['mapping_type_id'], 'MappingTypes'));
        $rules->add($rules->existsIn(['queue_table_id'], 'QueueTables'));
        $rules->add($rules->existsIn(['mapping_value_type_id'], 'MappingValueTypes'));

        return $rules;
    }
}