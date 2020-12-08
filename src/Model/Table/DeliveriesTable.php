<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Date;

class DeliveriesTable extends Table
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

        $this->setTable('k_deliveries');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['organization_id', 'id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('GcalendarEvents', [
            'foreignKey' => 'gcalendar_event_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => ['organization_id', 'delivery_id'],
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
            ->scalar('luogo')
            ->maxLength('luogo', 255)
            ->requirePresence('luogo', 'create')
            ->notEmptyString('luogo');

        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        $validator
            ->time('orario_da')
            ->requirePresence('orario_da', 'create')
            ->notEmptyTime('orario_da');

        $validator
            ->time('orario_a')
            ->requirePresence('orario_a', 'create')
            ->notEmptyTime('orario_a');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('nota_evidenza')
            ->requirePresence('nota_evidenza', 'create')
            ->notEmptyString('nota_evidenza');

        $validator
            ->scalar('isToStoreroom')
            ->requirePresence('isToStoreroom', 'create')
            ->notEmptyString('isToStoreroom');

        $validator
            ->scalar('isToStoreroomPay')
            ->requirePresence('isToStoreroomPay', 'create')
            ->notEmptyString('isToStoreroomPay');

        $validator
            ->scalar('stato_elaborazione')
            ->requirePresence('stato_elaborazione', 'create')
            ->notEmptyString('stato_elaborazione');

        $validator
            ->scalar('isVisibleFrontEnd')
            ->notEmptyString('isVisibleFrontEnd');

        $validator
            ->scalar('isVisibleBackOffice')
            ->notEmptyString('isVisibleBackOffice');

        $validator
            ->scalar('sys')
            ->notEmptyString('sys');

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
        $rules->add($rules->existsIn(['gcalendar_event_id'], 'GcalendarEvents'));

        return $rules;
    }

    public function getsList($user, $organization_id, $where=[], $order=[], $debug=false) {

        $listResults = [];

        $results = $this->gets($user, $organization_id, $where);
        if(!empty($results)) {
            foreach($results as $result) {
                // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                $listResults[$result->id] = $result->luogo.' '.$result->data->i18nFormat('d MMMM Y');
            }
        }

        // debug($listResults);
        
        return $listResults;
    }

    public function getById($user, $organization_id, $delivery_id, $debug=false) {

        $results = [];

        $where = ['Deliveries.organization_id' => $organization_id,
                  'Deliveries.id' => $delivery_id];

        $results = $this->find()
                                ->where($where)
                                ->first();
        // debug($results);

        if(!empty($results)) {
            if($results->sys=='Y')
                $results->label = $results->luogo;
            else
                $results->label = $results->data->i18nFormat('d MMMM Y').' - '.$results->luogo;
        }

        return $results;
    }

    public function gets($user, $organization_id, $where=[], $order=[], $debug=false) {

        $results = [];

        $where_delivery = [];
        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id,
                              'Deliveries.isVisibleBackOffice' => 'Y',
                              'Deliveries.sys' => 'N'], 
                              $where_delivery);

        $where_order = [];
        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge(['Orders.organization_id' => $organization_id,], $where_order);

        if(empty($order))
            $order = ['Deliveries.data'];

        if($debug) debug($where);
        $deliveryResults = $this->find()
                                ->where($where_delivery)
                                ->contain(['Orders' => [
                                    'conditions' => $where_order,
                                    'SuppliersOrganizations' => ['Suppliers']]])
                                ->order($order)
                                ->all();
        // debug($deliveryResults);

        /*
         * estraggo le consegne che hanno ordini
         */
        $i=0;
        foreach ($deliveryResults as $deliveryResult) {
            if(!empty($deliveryResult->orders)) {
                $results[$i] =  $deliveryResult;
                $i++; 
            }
        }
        // debug($results);
        
        return $results;
    }

    /* 
    * se isset($where['Orders']) cerco gli eventuali ordini
    */
    public function getDeliverySys($user, $organization_id, $where=[], $debug=false) {

        $contain = [];
        $where_order = [];
        if(isset($where['Orders'])) {
            $where_order = $where['Orders'];
            $contain = ['Orders' => [
                            'conditions' => $where_order, 
                            'sort' => ['Orders.data_inizio'],
                            'SuppliersOrganizations' => ['Suppliers']]];
        }
        // debug($contain);

        $where = ['Deliveries.organization_id' => $organization_id,
                  'Deliveries.sys' => 'Y'];

        $results = $this->find()
                        ->contain($contain)
                        ->where($where)
                        ->first();

        return $results;    
    }   

    public function getDeliverySysList($user, $organization_id, $debug=false) {

        $results = [];
        $where=[];

        $deliveryResults = $this->getDeliverySys($user, $organization_id, $where=[], $debug);

        $results[$deliveryResults->id] = $deliveryResults->luogo; 

        return $results;    
    }       
}
