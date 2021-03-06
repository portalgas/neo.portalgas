<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class SummaryOrdersTable extends Table
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

        $this->setTable('k_summary_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['id', 'organization_id', 'user_id', 'order_id']);
        // $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => ['organization_id', 'delivery_id'],
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
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

        $validator
            ->numeric('importo_pagato')
            ->notEmptyString('importo_pagato');

        $validator
            ->scalar('saldato_a')
            ->allowEmptyString('saldato_a');

        $validator
            ->scalar('modalita')
            ->notEmptyString('modalita');

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
        $rules->add($rules->existsIn(['organization_id', 'id'], 'Deliveries'));
        $rules->add($rules->existsIn(['organization_id', 'id'], 'Orders'));

        return $rules;
    }

    /*
     * precedente SummaryOrder::select_to_order()
    */
    public function getByOrder($user, $organization_id, $order_id, $options=[], $debug=false) {

        $where = ['SummaryOrders.organization_id' => $organization_id,
                  'SummaryOrders.order_id' => $order_id];
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
     * precedente SummaryOrder::select_to_order()
    */
    public function getByUserByOrder($user, $organization_id, $user_id, $order_ids, $options=[], $debug=false) {

        $where = ['SummaryOrders.organization_id' => $organization_id,
                  'SummaryOrders.user_id' => $user_id,
                  'SummaryOrders.order_id IN ' => $order_ids];
        if($debug) debug($where);

        if(is_array($order_ids))
          $results = $this->find()
                          ->contain(['Users'])
                          ->where($where)
                          ->all();
        else
          $results = $this->find()
                          ->contain(['Users'])
                          ->where($where)
                          ->first();
        
        if($debug) debug($results);
        
        return $results;
    }    
}
