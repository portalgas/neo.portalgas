<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JUsergroups Model
 *
 * @property \App\Model\Table\JUsergroupsTable&\Cake\ORM\Association\BelongsTo $ParentJUsergroups
 * @property \App\Model\Table\JUsergroupsTable&\Cake\ORM\Association\HasMany $ChildJUsergroups
 *
 * @method \App\Model\Entity\JUsergroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\JUsergroup newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JUsergroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JUsergroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUsergroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUsergroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JUsergroup[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JUsergroup findOrCreate($search, callable $callback = null, $options = [])
 */
class UsergroupsTable extends Table
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

        $this->setTable('j_usergroups');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('ParentJUsergroups', [
            'className' => 'JUsergroups',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildJUsergroups', [
            'className' => 'JUsergroups',
            'foreignKey' => 'parent_id'
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('rgt')
            ->notEmptyString('rgt');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->notEmptyString('title');

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentJUsergroups'));

        return $rules;
    }
}
