<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Scopes Model
 *
 * @property \App\Model\Table\TablesTable&\Cake\ORM\Association\HasMany $Tables
 *
 * @method \App\Model\Entity\Scope get($primaryKey, $options = [])
 * @method \App\Model\Entity\Scope newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Scope[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Scope|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Scope saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Scope patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Scope[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Scope findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SlaveScopesTable extends Table
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

        $this->setTable('scopes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Tables', [
            'foreignKey' => 'scope_id'
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
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }
}
