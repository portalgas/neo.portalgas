<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SummaryOrderAggregates Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\SummaryOrderAggregate get($primaryKey, $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryOrderAggregate findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SummaryOrderAggregatesTable extends Table
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

        $this->setTable('k_summary_order_aggregates');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['id', 'organization_id', 'user_id', 'order_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

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
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }


    /*
     * precedente SummaryOrder::select_to_order()
    */
    public function getByOrder($user, $organization_id, $order_id, $options=[], $debug=false) {

        $where = ['SummaryOrderAggregates.organization_id' => $organization_id,
                  'SummaryOrderAggregates.order_id' => $order_id];
        if($debug) debug($where);

        $results = $this->find()
                        ->contain(['Users'])
                        ->where($where)
                        ->order(['Users.name'])
                        ->all();

        if($debug) debug($results);
        
        return $results;
    }

    /*
     * precedente SummaryOrderAggregate::select_to_order()
    */
    public function getByUserByOrder($user, $organization_id, $user_id, $order_ids, $options=[], $debug=false) {

        $where = ['SummaryOrderAggregates.organization_id' => $organization_id,
                  'SummaryOrderAggregates.user_id' => $user_id,
                  'SummaryOrderAggregates.order_id IN ' => $order_ids];
        if($debug) debug($where);

        $results = $this->find()
                        ->contain(['Users'])
                        ->where($where)
                        ->all();

        if($debug) debug($results);
        
        return $results;
    }    
}
