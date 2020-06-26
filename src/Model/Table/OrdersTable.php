<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Validation\OrderValidator;

class OrdersTable extends Table
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

        $this->setTable('k_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => 'supplier_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OwnerOrganizations', [
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OwnerSupplierOrganizations', [
            'foreignKey' => 'owner_supplier_organization_id',
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
        $validator->setProvider('order', \App\Model\Validation\OrderValidation::class);

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'totArticles' => [
                   'rule' => ['totArticles'],
                   'provider' => 'order',
                   'message' => 'Il produttore scelto non ha articoli che si possono associare ad un ordine'
                ],
                'orderDuplicate' => [
                    'on' => ['create'], // , 'create', 'update',
                    'rule' => ['orderDuplicate'],
                    'provider' => 'order',
                    'message' => 'Esiste già un ordine del produttore sulla consegna scelta'
                ]
            ]);  

        $validator
            ->scalar('owner_articles')
            ->notEmptyString('owner_articles');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio')
            ->add('data_inizio', [
                'dateMinore' => [
                   // 'on' => ['create', 'update', 'empty']
                   'rule' => ['dateComparison', '<=', 'data_fine'],
                   'provider' => 'order',
                   'message' => 'La data di apertura non può essere posteriore della data di chiusura'
                ],
                'dateComparisonToDelivery' => [
                    'rule' => ['dateComparisonToDelivery', '>'],
                    'provider' => 'order',
                    'message' => 'La data di apertura non può essere posteriore della data della consegna'
                ]
            ]);            

        $validator
            ->date('data_fine')
            ->requirePresence('data_fine', 'create')
            ->notEmptyDate('data_fine')
            ->add('data_fine', [
                'dateMaggiore' => [
                   // 'on' => ['create', 'update', 'empty']
                   'rule' => ['dateComparison', '>=', 'data_inizio'],
                   'provider' => 'order',
                   'message' => 'La data di chiusura non può essere antecedente della data di apertura'
                ],
                'dateComparisonToDelivery' => [
                    'rule' => ['dateComparisonToDelivery', '>'],
                    'provider' => 'order',
                    'message' => 'La data di chiusura non può essere posteriore o uguale della data della consegna'
                ]
            ]);

        $validator
            ->date('data_fine_validation')
            ->notEmptyDate('data_fine_validation');

        $validator
            ->date('data_incoming_order')
            ->notEmptyDate('data_incoming_order');

        $validator
            ->date('data_state_code_close')
            ->notEmptyDate('data_state_code_close');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('hasTrasport')
            ->notEmptyString('hasTrasport')
            ->add('hasTrasport', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia di trasporto non prevista']);            

        $validator
            ->scalar('trasport_type')
            ->allowEmptyString('trasport_type')
            ->add('trasport_type', 'inList', ['rule' => ['inList', ['QTA', 'WEIGHT', 'USERS', '']],
                                    'message' => 'Tipologia di trasporto non prevista']);

        $validator
            ->numeric('trasport')
            ->notEmptyString('trasport');

        $validator
            ->scalar('hasCostMore')
            ->notEmptyString('hasCostMore')
            ->add('hasCostMore', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia di costo aggiuntivo non prevista']); 

        $validator
            ->scalar('cost_more_type')
            ->allowEmptyString('cost_more_type')
            ->add('cost_more_type', 'inList', ['rule' => ['inList', ['QTA', 'WEIGHT', 'USERS', '']],
                                    'message' => 'Tipologia di costo aggiuntivo non prevista']);            

        $validator
            ->numeric('cost_more')
            ->notEmptyString('cost_more');

        $validator
            ->scalar('hasCostLess')
            ->notEmptyString('hasCostLess')
            ->add('hasCostMore', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia di sconto non prevista']); 

        $validator
            ->scalar('cost_less_type')
            ->allowEmptyString('cost_less_type')
            ->add('cost_less_type', 'inList', ['rule' => ['inList', ['QTA', 'WEIGHT', 'USERS', '']],
                                    'message' => 'Tipologia di sconto non prevista']);  

        $validator
            ->numeric('cost_less')
            ->notEmptyString('cost_less');

        $validator
            ->scalar('typeGest')
            ->allowEmptyString('typeGest')
            ->add('typeGest', 'inList', ['rule' => ['inList', ['AGGREGATE', 'SPLIT', '']],
                                    'message' => 'Tipologia di gestione non prevista']);  

        $validator
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->requirePresence('state_code', 'create')
            ->notEmptyString('state_code');

        $validator
            ->scalar('mail_open_send')
            ->notEmptyString('mail_open_send')
            ->add('mail_open_send', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia invio mail per apertura ordine non prevista']); 

        $validator
            ->dateTime('mail_open_data')
            ->notEmptyDateTime('mail_open_data');

        $validator
            ->dateTime('mail_close_data')
            ->notEmptyDateTime('mail_close_data');

        $validator
            ->scalar('mail_open_testo')
            ->requirePresence('mail_open_testo', 'create')
            ->notEmptyString('mail_open_testo');

        $validator
            ->scalar('type_draw')
            ->notEmptyString('type_draw')
            ->add('type_draw', 'inList', ['rule' => ['inList', ['SIMPLE', 'COMPLETE', 'PROMOTION', '']],
                                    'message' => 'Tipologia di visualizzazione non prevista']); 

        $validator
            ->numeric('tot_importo')
            ->requirePresence('tot_importo', 'create')
            ->notEmptyString('tot_importo');

        $validator
            ->integer('qta_massima')
            ->requirePresence('qta_massima', 'create')
            ->notEmptyString('qta_massima');

        $validator
            ->scalar('qta_massima_um')
            ->allowEmptyString('qta_massima_um');

        $validator
            ->scalar('send_mail_qta_massima')
            ->notEmptyString('send_mail_qta_massima')
            ->add('send_mail_qta_massima', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia invio mail per quantità massima non prevista']); 

        $validator
            ->numeric('importo_massimo')
            ->requirePresence('importo_massimo', 'create')
            ->notEmptyString('importo_massimo');

        $validator
            ->scalar('send_mail_importo_massimo')
            ->notEmptyString('send_mail_importo_massimo')
            ->add('send_mail_importo_massimo', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia invio mail per importo massimo non prevista']); 

        $validator
            ->scalar('tesoriere_nota')
            ->allowEmptyString('tesoriere_nota');

        $validator
            ->numeric('tesoriere_fattura_importo')
            ->requirePresence('tesoriere_fattura_importo', 'create')
            ->notEmptyString('tesoriere_fattura_importo');

        $validator
            ->scalar('tesoriere_doc1')
            ->maxLength('tesoriere_doc1', 256)
            ->allowEmptyString('tesoriere_doc1');

        $validator
            ->date('tesoriere_data_pay')
            ->notEmptyDate('tesoriere_data_pay');

        $validator
            ->numeric('tesoriere_importo_pay')
            ->requirePresence('tesoriere_importo_pay', 'create')
            ->notEmptyString('tesoriere_importo_pay');

        $validator
            ->scalar('tesoriere_stato_pay')
            ->notEmptyString('tesoriere_stato_pay')
            ->add('tesoriere_stato_pay', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia stato pagamento del tesoriere non prevista']);

        $validator
            ->scalar('inviato_al_tesoriere_da')
            ->notEmptyString('inviato_al_tesoriere_da')
            ->add('inviato_al_tesoriere_da', 'inList', ['rule' => ['inList', ['REFERENTE', 'CASSIERE', '']],
                                    'message' => 'Tipologia invio al tesoriere da non prevista']);

        $validator
            ->scalar('isVisibleFrontEnd')
            ->notEmptyString('isVisibleFrontEnd')
            ->add('isVisibleFrontEnd', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia visibilità front-end non prevista']);

        $validator
            ->scalar('isVisibleBackOffice')
            ->notEmptyString('isVisibleBackOffice')
            ->add('isVisibleBackOffice', 'inList', ['rule' => ['inList', ['Y', 'N']],
                                    'message' => 'Tipologia visibilità backoffice non prevista']);

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
        $rules->add($rules->existsIn(['supplier_organization_id'], 'SuppliersOrganizations'));
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));
        $rules->add($rules->existsIn(['owner_supplier_organization_id'], 'OwnerSupplierOrganizations'));
        $rules->add($rules->existsIn(['delivery_id'], 'Deliveries'));
       
        return $rules;
    }

    public function factory($scope) {

        $table_registry = '';

        switch (strtoupper($scope)) {
            case 'DES-TITOLARE':
            case 'DES':
                $table_registry = 'OrdersDes';
                break;
            case 'GAS':
                $table_registry = 'OrdersGas';
                break;
            case 'PROMOTION':
                $table_registry = 'OrdersPromotion';
                break;
            case 'PACT-PRE':
                $table_registry = 'OrdersPactPre';
                break;
            case 'PACT':
                $table_registry = 'OrdersPact';
                break;
            
            default:
                die('OrdersTable scope ['.$scope.'] non previsto');
                break;
        }

        return TableRegistry::get($table_registry);
    } 

    public function getById($user, $organization_id, $order_id, $debug=false) {

        if (empty($order_id)) {
            return null;
        }

        $results = $this->find()  
                        ->where([
                            'Orders.organization_id' => $organization_id,
                            'Orders.id' => $order_id
                        ])
                        ->contain(['Deliveries', 'SuppliersOrganizations' => ['Suppliers']])
                        ->first();        

        return $results;      
    }

    public function riapriOrdine($user, $organization_id, $order_id, $debug=false) {
        
    }

    /*
     * estrae l'importo totale degli acquisti di un ordine
     * ctrl eventuali (come ExporDoc:getCartCompile() )
     *      - totali impostati dal referente (SummaryOrder) in Carts::managementCartsGroupByUsers
     *      - spese di trasporto  (SummaryOrderTrasport)
     */
    public function getTotImporto($user, $organization_id, $order_id, $debug=false) {
        
        $importo_totale = 0;

        $order = $this->getById($user, $organization_id, $order_id, $debug);

        /*
         * SummaryOrderAggregate: estraggo eventuali dati aggregati 
         */
         $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');
         
         $summaryOrderAggregateResults = $summaryOrderAggregatesTable->getByOrder($user, $organization_id, $order_id);
         if($summaryOrderAggregateResults->count()>0) {
            foreach ($summaryOrderAggregateResults as  $summaryOrderAggregateResult) 
                $importo_totale += $summaryOrderAggregateResult->importo;
                
            if($debug) debug("SummaryOrderAggregate->importo_totale ".$importo_totale);      
        }
        else {
            /*
             * estrae l'importo totale degli acquisti (qta e qta_forzato, importo_forzato) di un ordine
            */
            $CartsTable = TableRegistry::get('Carts');
            
            $where = [];
            $where = ['Carts.order_id' => $order_id];
            $importo_totale = $CartsTable->getTotImporto($user, $where);
            
            if($debug) debug("Cart::getTotImporto() ".$importo_totale);
        } // end if($summaryOrderAggregateResults->count()>0)

        /*
         * trasporto
        */
        if($order->hasTrasport=='Y') 
            $importo_totale += $order->trasport;
            
        if($order->hasCostMore=='Y') 
            $importo_totale += $order->cost_more;
            
        if($order->hasCostLess=='Y') 
            $importo_totale -= $order->cost_less;
        
        /* 
         *  bugs float: i float li converte gia' con la virgola!  li riporto flaot
         */
        if(strpos($importo_totale,',')!==false)  $importo_totale = str_replace(',','.',$importo_totale);
        
        if($debug) debug("Order::getTotImporto ".$importo_totale);

        return $importo_totale;
    }      
}
