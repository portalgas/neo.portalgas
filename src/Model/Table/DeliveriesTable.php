<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class DeliveriesTable extends Table
{
    const NOTA_EVIDENZA_NO = 'Nessun messaggio';
    const NOTA_EVIDENZA_MESSAGE = 'Messaggio normale';
    const NOTA_EVIDENZA_NOTICE = 'Messaggio importante';
    const NOTA_EVIDENZA_ALERT = 'Messaggio molto importante';

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
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'nota_evidenza' => [
                'strategy' => 'const',
                'prefix' => 'NOTA_EVIDENZA'
            ],
        ]]);

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

        return $rules;
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
                $results->label = $results->data->i18nFormat('eeee d MMMM Y').' - '.$results->luogo;
        }

        return $results;
    }

    /* 
     * elenco consegne attive + consegna da definire 
     * in un unico array per select
     */
    public function getsActiveList($user, $organization_id, $where=[], $order=[], $debug=false) {
        
        $conditions = ['Deliveries.isVisibleFrontEnd' => 'Y',
                        'Deliveries.stato_elaborazione' => 'OPEN',
                        'Deliveries.sys' => 'N',
                        'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesInTabs') . ' DAY'
                        // 'DATE(Deliveries.data) >= CURDATE()'
                      ];
        if($user->organization->paramsConfig['hasGasGroups']=='N')
            $conditions += ['Deliveries.type' => 'GAS']; // GAS-GROUP
                            
        if(isset($where['Deliveries']))
            $where['Deliveries'] += $conditions;
        else {
            $where['Deliveries'] = [];
            $where['Deliveries'] += $conditions;
        }

        $deliveries = $this->gets($user, $organization_id, $where);
        if($user->organization->paramsConfig['hasGasGroups']=='Y') {
            $gasGroupDeliveries = $this->getsGasGroups($user, $organization_id, $where);
            if(!empty($gasGroupDeliveries)) {
                if(count($deliveries)>0)
                    $deliveries = array_merge($deliveries, $gasGroupDeliveries);
                else 
                    $deliveries = $gasGroupDeliveries;    
            }
        }
        $deliveries = $this->getsList($deliveries);
        $sysDeliveries = $this->getDeliverySysList($user, $organization_id);

        $results = [];
        $results += $deliveries;
        $results += $sysDeliveries;

        return $results;
    }

    /* 
     * return 
     *      array['N'] elenco consegne attive per select
     *      array[delivery_id] consegna da definire 
     * 
     * escludo le consegne di gruppi, se richiamato da OrderGasParentGroups sovrascrivo con 'Deliveries.type' = GAS-GROUP
     */
    public function getsActiveGroup($user, $organization_id, $where=[], $order=[], $debug=false) {
        
        $conditions = ['Deliveries.isVisibleFrontEnd' => 'Y',
                        'Deliveries.stato_elaborazione' => 'OPEN',
                        'Deliveries.sys' => 'N',
                        'DATE(Deliveries.data) >= CURDATE()',
                        'Deliveries.type' => 'GAS']; // GAS-GROUP

        if(isset($where['Deliveries']))
            $where['Deliveries'] += $conditions;
        else {
            $where['Deliveries'] = [];
            $where['Deliveries'] += $conditions;
        }

        $deliveries = $this->gets($user, $organization_id, $where);
        $deliveries = $this->getsList($deliveries);
        $sysDeliveries = $this->getDeliverySysList($user, $organization_id);

        $results = [];
        $results['N'] = $deliveries;
        $results['Y'] = $sysDeliveries;

        return $results;
    }

    /*
     * args elenco consegne
     */
    public function getsList($results) {

        $listResults = [];

        if(!empty($results)) {
            foreach($results as $result) {
                if($result->sys=='Y') 
                    $listResults[$result->id] = $result->luogo;
                else {
                    // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                    $listResults[$result->id] = $result->luogo.' - '.$result->data->i18nFormat('eeee d MMMM Y');                    
                }
            }
        }

        // debug($listResults);
        
        return $listResults;
    }

    /* 
     * estraggo le consegne con ordini
     * per FE 
     */
    public function withOrdersGets($user, $organization_id, $where=[], $order=[], $debug=false) {

        $results = [];

        $deliveries = $this->gets($user, $organization_id, $where, $order, $debug);
        if(isset($user->organization->paramsConfig['hasGasGroups']) && $user->organization->paramsConfig['hasGasGroups']=='Y') {
            $gasGroupDeliveries = $this->getsGasGroups($user, $organization_id, $where);
            if($gasGroupDeliveries->count()>0) {
                if(count($deliveries)>0)
                    $deliveries = array_merge($deliveries, $gasGroupDeliveries);
                else 
                    $deliveries = $gasGroupDeliveries;    
            }
        }

        /*
         * estraggo le consegne che hanno ordini
         */
        $i=0;
        if(!empty($deliveries))
        foreach ($deliveries as $delivery) {
            if(!empty($delivery['orders'])) {
                $results[$i] =  $delivery;
                $i++; 
            }
        }
        // debug($results);
        
        return $results;
    }

    /* 
     * estraggo le consegne Deliveries.type = 'GAS' con ordini e senza
     */
    public function gets($user, $organization_id, $where=[], $order=[], $debug=false) {

        $results = [];

        $where_delivery = [];
        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id,
                              'Deliveries.isVisibleBackOffice' => 'Y',
                              'Deliveries.type' => 'GAS',
                              'Deliveries.sys' => 'N'], 
                              $where_delivery);
        
        $where_order = [];
        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge(['Orders.organization_id' => $organization_id,], $where_order);
    
        if(empty($order))
            $order = ['Deliveries.data'];

        $results = $this->find()
                        ->where($where_delivery)
                        ->contain(['Orders' => [
                            'conditions' => $where_order,
                            'SuppliersOrganizations' => ['Suppliers']]])
                        ->order($order)
                        ->all();   

        if($results->count()>0) 
            $results = $results->toArray();

        return $results;
    }

    /* 
     * estraggo le consegne Deliveries.type = 'GAS-GROUP' con ordini e senza
     */
    public function getsGasGroups($user, $organization_id, $where=[], $order=[], $debug=false) {

        $results = [];

        $where_delivery = [];
        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id,
                              'Deliveries.isVisibleBackOffice' => 'Y',
                              'Deliveries.type' => 'GAS-GROUP',
                              'Deliveries.sys' => 'N'], 
                              $where_delivery);
      
        $where_order = [];
        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge(['Orders.organization_id' => $organization_id,], $where_order);
    
        if(empty($order))
            $order = ['Deliveries.data'];
       
        // ctrl che l'utente appartertenga al gruppo 
        $gasGroupsTable = TableRegistry::get('GasGroups');
        $gasGroups = $gasGroupsTable->findMyLists($user, $organization_id, $user->id);
        if(empty($gasGroups))
            $where_order += ['Orders.gas_group_id' => '-1']; // utente non associato in alcun gruppo 
        else 
            $where_order += ['Orders.gas_group_id IN ' => array_keys($gasGroups)];
           
        $results = $this->find()
                                    ->where($where_delivery)
                                    ->contain(['Orders' => [
                                        'conditions' => $where_order,
                                        'SuppliersOrganizations' => ['Suppliers']]])
                                    ->order($order)
                                    ->all();
        if($results->count()>0) {
            $results = $results->toArray();
        }

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
                  'Deliveries.sys' => 'Y',
                  'Deliveries.type' => 'GAS'];

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
