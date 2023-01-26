<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TemplatesOrdersStatesOrdersActions Model
 *
 * @property \App\Model\Table\TemplatesTable&\Cake\ORM\Association\BelongsTo $Templates
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 * @property \App\Model\Table\OrderActionsTable&\Cake\ORM\Association\BelongsTo $OrderActions
 *
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction get($primaryKey, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TemplatesOrdersStatesOrdersAction findOrCreate($search, callable $callback = null, $options = [])
 */
class TemplatesOrdersStatesOrdersActionsTable extends Table
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

        $this->setTable('k_templates_orders_states_orders_actions');
        $this->setDisplayField('template_id');
        $this->setPrimaryKey(['template_id', 'state_code', 'group_id', 'order_action_id']);

        $this->belongsTo('Templates', [
            'foreignKey' => 'template_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OrdersActions', [
            'foreignKey' => 'order_action_id',
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
            ->nonNegativeInteger('sort')
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
        $rules->add($rules->existsIn(['order_action_id'], 'OrdersActions'));

        return $rules;
    }
}
