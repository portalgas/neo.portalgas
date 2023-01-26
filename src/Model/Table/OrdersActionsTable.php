<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrdersActions Model
 *
 * @method \App\Model\Entity\OrdersAction get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrdersAction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\OrdersAction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrdersAction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrdersAction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrdersAction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrdersAction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrdersAction findOrCreate($search, callable $callback = null, $options = [])
 */
class OrdersActionsTable extends Table
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

        $this->setTable('k_orders_actions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('controller')
            ->maxLength('controller', 50)
            ->requirePresence('controller', 'create')
            ->notEmptyString('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 50)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->scalar('state_code_next')
            ->maxLength('state_code_next', 50)
            ->allowEmptyString('state_code_next');

        $validator
            ->scalar('permission')
            ->maxLength('permission', 512)
            ->requirePresence('permission', 'create')
            ->notEmptyString('permission');

        $validator
            ->scalar('permission_or')
            ->maxLength('permission_or', 512)
            ->requirePresence('permission_or', 'create')
            ->notEmptyString('permission_or');

        $validator
            ->scalar('query_string')
            ->maxLength('query_string', 100)
            ->requirePresence('query_string', 'create')
            ->notEmptyString('query_string');

        $validator
            ->scalar('flag_menu')
            ->allowEmptyString('flag_menu');

        $validator
            ->scalar('label')
            ->maxLength('label', 75)
            ->requirePresence('label', 'create')
            ->notEmptyString('label');

        $validator
            ->scalar('label_more')
            ->maxLength('label_more', 25)
            ->requirePresence('label_more', 'create')
            ->notEmptyString('label_more');

        $validator
            ->scalar('css_class')
            ->maxLength('css_class', 50)
            ->requirePresence('css_class', 'create')
            ->notEmptyString('css_class');

        $validator
            ->scalar('img')
            ->maxLength('img', 50)
            ->requirePresence('img', 'create')
            ->notEmptyString('img');

        return $validator;
    }
}
