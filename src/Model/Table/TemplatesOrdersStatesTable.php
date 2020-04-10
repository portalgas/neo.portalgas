<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TemplatesOrdersStates Model
 *
 * @property \App\Model\Table\TemplatesTable&\Cake\ORM\Association\BelongsTo $Templates
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \App\Model\Entity\TemplatesOrdersState get($primaryKey, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersState findOrCreate($search, callable $callback = null, $options = [])
 */
class TemplatesOrdersStatesTable extends Table
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

        $this->setTable('k_templates_orders_states');
        $this->setDisplayField('template_id');
        $this->setPrimaryKey(['template_id', 'state_code', 'group_id']);

        $this->belongsTo('Templates', [
            'foreignKey' => 'template_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
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
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->allowEmptyString('state_code', null, 'create');

        $validator
            ->scalar('action_controller')
            ->maxLength('action_controller', 25)
            ->requirePresence('action_controller', 'create')
            ->notEmptyString('action_controller');

        $validator
            ->scalar('action_action')
            ->maxLength('action_action', 50)
            ->requirePresence('action_action', 'create')
            ->notEmptyString('action_action');

        $validator
            ->scalar('flag_menu')
            ->notEmptyString('flag_menu');

        $validator
            ->integer('sort')
            ->requirePresence('sort', 'create')
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
        $rules->add($rules->existsIn(['template_id'], 'Templates'));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));

        return $rules;
    }
}
