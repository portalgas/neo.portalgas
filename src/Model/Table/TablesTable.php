<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tables Model
 *
 * @property \App\Model\Table\ScopesTable&\Cake\ORM\Association\BelongsTo $Scopes
 * @property \App\Model\Table\QueueTablesTable&\Cake\ORM\Association\HasMany $QueueTables
 *
 * @method \App\Model\Entity\Table get($primaryKey, $options = [])
 * @method \App\Model\Entity\Table newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Table[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Table|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Table saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Table patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Table[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Table findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TablesTable extends Table
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

        $this->setTable('tables');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Scopes', [
            'foreignKey' => 'scope_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('QueueTables', [
            'foreignKey' => 'table_id'
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
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('table_name')
            ->maxLength('table_name', 100)
            ->requirePresence('table_name', 'create')
            ->notEmptyString('table_name');

        $validator
            ->scalar('entity')
            ->maxLength('entity', 100)
            ->requirePresence('entity', 'create')
            ->notEmptyString('entity');

        $validator
            ->scalar('where_key')
            ->maxLength('where_key', 45)
            ->allowEmptyString('where_key');

        $validator
            ->scalar('update_key')
            ->maxLength('update_key', 100)
            ->allowEmptyString('update_key');

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
        $rules->add($rules->existsIn(['scope_id'], 'Scopes'));

        return $rules;
    }

    public function getList() {

        return $this->find('list', 
            ['conditions' => ['Tables.is_active' => true], 
            'limit' => 200, 
            'keyField' => 'id', 
            'valueField' => function ($masterTables) {
                return $masterTables->name.' ('.$masterTables->scope->name.')';
            }])
            ->contain(['Scopes' => ['conditions' => ['Scopes.is_active' => true]]]);
    }    
}
