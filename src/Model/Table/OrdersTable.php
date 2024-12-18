<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use App\Validation\OrderValidator;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;
use App\Traits;

class OrdersTable extends Table
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

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
        $this->setPrimaryKey(['organization_id', 'id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OrderStateCodes', [
            'foreignKey' => 'state_code',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OrderTypes', [
            'foreignKey' => 'order_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
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
        $this->hasMany('Carts', [
            'foreignKey' => ['organization_id', 'order_id'],
        ]);
        $this->hasMany('ArticlesOrders', [
            'foreignKey' => ['organization_id', 'order_id'],
        ]);
        $this->belongsTo('GasGroups', [
            'foreignKey' => ['gas_group_id'],
            'joinType' => 'LEFT',
        ]);
        // ordini associati all'ordine parent
        $this->hasMany('GasGroupsChilds', [
            'className' => 'Orders',
            'foreignKey' => ['organization_id', 'parent_id'],
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator->setProvider('order', \App\Model\Validation\OrderValidation::class);

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('supplier_organization_id', 'create')
            ->notEmptyString('supplier_organization_id')
            ->add('supplier_organization_id', [
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
            ->scalar('order_type_id')
            ->notEmptyString('order_type_id');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio')
            ->add('data_inizio', [
                'dateMinore' => [
                   // 'on' => ['create', 'update', 'empty']
                   'rule' => ['dateComparison', '<=', 'data_fine'],
                   'provider' => 'order',
                   'message' => 'La data di apertura non può essere posteriore alla data di chiusura'
                ],
                'dateComparisonToDelivery' => [
                    'rule' => ['dateComparisonToDelivery', '<'],
                    'provider' => 'order',
                    'message' => 'La data di apertura non può essere posteriore alla data della consegna'
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
                   'message' => 'La data di chiusura non può essere antecedente alla data di apertura'
                ],
                'dateComparisonToDelivery' => [
                    'rule' => ['dateComparisonToDelivery', '<='],
                    'provider' => 'order',
                    'message' => 'La data di chiusura non può essere posteriore o uguale alla data della consegna'
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
            ->allowEmptyString('qta_massima_um')
            ->add('qta_massima_um', 'inList', ['rule' => ['inList', ['PZ', 'KG', 'LT', '']],
                                    'message' => 'Tipologia unità di misura per quantità massima non prevista']);

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
        // debug('OrdersTable buildRules');

        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'supplier_organization_id'], 'SuppliersOrganizations'));
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));
        $rules->add($rules->existsIn(['owner_supplier_organization_id'], 'OwnerSupplierOrganizations'));
        $rules->add($rules->existsIn(['organization_id', 'delivery_id'], 'Deliveries'));
        $rules->add($rules->existsIn(['order_type_id'], 'OrderTypes'));
        /*
        $rules->addCreate(function ($entity, $options) {
            debug('buildRules');
            // debug($entity);
        }, 'ruleName');
        */
        return $rules;
    }

    public function factory($user, $organization_id, $order_type_id, $order_id=0, $debug=false) {

        $table_registry = '';

        if(empty($order_type_id)) {
            /*
             * recupero order_type_id
             */
            $where = ['Orders.organization_id' => $organization_id, 'Orders.id' => $order_id];
            if($debug) debug($where);
            $orderResults = $this->find()
                            ->where($where)
                            ->first();
            if($debug) debug($orderResults);

            if(!empty($orderResults))
                $order_type_id = $orderResults->order_type_id;
            else
                $order_type_id = 0;
        }

        if(empty($order_type_id)) {
            return false;
            // die('OrdersTable order_type_id ['.$order_type_id.'] non previsto');
        }

        switch (strtoupper($order_type_id)) {
            case Configure::read('Order.type.des_titolare'):
                $table_registry = 'OrdersDesTitolare';
                break;
            case Configure::read('Order.type.des'):
                $table_registry = 'OrdersDes';
                break;
            case Configure::read('Order.type.gas'):
                $table_registry = 'OrdersGas';
                break;
            case Configure::read('Order.type.promotion'):
                $table_registry = 'OrdersPromotion';
                break;
            case Configure::read('Order.type.pact-pre'):
                $table_registry = 'OrdersPactPre';
                break;
            case Configure::read('Order.type.pact'):
                $table_registry = 'OrdersPact';
                break;
            case Configure::read('Order.type.socialmarket'):
                    $table_registry = 'OrdersSocialMarket';
                    break;
            case Configure::read('Order.type.gas_parent_groups'):
                $table_registry = 'OrdersGasParentGroups';
            break;
            case Configure::read('Order.type.gas_groups'):
                $table_registry = 'OrdersGasGroups';
                break;
            default:
                die('OrdersTable order_type_id ['.$order_type_id.'] non previsto');
            break;
        }
        if($debug) debug($table_registry);

        return TableRegistry::get($table_registry);
    }

    /*
     * implement
     */
    public function getSuppliersOrganizations($user, $organization_id, $user_id, $where=[], $debug=false) {
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $results = $suppliersOrganizationsTable->ACLgets($user, $organization_id, $user_id);
        return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */
    public function afterAddWithRequest($user, $organization_id, $order, $request, $debug=false) {
        return true;
    }

    /*
     * implement
     * get() gia' Cake\ORM\Table::get($primaryKey, $options = Array)
     */
    public function getById($user, $organization_id, $order_id, $debug=false) {

        if (empty($order_id)) {
            return null;
        }

        $results = $this->find()
                        ->where([
                            $this->getAlias().'.organization_id' => $organization_id,
                            $this->getAlias().'.id' => $order_id
                        ])
                        ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries',
                                    'SuppliersOrganizations' => [
                                        'Suppliers',
                                        'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles' => ['sort' => ['ordering']]]]],
                                  /*
                                   * con Orders.owner_articles => chi gestisce il listino
                                   */
                                  'OwnerOrganizations', 'OwnerSupplierOrganizations'
                                  ])
                        ->first();

        /*
         * produttori esclusi dal prepagato
         */
        if(!empty($results) && isset($user->organization->paramsConfig['hasCashFilterSupplier']) && $user->organization->paramsConfig['hasCashFilterSupplier']=='Y') {
            $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');
            $results->suppliers_organization->isSupplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->isSupplierOrganizationCashExcluded($user, $results->suppliers_organization->organization_id, $results->suppliers_organization->id);
        }

        /*
         * referenti
         */
        if(isset($results->suppliers_organization->suppliers_organizations_referents)) {
            $referentsResult = new ApiSuppliersOrganizationsReferentDecorator($user, $results->suppliers_organization->suppliers_organizations_referents);
            $results->referents = $referentsResult->results;
            unset($results->suppliers_organization->suppliers_organizations_referents);
        }

        return $results;
    }

    public function getsList($user, $organization_id, $where=[], $debug=false) {

        $listResults = [];

        $results = $this->gets($user, $organization_id, $where, $debug);
        if(!empty($results)) {
            foreach($results as $result) {
                 /*
                  * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                  * key array non per id, nel json perde l'ordinamento della data
                  * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM Y');
                  */
                // debug($result);exit;
                $listResults[$result->id] = $result->suppliers_organization->name.' - '.$result->delivery->data->i18nFormat('eeee d MMMM').' - '.$result->delivery->luogo;
            }
        }

        // debug($listResults);
        return $listResults;
    }

    public function gets($user, $organization_id, $where=[], $debug=false) {

        $results = [];
        $where_order = [];
        $where_delivery = [];

        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                              $this->getAlias().'.isVisibleBackOffice' => 'Y'],
                              $where_order);
        if($debug) debug($where_order);

        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id], $where_delivery);

        if($debug) debug($where_delivery);
        $results = $this->find()
                        ->where($where_order)
                        ->contain([
                            // 'OrderTypes' => ['conditions' => ['code IN ' => ['GAS', 'DES', ...]],
                            'OrderStateCodes',
                            'SuppliersOrganizations' => ['Suppliers'],
                            'Deliveries' => ['conditions' => $where_delivery]
                        ])
                        ->order([$this->getAlias().'.data_inizio'])
                        ->all();
        // debug($results);

        return $results;
    }

    /*
     * da OrdersBehavior
     *
     * ctrl data_inizio con data_oggi
     *
     *      se data_inizio < data_oggi NON invio mail
     *      se data_inizio = data_oggi mail_open_send = Y, Cron::mailUsersOrdersOpen domani invio mail
     *      se data_inizio > data_oggi mail_open_send = N, Cron::mailUsersOrdersOpen invio mail
     */
    public function setOrderMailOpenSend($request) {
        $mail_open_send = 'Y';

        /*
         * ctrl che il produttore scelto abbia SuppliersOrganization.mail_order_open = Y
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $where = ['SuppliersOrganizations.organization_id' => $request['organization_id'],
                  'SuppliersOrganizations.id' => $request['supplier_organization_id']];

        $results = $suppliersOrganizationsTable->find()
                                ->select(['SuppliersOrganizations.mail_order_open'])
                                ->where($where)
                                ->first();
        // debug($request);
        // debug($results);
        if($results->mail_order_open=='N')
            $mail_open_send = 'N';
         else {
             if(isset($request['data_inizio'])) {
                 $data_inizio = $request['data_inizio'];
                 $data_inizio = self::dateFrozenToArray($data_inizio);
                 $data_inizio = $data_inizio['year'].'-'.$data_inizio['month'].'-'.$data_inizio['day'];
                 $data_oggi = date("Y-m-d");

                 if ($data_inizio == $data_oggi)
                     $mail_open_send = 'Y';
                 else
                     $mail_open_send = 'N';
             }
             else
                 $mail_open_send = 'N';
        }

        // debug('setOrderMailOpenSend() data_inizio '.$data_inizio.' data_oggi '.$data_oggi.' mail_open_send = '.$mail_open_send);

        return $mail_open_send;
    }

    public function riapriOrdine($user, $organization_id, $order_id, $debug=false) {

    }

    /*
     * estrae l'importo totale degli acquisti di un ordine
     * ctrl eventuali (come ExporDoc:getCartCompile() )
     *      - totali impostati dal referente (SummaryOrder) in Carts::managementCartsGroupByUsers
     *      - spese di trasporto  (SummaryOrderTrasport)
     */
    public function getTotImporto($user, $organization_id, $order, $debug=false) {

        $importo_totale = 0;

        if(!is_object($order))
            $order = $this->getById($user, $organization_id, $order, $debug);

        /*
         * SummaryOrderAggregate: estraggo eventuali dati aggregati
         */
        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');

        $summaryOrderAggregateResults = $summaryOrderAggregatesTable->getByOrder($user, $organization_id, $order->id);
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
            $where = ['Carts.order_id' => $order->id];
            $importo_totale = $CartsTable->getTotImporto($user, $organization_id, $where);

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

	/*
	 * ctrl se la validazione del carrello e' abilitata (ArticlesOrder.pezzi_confezione > 1) per la gestione dei colli
	*/
	public function isOrderToValidate($user, $order_id) {

		$articlesOrdersTable = TableRegistry::get('ArticlesOrders');

		$where = ['ArticlesOrders.order_id' => (int)$order_id,
					   'ArticlesOrders.pezzi_confezione >' => '1',
                       'ArticlesOrders.organization_id' => $user->organization->id,
                       'ArticlesOrders.stato != ' => 'N',
                       'Articles.stato' => 'Y'];

	    $results = $articlesOrdersTable->find()->contain(['Articles'])->where($where)->all();
        if($results->count()==0)
            $isToValidate = false;
        else
            $isToValidate = true;

        return $isToValidate;
	}

    /*
     * estrae ordine precedente escludendo gli ordini che hanno dei parent Configure::read('Order.type.des') / Configure::read('Order.type.gas_groups')
     */
	public function getPrevious($user, $order) {

		$where = ['Deliveries.organization_id' => $user->organization->id,
                    'Deliveries.isVisibleBackOffice' => 'Y',
                    'DATE(Deliveries.data) < CURDATE()',
                    'Orders.isVisibleBackOffice' => 'Y',
                    'Orders.id != ' => $order->id,
                    'Orders.supplier_organization_id' => $order->supplier_organization_id,
                    'Orders.order_type_id NOT IN ' => [Configure::read('Order.type.des'), Configure::read('Order.type.gas_groups')]];
	    $results = $this->find()
                        ->contain(['Deliveries',
                            'ArticlesOrders' =>
                                ['Articles' => [
                                    'conditions' => ['Articles.stato' => 'Y',
                                                     'Articles.flag_presente_articlesorders' => 'Y']]]])
                        ->where($where)
                        ->first();

        if(!empty($results)) {
            // ordine senza articoli associati
            if(empty($results->articles_orders))
            $results = [];
        }

        return $results;
	}
}
