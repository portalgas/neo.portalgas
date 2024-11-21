<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class GasGroupDeliveriesTable extends Table
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

        $this->setTable('gas_group_deliveries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('GasGroups', [
            'foreignKey' => 'gas_group_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => ['organization_id', 'delivery_id'],
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
            ->allowEmptyString('id', null, 'create');

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
        $rules->add($rules->existsIn(['gas_group_id'], 'GasGroups'));
        $rules->add($rules->existsIn(['organization_id', 'delivery_id'], 'Deliveries'));

        return $rules;
    }

    public function getsActiveList($user, $organization_id, $gas_group_id, $debug=false) {

        $where = ['GasGroupDeliveries.organization_id' => $user->organization->id,
                  'GasGroupDeliveries.gas_group_id' => $gas_group_id
               ];

        $deliveries = $this->find('list', [
                        'keyField' => function ($gas_group_delivery) {
                            return $gas_group_delivery->delivery->get('id');
                        },
                        'valueField' => function ($gas_group_delivery) {
                            return $gas_group_delivery->delivery->get('luogo').' - '.$gas_group_delivery->delivery->get('data')->i18nFormat('eeee d MMMM YYYY');
                        }])
                        ->where($where)
                        ->contain(['Deliveries' => ['conditions' => [
                            'Deliveries.isVisibleFrontEnd' => 'Y',
                            'Deliveries.stato_elaborazione' => 'OPEN',
                            'Deliveries.sys' => 'N',
                            'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesInTabs') . ' DAY'
                        ]]])
                        ->order(['Deliveries.data'])
                        ->all();

        $deliveriesTable = TableRegistry::get('Deliveries');
        $sysDeliveries = $deliveriesTable->getDeliverySysList($user, $organization_id);

        $results = [];
        if($deliveries->count()>0)
           $results += $deliveries->toArray();
        $results += $sysDeliveries;

        return $results;
    }

    /*
     *  elenco delle consegne scadute
     */

    public function getsOldList($user, $organization_id, $gas_group_id, $debug=false) {

        $where = ['GasGroupDeliveries.organization_id' => $user->organization->id,
                 'GasGroupDeliveries.gas_group_id' => $gas_group_id
        ];

        $deliveries = $this->find('list', [
            'keyField' => function ($gas_group_delivery) {
                return $gas_group_delivery->delivery->get('id');
            },
            'valueField' => function ($gas_group_delivery) {
                return $gas_group_delivery->delivery->get('luogo').' - '.$gas_group_delivery->delivery->get('data')->i18nFormat('eeee d MMMM YYYY');
            }])
            ->where($where)
            ->contain(['Deliveries' => ['conditions' => [
                'Deliveries.isVisibleBackOffice' => 'Y',
                'Deliveries.isVisibleFrontEnd' => 'Y',
                'Deliveries.stato_elaborazione' => 'OPEN',
                'Deliveries.sys' => 'N',
                'DATE(Deliveries.data) < CURDATE()'
            ]]])
            ->order(['Deliveries.data'])
            ->all();

        $results = [];
        if($deliveries->count()>0)
            $results += $deliveries->toArray();

        return $results;
    }
}
