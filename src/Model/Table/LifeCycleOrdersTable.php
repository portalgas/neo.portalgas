<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use App\Validation\OrderValidator;

class LifeCycleOrdersTable extends Table
{
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
            'joinType' => 'INNER'
        ]);        
    }
    
    /*
     * richiamato a cambiamento dei dati di un ordine, ex
     *  CHANGE_DELIVERY quando l'ordine cambia di consegna
     */
    public function changeOrder($user, $orderResult, $operation='', $opts=[], $debug=false) {
    
        $esito = [];

        if(empty($orderResult)) {
            $esito['CODE'] = "500";
            $esito['MSG'] = "Parametri errati";
            return $esito; 
        }   

        $ordersTable = TableRegistry::get('Orders');
        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
    
        if($debug) debug("LifeCycleOrdersTable::changeOrder order_id [".$orderResult->id."] operation ".$operation);
        
        switch($operation) {
            case 'EDIT':
                if($orderResult->typeGest!='AGGREGATE') // SPLIT
                    $summaryOrderAggregatesTable->delete_to_order($user, $orderResult->id, $debug);
            
                /*
                 * elimina il trasporto da Orders
                */
                if($orderResult->hasTrasport=='N') {
                
                    if($debug) debug("Order.hasTrasport == N, cancello il trasporto");
                    
                    $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');

                    $summaryOrderTrasportsTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }

                /*
                 * elimina il costo aggiuntivo da Orders
                */
                if($orderResult->hasCostMore=='N') {
                
                    if($debug) debug("Order.hasCostMore == N, cancello il costo aggiuntivo");
                        
                    $summaryOrderCostMoresTable = TableRegistry::get('SummaryOrderCostMores');
                
                    $summaryOrderCostMoresTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }
                
                /*
                 * elimina lo sconto da Orders
                */
                if($orderResult->hasCostLess=='N') {
                
                    if($debug) debug("Order.hasCostLess == N, cancello lo sconto");
                        
                    $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');    
                
                    $summaryOrderCostLessesTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }   
            break;
            case 'CHANGE_DELIVERY':
                /*
                 * aggiorno con il nuovo delivery_id le tabelle
                 *
                 * k_summary_orders                      
                 * k_request_payments_orders
                 */
                $this->_updateTablesToChangeDeliverId($user, $orderResult->id, $orderResult->delivery_id, $debug);
                            
            break;
            default:
                die("LifeCycleOrdersTable::changeOrder operation non previsto [".$operation."]");
            break;          
        }
            
        return $esito; 
    }

    /*
     * $orderResult stato attuale dell'ordine
     */
    public function beforeRendering($user, $orderResult, $controller, $action, $opts = [], $debug=false) {
        
        $esito = [];

        if(empty($orderResult)) {
            $esito['CODE'] = "500";
            $esito['MSG'] = "Parametri errati";
            return $esito; 
        }   

        $ordersTable = TableRegistry::get('Orders');

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
    
        if($debug) debug("LifeCycleOrdersTable::beforeRendering order_id [".$orderResult->id."] state_code ".$orderResult->state_code);

        switch($orderResult->state_code) {
            case 'CREATE-INCOMPLETE':
            break;
            case 'OPEN-NEXT':
            break;
            case 'OPEN':            
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug);
            break;
            case 'RI-OPEN-VALIDATE':
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug); 
            break;
            case 'PROCESSED-BEFORE-DELIVERY':
            
            break;
            case 'PROCESSED-POST-DELIVERY':            
                $esito['isOrderValidateToTrasmit'] = $this->_isOrderValidateToTrasmit($user, $orderResult, $controller, $action, $debug);
                
                if(isset($opts['moduleConflicts'])) {
                    $esito['ctrlModuleConflicts'] = $this->_ctrlModuleConflicts($user, $orderResult, $opts['moduleConflicts'], $debug);
                }
            break;
            case 'INCOMING-ORDER':  // merce arrivata          
                $esito['isOrderValidateToTrasmit'] = $this->_isOrderValidateToTrasmit($user, $orderResult, $controller, $action, $debug);
                
                if(isset($opts['moduleConflicts'])) {
                    $esito['ctrlModuleConflicts'] = $this->_ctrlModuleConflicts($user, $orderResult, $opts['moduleConflicts'], $debug);
                }
                
                $esito['msgOrderToClose'] = $this->_msgOrderToClose($user, $orderResult, $controller, $action, $debug);
            break;
            case 'PROCESSED-ON-DELIVERY':  // in carico al Cassiere            
                $esito['isOrderValidateToTrasmit'] = $this->_isOrderValidateToTrasmit($user, $orderResult, $controller, $action, $debug);   

                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug);     

                $esito['msgOrderToClose'] = $this->_msgOrderToClose($user, $orderResult, $controller, $action, $debug);             
            break;
            /*
             * tesoriere
             */             
            case 'WAIT-PROCESSED-TESORIERE':
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug);
            break;
            case 'PROCESSED-TESORIERE':  // in carico al Tesoriere
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug);
            break;              
            case 'TO-REQUEST-PAYMENT':                  
            break;
            case 'TO-PAYMENT':
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug); 
            break;
            case 'USER-PAID':
            break;
            case 'SUPPLIER-PAID':
            break;
            case 'WAIT-REQUEST-PAYMENT-CLOSE':
            break;
            case 'CLOSE':
                $esito['msgExportDocs'] = $this->_msgExportDocs($user, $orderResult, $controller, $action, $debug);             
            break;
            default:
                die("LifeCycleOrdersTable::beforeRendering Order.state_code non previsto [".$orderResult->state_code."]");
            break;              
        }

        return $esito;
    }
    
    /*
     * $orderResult stato attuale dell'ordine
     * $state_code_next stato succesivo, se non valorizzato non ho ancora richiesto il cambio stato
     */
    public function stateCodeUpdate($user, $orderResult, $state_code_next='', $opts=[], $debug=false) {

        $esito = [];

        if(empty($orderResult) || empty($state_code_next)) {
            $esito['CODE'] = "500";
            $esito['MSG'] = "Parametri errati";
            return $esito; 
        }   

        $ordersTable = TableRegistry::get('Orders');

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
    
        if($debug) debug("LifeCycleOrdersTable::stateCodeUpdate order_id [".$orderResult->id."] state_code ".$orderResult->state_code." state_code_next ".$state_code_next);

        if($state_code_next==$orderResult->state_code) {
            if($debug) debug("LifeCycleOrdersTable::stateCodeUpdate order_id [".$orderResult->id."] state_code ".$orderResult->state_code." == state_code_next ".$state_code_next." => NON aggiorno");
            $esito['CODE'] = "200";
            return $esito;      
        }

        /*
         * eventi prima del salvataggio
         */
        switch($state_code_next) {
            case 'CREATE-INCOMPLETE':
            break;
            case 'OPEN-NEXT':
                if($orderResult->state_code=='PROCESSED-BEFORE-DELIVERY')
                    $ordersTable->riapriOrdine($user, $orderResult->organization_id, $orderResult->id, $debug);
            break;
            case 'OPEN':
                if($orderResult->state_code=='PROCESSED-BEFORE-DELIVERY')
                    $ordersTable->riapriOrdine($user, $orderResult->organization_id, $orderResult->id, $debug);
            break;
            case 'RI-OPEN-VALIDATE':
                if($orderResult->state_code=='PROCESSED-BEFORE-DELIVERY')
                    $ordersTable->riapriOrdine($user, $orderResult->organization_id, $orderResult->id, $debug);
            break;
            case 'PROCESSED-BEFORE-DELIVERY':
                /*
                 * cancello eventuali dati aggregati / trasporto ..., la merce non e' arrivata e il referente 
                 *      puo' modificare acquisti
                 *      dati aggregato / trasporto ... gia' calcolati possono essere errati
                 */
                if($orderResult->typeGest=='AGGREGATE') {
                    $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');
                
                    $summaryOrderAggregatesTable->delete_to_order($user, $orderResult->id, $debug);
                }
                if($orderResult->hasTrasport=='N') {  
                    $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');              
                    $summaryOrderTrasportsTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }
                if($orderResult->hasCostMore=='N') {  
                    $summaryOrderCostMoreTable = TableRegistry::get('SummaryOrderCostMores');                 
                    $summaryOrderCostMoreTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }
                if($orderResult->hasCostLess=='N') {
                    $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');

                    $summaryOrderCostLessesTable->delete_importo_to_order($user, $orderResult->id, $debug);
                }   
                 
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');      
            break;
            case 'PROCESSED-POST-DELIVERY':
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'INCOMING-ORDER':  // merce arrivata
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'PROCESSED-ON-DELIVERY':  // in carico al Cassiere     
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            /*
             * tesoriere
             */             
            case 'WAIT-PROCESSED-TESORIERE':
                // $Tesoriere->sendMailToUpload($user, $this->request->data, $orderResult, 'REFERENTE', $debug);        
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'PROCESSED-TESORIERE':  // in carico al Tesoriere  
                /*
                 * se l'ordine e' associato ad una richiesta di pagamento (ordine e' tornato indietro), lo elimino dalla rich di pagamento
                 */ 
                if($user->organization->template->payToDelivery=='POST' || $user->organization->template->payToDelivery=='ON-POST') {
                    /*
                     * estraggo RequestPaymentsOrder da cancellare
                     */
                    $requestPaymentsOrdersTable = TableRegistry::get('RequestPaymentsOrders'); 
        
                    $options =  [];
                    $options['conditions'] = ['RequestPaymentsOrder.organization_id'=>(int)$user->organization->id,
                                              'RequestPaymentsOrder.order_id'=> $orderResult->id];
                    $options['recursive'] = -1;
                    $requestPaymentsOrderResults = $requestPaymentsOrdersTable->find('first', $options);
                    if(!empty($requestPaymentsOrderResults)) {
                        /*
                         * aggiorno il totale in SummaryPayment, se il gasista aveva solo quell'ordine SummaryPayment.stato = DAPAGARE
                        */
                        $summaryPaymentsTable = TableRegistry::get('SummaryPayments');

                        $summaryPaymentsTable->delete_order($user, $orderResult->id, $requestPaymentsOrderResults['RequestPaymentsOrder']['request_payment_id'], $debug);
                    }
                }
                
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;              
            case 'TO-REQUEST-PAYMENT':  // Possibilità di richiederne il pagamento  
                $monitoringOrdersTable = TableRegistry::get('MonitoringOrders');

                $monitoringOrdersTable->delete_to_order($user, $orderResult->id);              

                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'TO-PAYMENT':  // Associato ad una richiesta di pagamento
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'USER-PAID':                   
                // da TO-PAYMENT a USER-PAID per in pagamenti POST
                // da PROCESSED-ON-DELIVERY a USER-PAID  per in pagamenti ON
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'SUPPLIER-PAID':
                $orderResult->data_state_code_close = Configure::read('DB.field.date.empty');
            break;
            case 'WAIT-REQUEST-PAYMENT-CLOSE':
            break;
            case 'CLOSE':
                /*
                 * posso chiudere un ordine 
                 *      senza aver saldato tutti i gasisit
                 *      senza aver pagato il produttore
                 */ 
                if($orderResult->delivery->sys=='Y') {
                    $msg = __("Lo stato dell'ordine non è stato aggiornato perchè non associato ad una consegna valida.");  
                    $esito['CODE'] = "500";
                    $esito['MSG'] = $msg;
                    return $esito; 
                }
        
                /*
                 * se order_just_pay = Y forzo il pagamento di un produttore
                 */
                 if(isset($opts['order_just_pay']) && $opts['order_just_pay']=='Y')
                    $order_just_pay = true;
                else
                    $order_just_pay = false;
                
                /*
                 * setto i campi tesoriere anche per i template ON 
                 *
                 * calcolo il totale degli importi degli acquisti dell'ordine
                 */ 
                $importo_totale = $ordersTable->getTotImporto($user, $orderResult->organization_id, $orderResult->id);
                $orderResult->tot_importo = $importo_totale;

                if($user->organization->template->orderSupplierPaid=='Y') {
                    if(empty($orderResult->tesoriere_importo_pay) || $order_just_pay)
                        $orderResult->tesoriere_importo_pay = $importo_totale;
                    if(empty($orderResult->tesoriere_importo_pay) || $order_just_pay)
                        $orderResult->tesoriere_importo_pay = $importo_totale;
            
                    if(empty($orderResult->inviato_al_tesoriere_da) || $order_just_pay)
                        $orderResult->inviato_al_tesoriere_da = 'REFERENTE';
                    if(empty($orderResult->tesoriere_data_pay) || $order_just_pay)
                        $orderResult->tesoriere_data_pay = date('Y-m-d');
                    if(empty($orderResult->tesoriere_stato_pay) || $order_just_pay)  // condizione che permette ad un ordine in stato SUPPLIER-PAID di passare a CLOSE
                        $orderResult->tesoriere_stato_pay = 'Y';
                } // if($user->organization->template->orderSupplierPaid=='Y')

                if($user->organization->template->orderUserPaid=='Y') {
                
                } // if($user->organization->template->orderUserPaid=='Y')
                
                /*
                 * da questa data va in STATISTICHE dopo Configure::read('GGArchiveStatics') gg 
                 */         
                $orderResult->data_state_code_close = date("Y-m-d");
            break;
            default:
                die("LifeCycleOrdersTable::stateCodeUpdate Order.state_code_next non previsto [".$state_code_next."]");
            break;              
        }
  
        $orderResult->state_code = $state_code_next;

        /*
         * add other fields 
         */      
        $orderResult = $this->_orderAddValue($orderResult, $state_code_next, $opts, $debug);
        if(isset($orderResult->CODE)) {
            if($debug) debug("LifeCycleOrdersTable::stateCodeUpdate _orderAddValue() order_id [".$orderResult->id."] ERROR salvando l'ordine ".$orderResult->CODE);
            return $orderResult; 
        }

        if($debug) debug("LifeCycleOrdersTable::stateCodeUpdate order_id [".$orderResult->id."] salvo l'ordine");
        if($debug) debug($orderResult);

        /*
         * aggiorno lo stato dell'ordine
         */
        unset($orderResult->suppliers_organization);
        unset($orderResult->delivery);
        //$order = $ordersTable->patchEntity($order, $orderResult);
        if (!$ordersTable->save($orderResult)) {
            if($debug) debug("LifeCycleOrdersTable::stateCodeUpdate order_id [".$orderResult->id."] ERROR salvando l'ordine");
            if($debug) debug($orderResult>getErrors());
            $esito['CODE'] = "500";
            $esito['MSG'] = $orderResult>getErrors();
            return $esito; 
        }

        /*
         * add other fields 
         */          
        switch($orderResult->state_code) {
            case 'CREATE-INCOMPLETE':
            break;
            case 'OPEN-NEXT':
            break;
            case 'OPEN':
            break;
            case 'RI-OPEN-VALIDATE':
            break;
            case 'PROCESSED-BEFORE-DELIVERY':
            
            break;
            case 'PROCESSED-POST-DELIVERY':
            
            break;
            case 'INCOMING-ORDER':  // merce arrivata
                $lifeCycleSummaryOrdersTable->callbackToOrder($user, $orderResult); // => pulisco k_summary_orders
            break;
            case 'PROCESSED-ON-DELIVERY':  // in carico al Cassiere
                $lifeCycleSummaryOrdersTable->callbackToOrder($user, $orderResult); // => popolo k_summary_orders 
            break;
            /*
             * tesoriere
             */             
            case 'WAIT-PROCESSED-TESORIERE':
                // $Tesoriere->sendMailToUpload($user, $this->request->data, $results, 'REFERENTE', $debug);    
                $lifeCycleSummaryOrdersTable->callbackToOrder($user, $orderResult); // => pulisco k_summary_orders            
            break;
            case 'PROCESSED-TESORIERE':  // in carico al Tesoriere
                $lifeCycleSummaryOrdersTable->callbackToOrder($user, $orderResult); // => popolo k_summary_orders                             
            break;              
            case 'TO-REQUEST-PAYMENT':  // Possibilità di richiederne il pagamento
                $lifeCycleSummaryOrdersTable->callbackToOrder($user, $orderResult); // => pulisco k_summary_orders                        
            break;
            case 'TO-PAYMENT':
                 /*
                 * riporto le consegne da CLOSE e OPEN
                 */
                $DeliveryLifeCycle->deliveriesToOpen($user, $orderResult->delivery_id, $debug);  // TODO  
            break;
            case 'USER-PAID':                   
            break;
            case 'SUPPLIER-PAID':
            break;
            case 'WAIT-REQUEST-PAYMENT-CLOSE':
            break;
            case 'CLOSE':
            break;
            default:
                die("LifeCycleOrdersTable::stateCodeUpdate Order.state_code non previsto [".$orderResult->state_code."]");
            break;          
        }
        
        $esito['CODE'] = "200";
                        
        return $esito;         
    }       
    
    /*
     * ordine saldato dai gasisti
     * Organization.orderUserPaid = 'Y'
     * solo per Order.state_code PROCESSED-ON-DELIVERY / TO-PAYMENT / SUPPLIER-PAID
     * $entityOrder Order o order_id
     */
    public function getPaidUsers($user, $orderResult, $debug=false) {
    
        $results = [];
        
        $ordersTable = TableRegistry::get('Orders');

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
 
        if($user->organization->template->orderUserPaid == 'Y' && in_array($orderResult->state_code, $this->getStateCodeManagementPayments($user))) {
               
            $summaryOrdersTable = TableRegistry::get('SummaryOrders');  
            $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');        
               
            $where = [];
            $where = ['SummaryOrders.organization_id' => $user->organization->id,
                      'SummaryOrders.order_id' => $orderResult->id];
            $where += $lifeCycleSummaryOrdersTable->getConditionIsSaldato($user);          
            $options['recursive'] =  0;                                 
            $summaryOrderPaidResults = $summaryOrdersTable->find()
                                                            ->where($where)
                                                            ->all();
            $totalSummaryOrderPaid = $summaryOrderPaidResults->count(); 
        
            $where = [];
            $where = ['SummaryOrders.organization_id' => $user->organization->id,
                      'SummaryOrders.order_id' => $orderResult->id];
            $where += $lifeCycleSummaryOrdersTable->getConditionIsNotSaldato($user);
            $summaryOrderNotPaidResults = $summaryOrdersTable->find()
                                                            ->where($where)
                                                            ->all();
            $totalSummaryOrderNotPaid = $summaryOrderNotPaidResults->count(); 

            $results['totalSummaryOrder'] = ($totalSummaryOrderPaid + $totalSummaryOrderNotPaid);
            $results['totalSummaryOrderPaid'] = $totalSummaryOrderPaid;
            $results['totalSummaryOrderNotPaid'] = $totalSummaryOrderNotPaid;
            
            $results['summaryOrderPaid'] = $summaryOrderPaidResults;
            $results['summaryOrderNotPaid'] = $summaryOrderNotPaidResults;          
        }
        
        if($debug) debug("LifeCycleOrdersTable::getPaidUsers order_id [".$orderResult->id."] ".$orderResult->state_code);
        // if(!empty($results)) if($debug) debug($results);
                
        return $results;     
    }

    /*  
     * ordine pagato da tutti i gasisti produttore se Order.tesoriere_stato_pay=='Y'
     */
    public function isPaidUsers($user, $orderResult, $debug=false) {

        $results = false;

        $ordersTable = TableRegistry::get('Orders');
        $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
        
        // ctrl se e' stato saldato da tutti i gasisti
        if($lifeCycleSummaryOrdersTable->isSummaryOrderAllSaldato($user, $orderResult, $debug))  
            $results = true;
        else
            $results = false;   
            
        if($debug) debug("LifeCycleOrdersTable::isPaidUsers order_id [".$orderResult->id."] ".$orderResult->state_code);
        if(!empty($results)) if($debug) debug($results);
    
        return $results;    
    }
    
    /*
     * ordine pagato al produttore
     * Organization.orderSupplierPaid = Y
     * solo per Order.state_code PROCESSED-ON-DELIVERY / TO-PAYMENT / USER-PAID
     * $entityOrder Order o order_id
     */
    public function getPaidSupplier($user, $orderResult, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');

        $results = [];

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
        
        if($user->organization->template->orderSupplierPaid == 'Y' && in_array($orderResult->state_code, $this->getStateCodeManagementPayments($user))) { 
                $results['isPaid'] = $this->isPaidSupplier($user, $orderResult, $debug);
        }
    
        if($debug) debug("LifeCycleOrdersTable::getPaidSupplier order_id [".$orderResult->id."] ".$orderResult->state_code);
        if(!empty($results) && isset($results['isPaid']) && !empty($results['isPaid'])) if($debug) debug($results);
    
        return $results;    
    }   
    
    /*  
     * ordine pagato al produttore se Order.tesoriere_stato_pay=='Y'
     */
    public function isPaidSupplier($user, $orderResult, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');

        $results = false;

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
        
        if($orderResult->tesoriere_stato_pay=='Y') 
            $results = true;
        else 
            $results = false;
    
        if($debug) debug("LifeCycleOrdersTable::isPaidSupplier order_id [".$orderResult->id."] ".$orderResult->state_code." - order.tesoriere_stato_pay ".$orderResult->tesoriere_stato_pay." => esito ".$results);

        return $results;    
    }
    
    public function msgGgArchiveStatics($user, $orderResult, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');

        $results = [];
        $delta_gg = 0;
        
        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
        
        if($orderResult->state_code=='CLOSE' && $orderResult->data_state_code_close!=Configure::read('DB.field.date.empty')) { 
            
            $data_state_code_close = $orderResult->data_state_code_close;
            $ggArchiveStatics = $user->organization->template->ggArchiveStatics;
            $data_statistiche = date('Y-m-d', strtotime($data_state_code_close . ' +'.$ggArchiveStatics.' day'));
            $data_oggi = date('Y-m-d');
            $datetime1 = new DateTime($data_oggi);
            $datetime2 = new DateTime($data_statistiche);
            $interval = $datetime1->diff($datetime2);
            
            if($interval->invert) {
                // non visualizzo perche' data maggiore di 
                $results['mailto'] = Configure::read('SOC.mail');
                $results['mgs'] = 'Dovrebbe essere in statistiche, segnalalo!';
                $results['class'] = 'label label-danger';
            }
            else
            if($interval->days==0) {
                $results['mgs'] = "In statistiche oggi";
                $results['class'] = 'label label-info';
            }
            else {
                $results['mgs'] = "In statistiche tra ".$interval->format('%a gg');
                $results['class'] = 'label label-info';
            }
                
            if($debug) debug("LifeCycleOrdersTable::msgGgArchiveStatics order_id [".$orderResult->id."] ".$orderResult->state_code." data_statistiche ".$data_statistiche);
        }

        return $results;    
    }
    
    /*
     * ctrl se in Order::index far compare il btn che consiglia il passagio allo stato successivo
     * escludo i gas con payToDelivery ON-POST
     */ 
    public function getOrderStateNext($user, $orderResult, $isReferenteTesoriere=false, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');

        $results = [];

        if($user->organization->template->payToDelivery!='ON-POST') {
        
            $class_css = 'label label-info';
            
            if(!is_object($orderResult))
                $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);

            $stateCodeAfter = $this->stateCodeAfter($user, $orderResult, $orderResult->state_code, $debug);
            if($debug) debug("LifeCycleOrdersTable::getOrderStateNext order_id [".$orderResult->id."] ".$orderResult->state_code." - stateCodeAfter ".$stateCodeAfter);

            if($stateCodeAfter=='CLOSE') {
                $canStateCodeToClose = $this->canStateCodeToClose($user, $orderResult);
                if(!$canStateCodeToClose)
                    $stateCodeAfter = '';
                else {
                    $class_css = 'label label-danger';
                }
            }
        
            /*
             * il btn "Riportalo 'in carico al referente' solo se sono WAIT-PROCESSED-TESORIERE 
             */
            if($orderResult->state_code=='RI-OPEN-VALIDATE' && ($user->organization->template->payToDelivery=='ON' || $user->organization->template->payToDelivery=='POST')) {
                $stateCodeAfter = '';
            }
            else 
            if($orderResult->state_code=='PROCESSED-BEFORE-DELIVERY') {
                $stateCodeAfter = '';
            }
            else 
            if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { //  In carico al cassiere durante la consegna
                $stateCodeAfter = '';
            }
            else 
            if($orderResult->state_code=='WAIT-PROCESSED-TESORIERE') {
                $stateCodeAfter = 'PROCESSED-POST-DELIVERY';
            }
            
            /*
             * ottengo i dati del controller per creare il link
             */
            if(!empty($stateCodeAfter)) {
                        
                $ordersActionsTable = TableRegistry::get('OrdersActions');
        
                $options = [];
                $options['conditions'] = ['OrdersAction.state_code_next' => $stateCodeAfter];
                $options['recursive'] = -1;
                $ordersActionResults = $ordersActionsTable->find('first', $options);
                if(!empty($ordersActionResults)) {
                    $i=0;
                    $results[$i]['label'] = __('GoToOrderState'.$stateCodeAfter); // Merce arrivata
                    $results[$i]['action'] = ['controller' => $ordersActionResults['OrdersAction']['controller'], 'action' => $ordersActionResults['OrdersAction']['action'], null, 'delivery_id='.$orderResult->delivery_id, 'order_id='.$orderResult->id];
                    $results[$i]['options'] = ['class' => $class_css, 'title' => __('GoToOrderState'.$stateCodeAfter)];
                }
            }
        } // end if($user->organization->template->payToDelivery!='ON-POST')
                
        return $results;  
    }
    
    /*
     * ctrl se si puo' forzare la chiusura di un ordine
     *  Organization.orderForceClose=='Y'
     * => in Order::index compare il btn
     */
    public function canStateCodeToClose($user, $orderResult, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');

        $results = false;
        
        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);

        if($debug) debug("LifeCycleOrdersTable::canStateCodeToClose order_id [".$orderResult->id."] ".$orderResult->state_code);
        
        if($user->organization->template->orderForceClose=='Y') {
            /*
             * in base al template ctrl chi ha abilitato Orders::close
             */
            $templatesOrdersStatesOrdersActionsTable = TableRegistry::get('TemplatesOrdersStatesOrdersActions');
    
            $options = [];
            $options['conditions'] = ['TemplatesOrdersStatesOrdersAction.template_id' =>  $user->organization->template_id,
                                      'TemplatesOrdersStatesOrdersAction.group_id' => Configure::read('group_id_super_referent'), // prendo quello di un gruppo tanto solo =
                                      'OrdersAction.controller' => 'Orders',
                                      'OrdersAction.action' => 'CLOSE'];  
            $options['fields'] = ['TemplatesOrdersStatesOrdersAction.state_code'];
            $options['recursive'] = 0;
            $templatesOrdersStatesOrdersActionResults = $templatesOrdersStatesOrdersActionsTable->find('all', $options);
            
            if($debug) debug($templatesOrdersStatesOrdersActionResults);
            
            if(!empty($templatesOrdersStatesOrdersActionResults)) {
                foreach($templatesOrdersStatesOrdersActionResults as $templatesOrdersStatesOrdersActionResult) {
                    if($templatesOrdersStatesOrdersActionResult['TemplatesOrdersStatesOrdersAction']['state_code']==$orderResult->state_code) {
                        $results = true;
                        break;
                    }
                }
            } // if(!empty($templatesOrdersStatesOrdersActionResults)) 
            
        } // end if($user->organization->template->orderForceClose=='Y')        
        
        return $results;    
    }
    
    public function canOrdersClose($user, $orderResult, $debug=false) {
    
        $ordersTable = TableRegistry::get('Orders');

        $results = false;
            
        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);

        if($debug) debug("LifeCycleOrdersTable::canOrdersClose order_id [".$orderResult->id."] ".$orderResult->state_code);
        
        if(!isset($user->organization->paramsConfig['canOrdersClose']))
            $user->organization->paramsConfig['canOrdersClose'] = 'ALL';

        /*
         * calcolo il totale degli importi degli acquisti dell'ordine
         * se ZERO => non puo' chiuderlo => Si DELETE
         */
        $ordersTable = TableRegistry::get('Orders');    

        $importo_totale = $ordersTable->getTotImporto($user, $orderResult->organization_id, $orderResult->id);
        if($importo_totale==0 || $importo_totale==Configure::read('DB.field.double.empty') || $importo_totale=='0,00')
            return false;
        else {
            /*
             * salvo totImporto cosi' il msg in beforeRendering e' aggiornato
             */
            $orderResult->tot_importo = $importo_totale;         
            if(!$ordersTable->save($orderResult)) {
                $errors = $this->validationErrors;
                if($debug) debug("LifeCycleOrdersTable::canOrdersClose order_id [".$orderResult->id."] ERROR salvando l'ordine");
                if($debug) debug($errors);
                
                $esito['CODE'] = "500";
                $esito['MSG'] = $errors;
                return $esito;  
            }
        }
        
        if($orderResult->delivery->sys=='Y')
            return false;
        
        if($debug) debug('canOrdersClose '.$user->organization->paramsConfig['canOrdersClose']); 
        if($debug) debug($user->getAuthorisedGroups()); 
        if($debug) debug(['group_id_super_referent '.Configure::read('group_id_super_referent'), 'group_id_referent '.Configure::read('group_id_referent')]);
            
        switch($user->organization->paramsConfig['canOrdersClose']) {
            case 'ALL':
                $results = true;
            break;
            case 'SUPER-REFERENT':
                if (in_array(Configure::read('group_id_super_referent'), $user->getAuthorisedGroups()))
                    $results = true;
            break;
            case 'REFERENT':
                if (in_array(Configure::read('group_id_referent'), $user->getAuthorisedGroups())) 
                    $results = true;
            break;
        }
        
        return $results;
    }
    
    public function canOrdersDelete($user, $debug=false) {
        
        $results = false;
    
        if(!isset($user->organization->paramsConfig['canOrdersDelete']))
            $user->organization->paramsConfig['canOrdersDelete'] = 'ALL';
            
        if($debug) debug('canOrdersDelete '.$user->organization->paramsConfig['canOrdersDelete']); 
        if($debug) debug($user->getAuthorisedGroups()); 
        if($debug) debug(['group_id_super_referent '.Configure::read('group_id_super_referent'), 'group_id_referent '.Configure::read('group_id_referent')]);
            
        switch($user->organization->paramsConfig['canOrdersDelete']) {
            case 'ALL':
                $results = true;
            break;
            case 'SUPER-REFERENT':
                if (in_array(Configure::read('group_id_super_referent'), $user->getAuthorisedGroups()))
                    $results = true;
            break;
            case 'REFERENT':
                if (in_array(Configure::read('group_id_referent'), $user->getAuthorisedGroups()))
                    $results = true;
            break;
        }
        
        return $results;    
    }
    
   /*
     * Ajax::admin_view_orders
     * in base allo stato dell'ordine
     * setto l'action possibile sull'ordine
     */
    public function actionToEditOrder($user, $results) {

        $actionToEditOrder = [];

        if (isset($results['Order'])) {

            if ($this->isUserPermissionArticlesOrder($user)) { // l'utente gestisce l'associazione degli articoli con l'ordine
                switch ($results['Order']['state_code']) {
                    case 'CREATE-INCOMPLETE':
                        $actionToEditOrder = ['controller' => 'ArticlesOrders', 'action' => 'admin_add', 'title' => __('Add ArticlesOrder Error')];
                    break;
                    case 'OPEN':
                    case 'OPEN-NEXT':
                    case 'PROCESSED-BEFORE-DELIVERY':
                    case 'PROCESSED-ON-DELIVERY':
                    case 'PROCESSED-POST-DELIVERY':
                        $actionToEditOrder = ['controller' => 'ArticlesOrders', 'action' => 'admin_index', 'title' => __('List Articles Orders')];
                    break;
                    default:
                        $actionToEditOrder = [];
                    break;
                    
                }
            }
            else {  // l'utente non gestisce l'associazione degli articoli con l'ordine
                switch ($results['Order']['state_code']) {
                    case 'WAIT-PROCESSED-TESORIERE':
                    case 'PROCESSED-TESORIERE':
                    case 'TO-REQUEST-PAYMENT':              
                    case 'TO-PAYMENT':
                    case 'USER-PAID':
                    case 'SUPPLIER-PAID':
                    case 'WAIT-REQUEST-PAYMENT-CLOSE':
                    case 'CLOSE':
                        $actionToEditOrder = [];
                    break;
                    default:
                        $actionToEditOrder = ['controller' => 'Articles', 'action' => 'context_order_index', 'title' => __('List Articles')];
                    break;
                }     
            }
        }

        return $actionToEditOrder;
    }

    /*
     * Ajax::admin_view_orders
     * in base allo stato dell'ordine
     * setto l'action possibile di un articolo
     */
    public function actionToEditArticle($user, $results) {

        $actionToEditArticle = [];
        if (isset($results['Order'])) {

            if ($this->isUserPermissionArticlesOrder($user)) {  // l'utente gestisce l'associazione degli articoli con l'ordine
                switch ($results['Order']['state_code']) {
                    case 'CREATE-INCOMPLETE':
                        $actionToEditOrder = ['controller' => 'ArticlesOrders', 'action' => 'admin_add', 'title' => __('Add ArticlesOrder Error')];
                    break;
                    case 'OPEN':
                    case 'OPEN-NEXT':
                    case 'PROCESSED-BEFORE-DELIVERY':
                    case 'PROCESSED-ON-DELIVERY':
                    case 'PROCESSED-POST-DELIVERY':
                        $actionToEditOrder = ['controller' => 'ArticlesOrders', 'action' => 'admin_edit', 'title' => __('Edit ArticlesOrder')];
                    break;
                    default:
                        $actionToEditOrder = [];
                    break;
                    
                }
            }
            else { // l'utente non gestisce l'associazione degli articoli con l'ordine
                switch ($results['Order']['state_code']) {
                    case 'WAIT-PROCESSED-TESORIERE':
                    case 'PROCESSED-TESORIERE':
                    case 'TO-REQUEST-PAYMENT':              
                    case 'TO-PAYMENT':
                    case 'USER-PAID':
                    case 'SUPPLIER-PAID':
                    case 'WAIT-REQUEST-PAYMENT-CLOSE':
                    case 'CLOSE':
                        $actionToEditOrder = [];
                    break;
                    default:
                        $actionToEditArticle = ['controller' => 'Articles', 'action' => 'admin_context_order_edit', 'title' => __('Edit Article')];
                    break;
                } 
            }
        }

        return $actionToEditArticle;
    }
    
    /*
     * estrae lo stato SUCCESSIVO di un Ordine in base al template
     */
    public function stateCodeAfter($user, $orderResult, $state_code, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');
        $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');

        $state_code_next = '';
        $rule_sort_next = 1; 

        if(!is_object($orderResult))
            $orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);

        $template_id = $user->organization->template_id;

        if($debug) debug('LifeCycleOrdersTable::stateCodeAfter template_id '.$template_id);
        if($debug) debug($user->organization->template);
        if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' Order.state_code CURRENT '.$state_code);

        switch ($state_code) {
            
            case 'PROCESSED-ON-DELIVERY':  // In carico al cassiere durante la consegna
           
                if(!$lifeCycleSummaryOrdersTable->isSummaryOrderAllSaldato($user, $orderResult, $debug)) { // ctrl se e' stato saldato da tutti i gasisti
                    // rimane invariato
                    if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." NON saldato da parte di tutti i gasisti => NON aggiorno lo stato ordine, rimane ".$orderResult->state_code);
                    
                    $state_code_next = $orderResult->state_code;
                }   
                else {          
                    if($user->organization->template->orderSupplierPaid=='Y') {

                        /*
                         * ctrl se il produttore e' pagato
                         */ 
                        $isPaidSupplier = $this->isPaidSupplier($user, $orderResult, $debug);

                        if($isPaidSupplier) {
                            $state_code_next = 'CLOSE';
                            if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." produttore PAGATO => estraggo lo stato $rule_sort_next di un Ordine in base al template");
                        }
                        else { 
                            $state_code_next = 'SUPPLIER-PAID'; 
                            if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." produttore NON PAGATO => estraggo lo stato $rule_sort_next di un Ordine in base al template");
                        }
                    }
                    else 
                        $state_code_next = 'CLOSE';
                } // end if(!$lifeCycleSummaryOrdersTable->isSummaryOrderAllSaldato($user, $orderResult, $debug)) 
            break;
            /* 
             * Template.payToDelivery = POST / ON-POST 
             *  da USER-PAID => saldato da tutti i gasisti = N => rimane USER-PAID
             *  da USER-PAID => saldato da tutti i gasisti = Y => WAIT-REQUEST-PAYMENT-CLOSE 
             *
             * Template.payToDelivery = ON => mai, ha gli stati (PROCESSED-ON-DELIVERY, SUPPLIER-PAID)
             */             
            case 'USER-PAID':  // Da saldare da parte dei gasisti (solo per gestione con Tesoriere) 
            
                if(!$lifeCycleSummaryOrdersTable->isSummaryOrderAllSaldato($user, $orderResult, $debug)) { // ctrl se e' stato saldato da tutti i gasisti
                    // rimane invariato
                    if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." NON saldato da parte di tutti i gasisti => NON aggiorno lo stato ordine, rimane ".$orderResult->state_code);
                    
                    $state_code_next = 'USER-PAID';
                }   
                else {
                    if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." saldato da parte di tutti i gasisti => WAIT-REQUEST-PAYMENT-CLOSE");
                
                    $state_code_next = 'WAIT-REQUEST-PAYMENT-CLOSE';
                } 
            break;
            /*
             * 
             * Template.payToDelivery = POST / ON-POST 
             *
             * in $RequestPaymentsOrder->setOrdersStateCodeByRequestPaymentId() calcolato che 
             * tutti gli ordini della rich sono in state_code WAIT-REQUEST-PAYMENT-CLOSE => calcolo se SUPPLIER-PAID o CLOSE
             *
             * Template.payToDelivery = ON => mai, ha gli stati (PROCESSED-ON-DELIVERY, SUPPLIER-PAID)
             */                             
            case 'WAIT-REQUEST-PAYMENT-CLOSE':     //  (solo per gestione con Tesoriere)
            case 'WAIT-REQUEST-PAYMENT-CLOSE-ALL': //  (solo per gestione con Tesoriere)    
            
                if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." saldato da parte di tutti i gasisti");
            
                if($user->organization->template->orderSupplierPaid=='Y') {
                    
                    /*
                     * ctrl se il produttore e' pagato
                     */                      
                     $isPaidSupplier = $this->isPaidSupplier($user, $orderResult, $debug);

                     if($isPaidSupplier) 
                        $state_code_next = 'CLOSE';
                     else 
                        $state_code_next = 'SUPPLIER-PAID';
                
                     if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." produttore PAGATO => estraggo lo stato $rule_sort_next di un Ordine in base al template");
                }
                else 
                    $state_code_next = 'CLOSE';
            break;          
            case 'SUPPLIER-PAID':
                if($user->organization->template->orderUserPaid=='Y') {
     
                    /*
                     * ctrl se il produttore e' pagato
                     */ 
                     $paidUsersResults = $this->getPaidUsers($user, $orderResult, $debug);
                     if($debug) debug($paidUsersResults);
                     
                     if(!empty($paidUsersResults) && $paidUsersResults['totalSummaryOrderNotPaid']==0) {
                        if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." hanno SALDATO tutti => estraggo lo stato posizionato con SORT $rule_sort_next di un Ordine in base al template");
                        $rule_sort_next = 1;
                     }
                     else {
                        if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." NON hanno SALDATO tutti => NON aggiorno lo stato ordine, rimane ".$orderResult->state_code);
                        $state_code_next = $orderResult->state_code;
                     }
                }
            break;
            default:
                if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." Order.state_code [".$state_code."] non previsto");
            break;          
        }

        if(empty($state_code_next)) {
            /*
             * non ancora definito, lo calcolo con calcolo del sort precedente o successivo
             */
            if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." ricerco Order.state_code posizionato con SORT $rule_sort_next a ".$state_code);
                
            $templatesOrdersStatesTable = TableRegistry::get('TemplatesOrdersStates'); 
    
            $where = [];
            $where = ['TemplatesOrdersStates.template_id' => $template_id,
                      'TemplatesOrdersStates.state_code' => $state_code,
                      'TemplatesOrdersStates.group_id' => Configure::read('group_id_super_referent')]; // prendo quello di un gruppo tanto solo = 
            $results = $templatesOrdersStatesTable->find()
                                                  ->select(['TemplatesOrdersStates.sort'])
                                                  ->where($where)
                                                  ->first();
            /*
             * calcolo il sort precedente o successivo
             */
            $sort_next = ($results['TemplatesOrdersState']['sort'] + ($rule_sort_next));
             
            /*
             * ottengo i successivi e restituisco il primo
             */
            $where = [];
            $where = ['TemplatesOrdersStates.template_id' => $template_id,
                      'TemplatesOrdersStates.sort' => $sort_next,
                      'TemplatesOrdersStates.group_id' => Configure::read('group_id_super_referent')]; // prendo quello di un gruppo tanto solo = 
            $results = $templatesOrdersStatesTable->find()
                                                  ->where($where)
                                                  ->order(['TemplatesOrdersStates.sort asc'])
                                                  ->all();
            foreach($results as $result) {
                $state_code_next = $result->state_code;   
                break; // restituisco il primo
            }

            if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." state_code_next ".$state_code_next." => ctrl se e' valido o lo ricalcolo");
                    
            /*
             * ctrl se state_code_next e' valido, ex SUPPLIER-PAID ma e' gia' saldato
             */
            switch ($state_code_next) {
                
                /* 
                 * Templates.payToDelivery = POST / ON-POST 
                 *  da USER-PAID => saldato da tutti i gasisti = N => rimane USER-PAID
                 *  da USER-PAID => saldato da tutti i gasisti = Y => WAIT-REQUEST-PAYMENT-CLOSE 
                 *
                 * Templates.payToDelivery = ON => mai, ha gli stati (PROCESSED-ON-DELIVERY, SUPPLIER-PAID)
                 */             
                case 'USER-PAID':  // Da saldare da parte dei gasisti (solo per gestione con Tesoriere) 
                
                    if(!$lifeCycleSummaryOrdersTable->isSummaryOrderAllSaldato($user, $orderResult, $debug)) { // ctrl se e' stato saldato da tutti i gasisti
                        // rimane invariato
                        if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." NON saldato da parte di tutti i gasisti => NON aggiorno lo stato ordine, rimane ".$orderResult->state_code);
                        
                        $state_code_next = 'USER-PAID';
                    }   
                    else {
                        if($debug) debug("LifeCycleOrdersTable::stateCodeAfter - Order.id ".$orderResult->id." saldato da parte di tutti i gasisti => WAIT-REQUEST-PAYMENT-CLOSE");
                    
                        $state_code_next = 'WAIT-REQUEST-PAYMENT-CLOSE';
                    } 
                break;
                case 'SUPPLIER-PAID':
                    if($user->organization->template->orderUserPaid=='Y') {
                        
                        /*
                         * ctrl se il produttore e' pagato
                         */ 
                         $paidUsersResults = $this->getPaidUsers($user, $orderResult, $debug);
                         if($debug) debug($paidUsersResults);
                         
                         if(!empty($paidUsersResults) && $paidUsersResults['totalSummaryOrderNotPaid']==0) {
                            if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." hanno SALDATO tutti => estraggo lo stato posizionato con SORT $rule_sort_next di un Ordine in base al template");
                            $state_code_next = 'CLOSE';
                         }
                         else {
                            if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." NON hanno SALDATO tutti => NON aggiorno lo stato ordine, rimane ".$orderResult->state_code);
                            $state_code_next = $orderResult->state_code;
                         }
                    }
                break;
                default:
                break;          
            }            
        }
                
        if($debug) debug('LifeCycleOrdersTable::stateCodeAfter Order.id '.$orderResult->id.' template_id '.$template_id." state_code_next ".$state_code_next);
    
        return $state_code_next;        
    }
    
    private function _orderAddValue($orderResult, $state_code_next, $opts=[], $debug) {
        
        $esito = [];
        
        switch($state_code_next) {
            case 'RI-OPEN-VALIDATE':
                if(!isset($opts['data_fine_validation'])) {
                    $esito['CODE'] = "500";
                    $esito['MSG'] = "data_fine_validation non valorizzato";
                    return $esito; 
                }   
                    
                $orderResult->data_fine_validation = $opts['data_fine_validation'];              
            break;
            case 'WAIT-PROCESSED-TESORIERE':
                if(isset($opts['tesoriere_doc1']))
                    $orderResult->tesoriere_doc1 = $opts['tesoriere_doc1'];
            break;
            case 'PROCESSED-POST-DELIVERY':
            break;
            case 'INCOMING-ORDER':  // merce arrivata
                if(!isset($opts['data_incoming_order'])) 
                    $opts['data_incoming_order'] = date('Y-m-d');
                    
                $orderResult->data_incoming_order = $opts['data_incoming_order'];

            break;          
            case 'CLOSE':
                if(isset($opts['tot_importo'])) 
                    $orderResult->tot_importo = $opts['tot_importo'];
                
                if(isset($opts['inviato_al_tesoriere_da'])) 
                    $orderResult->inviato_al_tesoriere_da = $opts['inviato_al_tesoriere_da'];
                
                if(empty($orderResult->tot_importo)) {
                    $esito['CODE'] = "500";
                    $esito['MSG'] = "Order.tot_importo non valorizzato";
                    return $esito; 
                }
                if(empty($orderResult->inviato_al_tesoriere_da)) {
                    $esito['CODE'] = "500";
                    $esito['MSG'] = "Order.inviato_al_tesoriere_da non valorizzato";
                    return $esito; 
                }   
                             
                if(isset($opts['tesoriere_data_pay']))
                    $orderResult->tesoriere_data_pay = $opts['tesoriere_data_pay'];
                if(isset($opts['tesoriere_importo_pay']))
                    $orderResult->tesoriere_importo_pay = $opts['tesoriere_importo_pay'];
                if(isset($opts['tesoriere_fattura_importo']))
                    $orderResult->tesoriere_fattura_importo = $opts['tesoriere_fattura_importo'];
                if(isset($opts['tesoriere_stato_pay']))
                    $orderResult->tesoriere_stato_pay = $opts['tesoriere_stato_pay'];
            break;
            default:
                
            break;          
        }
        
        return $orderResult;
    }
    
    /*
     * stati dell'ordine che non permettono l'aggiornamneto dell'anagrafica di un articolo
     *  Article::syncronizeArticlesOrder()
     */
    public function getStateCodeNotUpdateArticle($user) {
        
        $results[] = 'PROCESSED-TESORIERE';
        $results[] = 'TO-REQUEST-PAYMENT';      
        $results[] = 'TO-PAYMENT';  
        $results[] = 'WAIT-REQUEST-PAYMENT-CLOSE';
        $results[] = 'USER-PAID';               
        $results[] = 'SUPPLIER-PAID';       
        $results[] = 'CLOSE';
        
        return $results;
    }
    
    /*
     * stati dell'ordine che indicano la gestione dei pagamenti
     */ 
    public function getStateCodeManagementPayments($user) {

        $results[] = 'PROCESSED-ON-DELIVERY';
        $results[] = 'TO-PAYMENT';      
        $results[] = 'WAIT-REQUEST-PAYMENT-CLOSE';
        $results[] = 'USER-PAID';               
        $results[] = 'SUPPLIER-PAID';   
        
        return $results;
    }

    /*
     * stati dell'ordine che li escludono per calcolare gli acquisti di un gasista
     */     
    public function getStateCodeUsersCash($user) {

        $results[] = 'CLOSE';   
        
        return $results;
    }
    
    public function getStateCodeNotUpdateArticleToSql($user) {
        
        $results = $this->getStateCodeNotUpdateArticle($user);
        
        $tmp = "";
        foreach($results as $result) {
            $tmp .= "'".$result."',";
        }

        $tmp = substr($tmp, 0, (strlen($tmp)-1));
        
        return $tmp;
    }
     
    /* 
     * ctrl se e' ordine chiuso agli acquisti
     * se true l'importo e' carts.qta * article_orders.prezzo
     * se false l'importo e' carts.final_price
     */     
    public function isOpenToPurchasable($user, $order_state_code) {
        
        if($order_state_code == 'OPEN' ||  
            $order_state_code == 'RI-OPEN-VALIDATE') 
            return true;
        else
            return false;
    }

    /*
     * $modulo: sono in quel modulo e ctrl se ho anche altri moduli che possono andare in conflitto
     *          managementCartsOne (Gestisci gli acquisti nel dettaglio) con 
     *              Order.typeGest.AGGREGATE per SummaryOrder
     *              Order.typeGest.SPLIT     per Order.qta
     * 
     *              Order.trasport
     *              Order.hasCostMore
     *              Order.hasCostLess
     * 
     *          managementCartsGroupByUsers (Gestisci gli acquisti aggregati per importo) con 
     *              Order.trasport
     *              Order.hasCostMore
     *              Order.hasCostLess
     */
    private function _ctrlModuleConflicts($user, $orderResult, $modulo, $debug) {

        $results = [];
        $results['alertModuleConflicts'] = '';

        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts order_id [".$orderResult->id."] state_code ".$orderResult->state_code);
        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts modulo ".$modulo);

        switch ($modulo) {
            case 'managementCartsOne':
                if ($orderResult->typeGest == 'AGGREGATE') {

                    $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');

                    $summaryOrderAggregateorderResult = $summaryOrderAggregatesTable->select_to_order($user, $orderResult->id);
                    if (!empty($summaryOrderAggregateorderResult)) 
                        $results['alertModuleConflicts'] = 'summary_order_just_populate';
                }
                else
                if ($orderResult->typeGest == 'SPLIT') {
                    $results['alertModuleConflicts'] = 'order_change_qta';
                }

                if (empty($results['alertModuleConflicts'])) {
                    if (($orderResult->hasTrasport == 'Y' && $orderResult->trasport != '0.00') ||
                            ($orderResult->hasCostMore == 'Y' && $orderResult->cost_more != '0.00') ||
                            ($orderResult->hasCostLess == 'Y' && $orderResult->cost_less != '0.00'))
                        $results['alertModuleConflicts'] = 'order_change_carts_one';
                }
                break;
            case 'managementCartsGroupByUsers':
                if (($orderResult->hasTrasport == 'Y' && $orderResult->trasport != '0.00') ||
                        ($orderResult->hasCostMore == 'Y' && $orderResult->cost_more != '0.00') ||
                        ($orderResult->hasCostLess == 'Y' && $orderResult->cost_less != '0.00'))
                    $results['alertModuleConflicts'] = 'summary_order_change';
                break;
        }

        if ($orderResult->typeGest == 'AGGREGATE')
            $results['orderHasSummaryOrderAggregate'] = 'Y';
        else
            $results['orderHasSummaryOrderAggregate'] = 'N';
        
        if ($orderResult->hasTrasport == 'Y' && $orderResult->trasport != '0.00')
            $results['orderHasTrasport'] = 'Y';
        else
            $results['orderHasTrasport'] = 'N';

        if ($orderResult->hasCostMore == 'Y' && $orderResult->cost_more != '0.00')
            $results['orderHasCostMore'] = 'Y';
        else
            $results['orderHasCostMore'] = 'N';

        if ($orderResult->hasCostLess == 'Y' && $orderResult->cost_less != '0.00')
            $results['orderHasCostLess'] = 'Y';
        else
            $results['orderHasCostLess'] = 'N';

        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts alertModuleConflicts ".$results['alertModuleConflicts']);
        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts orderHasSummaryOrderAggregate ".$results['orderHasSummaryOrderAggregate']);
        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts orderHasTrasport ".$results['orderHasTrasport']);
        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts orderHasCostMore ".$results['orderHasCostMore']);
        if($debug) debug("LifeCycleOrdersTable::_ctrlModuleConflicts orderHasCostLess ".$results['orderHasCostLess']);

        return $results;
    }
        
    /*
     * al'ordine e' cambiata la consegna,
     * aggiorno con il nuovo delivery_id le tabelle
     *
     * k_summary_orders                      
     * k_request_payments_orders
     */             
    private function _updateTablesToChangeDeliverId($user, $order_id, $delivery_id, $debug=false) {

        try {
            $sql = "UPDATE ".Configure::read('DB.prefix')."summary_orders  
                    SET delivery_id = $delivery_id
                    WHERE 
                        organization_id = ".(int)$user->organization->id."
                        and order_id = ".(int)$order_id;
            if($debug) debug($sql);
            $results = $this->query($sql);
            

            $sql = "UPDATE ".Configure::read('DB.prefix')."request_payments_orders 
                    SET delivery_id = $delivery_id
                    WHERE 
                        organization_id = ".(int)$user->organization->id."
                        and order_id = ".(int)$order_id;
            if($debug) debug($sql);
            $results = $this->query($sql);
        }
        catch (Exception $e) {
            CakeLog::write('error',$e);
            return false;
        }
        
        return true;
    }
    
    /*
     * ctrl se il referente puo' trasmettre al cassiere / tesoriere
     * se configurati 
     *  i dati aggragati => se li ha compilati
     *  trasporto => se li ha compilati ...
     */
    private function _isOrderValidateToTrasmit($user, $orderResult, $controller, $action, $debug=false) {

        $esito = [];
        $continua = true;
        $controller_action_validates = [['Referente', 'admin_order_state_in_WAIT_PROCESSED_TESORIERE'], // referente => tesoriere
                                        ['Referente', 'admin_order_state_in_PROCESSED_ON_DELIVERY'],    // referente => cassiere
                                        ['Cassiere', 'admin_order_state_in_WAIT_PROCESSED_TESORIERE']]; // cassiere => tesoriere
        
        if(!$this->_ctrlMethodValid($controller_action_validates, $controller, $action)) 
            return $esito;
        
        $ajaxGasCodesTable = TableRegistry::get('AjaxGasCodes');
            
        /*
         * TESORIERE - Se Delivery.sys == 'Y' (consegna da definire) in 'WAIT-PROCESSED-TESORIERE' non posso editare l'ordine
         * CASSIERE -  Se Delivery.sys == 'Y' (consegna da definire) in 'PROCESSED-ON-DELIVERY' non posso editare l'ordine
         */
        if($orderResult->delivery->sys=='Y') {
            $esito['msg'] = "L'ordine è associato ad una consegna ancora da definire<br />e non può essere trasmesso al cassiere/tesoriere";
            return $esito; 
        }
            
        switch ($orderResult->state_code) {
            case 'PROCESSED-POST-DELIVERY':
            case 'PROCESSED-ON-DELIVERY':   // in carico al cassiere durante la consegna
                $destinatario = 'Tesoriere';
            break;
            case 'INCOMING-ORDER':  // merce arrivata
                $destinatario = 'Cassiere';
            break;
            default:
                $destinatario = 'Tesoriere o al Cassiere';
            break;
        }

        if($continua && $orderResult->typeGest=='AGGREGATE') {
                
            if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] typeGest ".$orderResult->typeGest);
        
            /*
             *  dati aggregati
             */     
            $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');
         
            $totale = $summaryOrderAggregatesTable->select_totale_importo_to_order($user, $orderResult->id, $debug);
            if(floatval($totale)==0) {
                if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                    $esito['actions'][1]['msg'] = "L'ordine gestisce i <b>dati aggregati</b> ma il referente non li ha gestiti, clicca qui rimandare l'ordine al referente";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                    $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                }
                else {
                    $esito['actions'][1]['msg'] = "L'ordine gestisce i <b>dati aggregati</b> ma non li hai gestiti, clicca qui gestirli";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=SummaryOrderAggregates&action=managementCartsGroupByUsers&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionEditDbGroupByUsers';
                    $esito['actions'][1]['action_label']= __('Management Carts Group By Users Short');
                }
                $continua = false;
                
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati aggregati NON completi => KO");
            }
            else {
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati aggregati completi => OK");
            }
        }
        
        if($continua && $orderResult->hasTrasport=='Y' && floatval($orderResult->trasport) > 0) {
                
            if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] hasTrasport ".$orderResult->hasTrasport." ".$orderResult->trasport);
        
            /*
             *  trasporto
             */     
            $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');

            $totale = $summaryOrderTrasportsTable->select_totale_importo_trasport($user, $orderResult->id, $debug);
            if(floatval($totale)==0) {
                if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                    $esito['actions'][1]['msg'] = "L'ordine gestisce il <b>trasporto</b> ma il referente l'hai suddiviso per i gasisti, clicca qui rimandare l'ordine al referente";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                    $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                }
                else {                  
                    $esito['actions'][1]['msg'] = "L'ordine gestisce il <b>trasporto</b> ma non l'hai suddiviso per i gasisti, clicca qui suddividerlo";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=trasport&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionTrasport';
                    $esito['actions'][1]['action_label']= __('Management trasport');
                }
                $continua = false;
                
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati trasporto NON completi => KO");
            }
            
            if($continua) {
                /*
                 * ctrl che i calcoli effettuati siano coerenti con il totale acquisti (non fatte modifiche successive)
                 * if($totImporto_ != $results['SummaryOrder...']['importo_']) 
                 */             
                $results = $ajaxGasCodesTable->getSummaryOrderTrasportValidate($user, $orderResult, $debug);
                if(isset($results['results']) && !empty($results['results'])) {
                    if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                        $esito['actions'][1]['msg'] = "L'ordine gestisce il <b>trasporto</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui rimandare l'ordine al referente";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                        $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                        $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                    }
                    else {                  
                        $esito['actions'][1]['msg'] = "L'ordine gestisce il <b>trasporto</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui correggere quelli evidenziati in <b>rosso</b>";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=trasport&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id; 
                        $esito['actions'][1]['action_class'] = 'actionTrasport';
                        $esito['actions'][1]['action_label']= __('Management trasport');
                    }
                    $continua = false;                  
                }           
            }
            else  
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati trasporto completi => OK");          
        }
                
        if($continua && $orderResult->hasCostMore=='Y' && floatval($orderResult->cost_more) > 0) {
            
            if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] hasCostMore ".$orderResult->hasCostMore." ".$orderResult->cost_more);

            /*
             *  costo aggiuntivo
             */
            $summaryOrderCostMoresTable = TableRegistry::get('SummaryOrderCostMores');
             
            $totale = $summaryOrderCostMoresTable->select_totale_importo_cost_more($user, $orderResult->id, $debug);
            if(floatval($totale)==0) {
                if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                    $esito['actions'][1]['msg'] = "L'ordine gestisce un <b>costo aggiuntivo</b> ma il referente non l'ha suddiviso per i gasisti, clicca qui rimandare l'ordine al referente";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                    $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                }
                else {              
                    $esito['actions'][1]['msg'] = "L'ordine gestisce un <b>costo aggiuntivo</b> ma non l'hai suddiviso per i gasisti, clicca qui suddividerlo";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=cost_more&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionCostMore';
                    $esito['actions'][1]['action_label'] = __('Management cost_more');
                }
                $continua = false;
                
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati costo aggiuntivo NON completi => KO");
            }
            
            if($continua) {
                /*
                 * ctrl che i calcoli effettuati siano coerenti con il totale acquisti (non fatte modifiche successive)
                 * if($totImporto_ != $results['SummaryOrder...']['importo_']) 
                 */
                $results = $ajaxGasCodesTable->getSummaryOrderCostMoreValidate($user, $orderResult, $debug);  
                if(isset($results['results']) && !empty($results['results'])) {
                    if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                        $esito['actions'][1]['msg'] = "L'ordine gestisce un <b>costo aggiuntivo</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui rimandare l'ordine al referente";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                        $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                        $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                    }
                    else {                          
                        $esito['actions'][1]['msg'] = "L'ordine gestisce un <b>costo aggiuntivo</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui correggere quelli evidenziati in <b>rosso</b>";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=cost_more&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                        $esito['actions'][1]['action_class'] = 'actionCostMore';
                        $esito['actions'][1]['action_label'] = __('Management cost_more');
                    }
                    $continua = false;                  
                }           
            }
            else 
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati costo aggiuntivo completi => OK");                   
        }
        
        if($continua && $orderResult->hasCostLess=='Y' && floatval($orderResult->cost_less) > 0) {

            if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] hasCostLess ".$orderResult->hasCostLess." ".$orderResult->cost_less);

            /*
             *  sconto
             */
            $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');
             
            $totale = $summaryOrderCostLessesTable->select_totale_importo_cost_less($user, $orderResult->id, $debug);
            if(floatval($totale)==0) {
                if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                    $esito['actions'][1]['msg'] = "L'ordine gestisce uno <b>sconto</b> ma il referente non l'ha suddiviso per i gasisti, clicca qui rimandare l'ordine al referente";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                    $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                }
                else {              
                    $esito['actions'][1]['msg'] = "L'ordine gestisce uno <b>sconto</b> ma non l'hai suddiviso per i gasisti, clicca qui suddividerlo";
                    $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=cost_less&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                    $esito['actions'][1]['action_class'] = 'actionCostLess';
                    $esito['actions'][1]['action_label'] = __('Management cost_less');
                }
                $continua = false;
                
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati sconto NON completi => KO");
            }

            if($continua) {
                /*
                 * ctrl che i calcoli effettuati siano coerenti con il totale acquisti (non fatte modifiche successive)
                 * if($totImporto_ != $results['SummaryOrder...']['importo_']) 
                 */             
                $results = $ajaxGasCodesTable->getSummaryOrderCostLessValidate($user, $orderResult, $debug);
                if(isset($results['results']) && !empty($results['results'])) {
                    if($orderResult->state_code=='PROCESSED-ON-DELIVERY') { /* se PROCESSED-ON-DELIVERY (in carico al cassiere durante la consegna) devo rimandare l'ordine al referente per completarlo */
                        $esito['actions'][1]['msg'] = "L'ordine gestisce uno <b>sconto</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui rimandare l'ordine al referente";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Referente&action=order_state_in_INCOMING_ORDER&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                        $esito['actions'][1]['action_class'] = 'actionFromTesToRef';
                        $esito['actions'][1]['action_label']= __('OrderGoBackReferente');
                    }
                    else {                      
                        $esito['actions'][1]['msg'] = "L'ordine gestisce uno <b>sconto</b> ma alcuni calcoli si riferiscono a dati che sono stati modificati, clicca qui correggere quelli evidenziati in <b>rosso</b>";
                        $esito['actions'][1]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Carts&action=cost_less&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                        $esito['actions'][1]['action_class'] = 'actionCostLess';
                        $esito['actions'][1]['action_label'] = __('Management cost_less');
                    }
                    $continua = false;                  
                }           
            }
            else 
                if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] dati sconto completi => OK");     
        }
        
        if(!$continua) {
            $esito['msg'] = "L'ordine non può essere trasmesso al $destinatario perchè non è completo!<br />";
            
            if($orderResult->state_code!='PROCESSED-ON-DELIVERY') {
                $esito['actions'][0]['msg'] = "Oppure non desideri più gestirlo, clicca qui per modificare l'anagrafica dell'ordine";
                $esito['actions'][0]['url'] = Configure::read('App.server').'/administrator/index.php?option=com_cake&controller=Orders&action=edit&delivery_id='.$orderResult->delivery_id.'&order_id='.$orderResult->id;
                $esito['actions'][0]['action_class'] = 'actionEdit';
                $esito['actions'][0]['action_label'] = __('Edit Order');
            }
        }
                
        if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id ".$orderResult->id);
        if(!empty($esito))  
            if($debug) debug("LifeCycleOrdersTable::_isOrderValidateToTrasmit order_id [".$orderResult->id."] esito ".print_r($esito, true));
                
        return $esito;
    }
            
    /* 
     * quando chiudo un ordine avviso l'utente che non potra' +...
     */
    private function _msgOrderToClose($user, $orderResult, $controller, $action, $debug) {

        $esito = [];
        $controller_action_validates = [['Orders', 'admin_close']];

        if(!$this->_ctrlMethodValid($controller_action_validates, $controller, $action))
            return $esito;  

        if($debug) debug($orderResult->state_code);
        
        if($orderResult->delivery->sys=='Y') {
            $esito = "Per poter chiudere l'ordine dovrai prima associarlo ad una <b>consegna valida</b>, clicca qui per <a href=\"".Configure::read('App.server')."/administrator/index.php?option=com_cake&controller=Orders&action=edit&delivery_id=".$orderResult->delivery_id."&order_id=".$orderResult->id."\"><b>modificare</b> la consegna associata</a>";
        }
        else {
            if($orderResult->tot_importo>0) {
                switch ($orderResult->state_code) {
                    case 'INCOMING-ORDER':
                        if($user->organization->template->payToDelivery=='POST')
                            $esito = "Se chiudi l'ordine non potrai passarlo al TESORIERE per gestire i pagamenti";
                        else
                        if($user->organization->template->payToDelivery=='ON')
                            $esito = "Se chiudi l'ordine non potrai passarlo al CASSIERE per gestire i pagamenti";
                        else
                        if($user->organization->template->payToDelivery=='ON-POST')
                            $esito = "Se chiudi l'ordine non potrai passarlo al CASSIERE o al TESORIERE per gestire i pagamenti";           
                    break;
                    case 'PROCESSED-ON-DELIVERY':
                        if($user->organization->template->payToDelivery=='POST')
                            $esito = "Se chiudi l'ordine il TESORIERE non potr&agrave; più gestire i pagamenti";
                        else
                        if($user->organization->template->payToDelivery=='ON')
                            $esito = "Se chiudi l'ordine il CASSIERE non potr&agrave; più gestire i pagamenti";
                        else
                        if($user->organization->template->payToDelivery=='ON-POST')
                            $esito = "Se chiudi l'ordine il CASSIERE o il TESORIERE non potr&agrave; più gestire i pagamenti";
                    break;
                }
            }
            else
                $esito = "L'ordine <b>non ha acquisti</b> da parte dei gasisti, clicca qui per <a href=\"".Configure::read('App.server')."/administrator/index.php?option=com_cake&controller=Orders&action=delete&delivery_id=".$orderResult->delivery_id."&order_id=".$orderResult->id."\"><b>cancellalo</b></a>";
        }
            
        return $esito;
    }
                
    /*
     * quando stampo i dati di un doc visualizzo eventuale messaggio se i dati sono consistenti
     */ 
    private function _msgExportDocs($user, $orderResult, $controller, $action, $debug) {

        $esito = [];
        $controller_action_validates = [['Carts', 'admin_managementCartsOne'],
                                        ['Carts', 'admin_validationCarts'],
                                        ['Carts', 'admin_managementCartsSplit'],
                                        ['AjaxGasCode', 'admin_trasport'],
                                        ['SummaryOrderAggregates', 'admin_managementCartsGroupByUsers'],
                                        ['Docs', 'admin_referentDocsExport'],
                                        ['Docs', 'admin_referentDocsExportHistory'],
                                        ['Docs', 'admin_cassiere_docs_export']];
        
        if(!$this->_ctrlMethodValid($controller_action_validates, $controller, $action))
            return $esito;  
    
        $msg_visible=false;
        $msgIni='';
        $msgEnd='';
        
        if($action=='admin_managementCartsOne' || $action=='admin_managementCartsGroupByUsers'|| $action=='admin_managementCartsSplit' || $action=='admin_validationCarts' || $action=='admin_trasport') { 
            $msgIni = "Elaborazione dell'ordine";
            
            if(!$orderResult->permissionToEditReferente) {
                $msgEnd = '<br />Non si potranno modificare i dati.';
                $msg_visible=true;
            }   
            else {
                $msgEnd = "<br />Si pu&ograve; proseguire con la gestione dell'ordine.";
                $msg_visible=false;
            }
        }
        else
        if($action=='admin_referentDocsExport') { 
            $msgIni = "Esportazione dell'ordine";
        
        /*  if(!$isReferentGeneric)
                $msgEnd = "<br />Non sei referente dell'ordine, non si potr&agrave; esportare i dati.";
            else */
            if(!$orderResult->permissionToEditReferente) {
                $msgEnd = "<br />L'esportazione dell'ordine sar&agrave; parziale";
                $msg_visible=true;
            }   
            else {
                $msgEnd = "<br />Si pu&ograve; proseguire con l'esportazione dell'ordine.";
                $msg_visible=false;
            }
        }
        
        $msg = '';
        if($orderResult->state_code=='OPEN') {
            $msg .= "<br />L'ordine&nbsp;non&nbsp;e&grave;&nbsp;ancora&nbsp;chiuso,&nbsp;";
            
            if($orderResult->dayDiffToDateFine==0) $msg .= 'chiuderà&nbsp;oggi';
            else {
                $msg .= 'chiuderà&nbsp;tra&nbsp;'.(-1 * $orderResult->dayDiffToDateFine).'&nbsp;gg,';
                $msg .= '&nbsp;il&nbsp;'.CakeTime::format($orderResult->data_fine,"%A %e %B %Y");
            }
        }
        else
        if($orderResult->state_code=='RI-OPEN-VALIDATE') {
            $msg .= "<br />L'ordine&nbsp;e&grave;&nbsp;stato riaperto,&nbsp;";
        
            if($orderResult->dayDiffToDateFine==0) $msg .= 'chiuderà&nbsp;oggi';
            else {
                $msg .= 'chiuderà&nbsp;tra&nbsp;'.(-1 * $orderResult->dayDiffToDateFine).'&nbsp;gg,';
                $msg .= '&nbsp;il&nbsp;'.CakeTime::format($orderResult->data_fine_validation,"%A %e %B %Y");
            }
        }
        else        
        if($orderResult->state_code=='WAIT-PROCESSED-TESORIERE')
            $msg .= "<br />".__($orderResult->state_code.'-label');
        else    
        if($orderResult->state_code=='PROCESSED-ON-DELIVERY')
            $msg .= "<br />".__($orderResult->state_code.'-label');
        else 
        if($orderResult->state_code=='PROCESSED-TESORIERE')
            $msg .= "<br />".__($orderResult->state_code.'-label');
        else
        if($orderResult->state_code=='CLOSE' || 
           $orderResult->state_code=='TO-PAYMENT') {
            $msg .= "<br />".__($orderResult->state_code.'-label');
            $msgEnd = '';
        }
        
        $msgFinale = $msgIni.$msg.$msgEnd;
        if($msg_visible) 
            return $msgFinale;
        else
            return $esito;
    }
    
    /*
     * il metodo chiamate puo' essere eseguito solo per alcuni controller/action => qui li ctrl
     */
    private function _ctrlMethodValid($controller_action_validates, $controller, $action) {
        
        if($debug) debug(["controller ".$controller, "action ".$action]);
        if($debug) debug($controller_action_validates);
        
        foreach($controller_action_validates as $controller_action_validate) {
            $controller_acl = $controller_action_validate[0];
            $action_acl = $controller_action_validate[1];

            if(strtolower($controller_acl) == strtolower($controller_acl) && strtolower($action) == strtolower($action_acl)) {
                return true;
            }
        }
        
        return false;
    }
                
    public function afterFind($results, $primary = true) {
        
        foreach ($results as $key => $val) {

            if(!empty($val)) {
                if (isset($val['Order']['data_inizio'])) {
                    $results[$key]['Order']['dayDiffToDateInizio'] = $this->utilsCommons->dayDiffToDate($val['Order']['data_inizio']);
                    if(!empty($val['Order']['data_fine_validation']) && $val['Order']['data_fine_validation']!=Configure::read('DB.field.date.empty')) 
                        $results[$key]['Order']['dayDiffToDateFine']   = $this->utilsCommons->dayDiffToDate($val['Order']['data_fine_validation']);
                    else
                        $results[$key]['Order']['dayDiffToDateFine']   = $this->utilsCommons->dayDiffToDate($val['Order']['data_fine']);
                    
                    $results[$key]['Order']['data_inizio_'] = date('d',strtotime($val['Order']['data_inizio'])).'/'.date('n',strtotime($val['Order']['data_inizio'])).'/'.date('Y',strtotime($val['Order']['data_inizio']));
                    $results[$key]['Order']['data_fine_'] = date('d',strtotime($val['Order']['data_fine'])).'/'.date('n',strtotime($val['Order']['data_fine'])).'/'.date('Y',strtotime($val['Order']['data_fine']));
                    $results[$key]['Order']['data_fine_validation_'] = date('d',strtotime($val['Order']['data_fine_validation'])).'/'.date('n',strtotime($val['Order']['data_fine_validation'])).'/'.date('Y',strtotime($val['Order']['data_fine_validation']));
                    $results[$key]['Order']['tesoriere_data_pay_'] = date('d',strtotime($val['Order']['tesoriere_data_pay'])).'/'.date('n',strtotime($val['Order']['tesoriere_data_pay'])).'/'.date('Y',strtotime($val['Order']['tesoriere_data_pay']));

                    $results[$key]['Order']['trasport_'] = number_format($val['Order']['trasport'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['trasport_e'] = $results[$key]['Order']['trasport_'].' &euro;';             

                    $results[$key]['Order']['cost_more_'] = number_format($val['Order']['cost_more'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['cost_more_e'] = $results[$key]['Order']['cost_more_'].' &euro;';

                    $results[$key]['Order']['cost_less_'] = number_format($val['Order']['cost_less'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['cost_less_e'] = $results[$key]['Order']['cost_less_'].' &euro;';

                    $results[$key]['Order']['tesoriere_importo_pay_'] = number_format($val['Order']['tesoriere_importo_pay'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tesoriere_importo_pay_e'] = $results[$key]['Order']['tesoriere_importo_pay_'].' &euro;';

                    $results[$key]['Order']['tesoriere_fattura_importo_'] = number_format($val['Order']['tesoriere_fattura_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tesoriere_fattura_importo_e'] = $results[$key]['Order']['tesoriere_fattura_importo_'].' &euro;';


                    $results[$key]['Order']['tot_importo_'] = number_format($val['Order']['tot_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tot_importo_e'] = $results[$key]['Order']['tot_importo_'].' &euro;';                   
                }
                else 
                /*
                 * se il find() arriva da $hasAndBelongsToMany
                 */
                if (isset($val['data_inizio'])) {
                    $results[$key]['dayDiffToDateInizio'] = $this->utilsCommons->dayDiffToDate($val['data_inizio']);
                    if(!empty($val['data_fine_validation']) && $val['data_fine_validation']!=Configure::read('DB.field.date.empty'))
                        $results[$key]['dayDiffToDateFine']   = $this->utilsCommons->dayDiffToDate($val['data_fine']);
                    else
                        $results[$key]['dayDiffToDateFine']   = $this->utilsCommons->dayDiffToDate($val['data_fine']);
                        
                    $results[$key]['data_inizio_'] = date('d',strtotime($val['data_inizio'])).'/'.date('n',strtotime($val['data_inizio'])).'/'.date('Y',strtotime($val['data_inizio']));
                    $results[$key]['data_fine_'] = date('d',strtotime($val['data_fine'])).'/'.date('n',strtotime($val['data_fine'])).'/'.date('Y',strtotime($val['data_fine']));
                    $results[$key]['data_fine_validation_'] = date('d',strtotime($val['data_fine_validation'])).'/'.date('n',strtotime($val['data_fine_validation'])).'/'.date('Y',strtotime($val['data_fine_validation']));
                    $results[$key]['tesoriere_data_pay_'] = date('d',strtotime($val['tesoriere_data_pay'])).'/'.date('n',strtotime($val['tesoriere_data_pay'])).'/'.date('Y',strtotime($val['tesoriere_data_pay']));
                }   
                
                if(isset($val['Order']['trasport'])) {
                    $results[$key]['Order']['trasport_'] = number_format($val['Order']['trasport'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['trasport_e'] = $results[$key]['Order']['trasport_'].' &euro;';
                }
                else 
                if(isset($val['trasport'])) {
                    $results[$key]['trasport_'] = number_format($val['trasport'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['trasport_e'] = $results['Order']['trasport_'].' &euro;';
                }       

                if(isset($val['Order']['cost_more'])) {
                    $results[$key]['Order']['cost_more_'] = number_format($val['Order']['cost_more'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['cost_more_e'] = $results[$key]['Order']['cost_more_'].' &euro;';
                }
                else
                if(isset($val['cost_more'])) {
                    $results[$key]['cost_more_'] = number_format($val['cost_more'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['cost_more_e'] = $results['Order']['cost_more_'].' &euro;';
                }

                if(isset($val['Order']['cost_less'])) {
                    $results[$key]['Order']['cost_less_'] = number_format($val['Order']['cost_less'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['cost_less_e'] = $results[$key]['Order']['cost_less_'].' &euro;';
                }
                else
                if(isset($val['cost_less'])) {
                    $results[$key]['cost_less_'] = number_format($val['cost_less'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['cost_less_e'] = $results['Order']['cost_less_'].' &euro;';
                }
                
                if(isset($val['Order']['tesoriere_importo_pay'])) {
                    $results[$key]['Order']['tesoriere_importo_pay_'] = number_format($val['Order']['tesoriere_importo_pay'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tesoriere_importo_pay_e'] = $results[$key]['Order']['tesoriere_importo_pay_'].' &euro;';
                }
                else
                if(isset($val['tesoriere_importo_pay'])) {
                    $results[$key]['tesoriere_importo_pay_'] = number_format($val['tesoriere_importo_pay'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['tesoriere_importo_pay_e'] = $results['Order']['tesoriere_importo_pay_'].' &euro;';
                }
                
                if(isset($val['Order']['tesoriere_fattura_importo'])) {
                    $results[$key]['Order']['tesoriere_fattura_importo_'] = number_format($val['Order']['tesoriere_fattura_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tesoriere_fattura_importo_e'] = $results[$key]['Order']['tesoriere_fattura_importo_'].' &euro;';
                }
                else
                if(isset($val['tesoriere_fattura_importo'])) {
                    $results[$key]['tesoriere_fattura_importo_'] = number_format($val['tesoriere_fattura_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['tesoriere_fattura_importo_e'] = $results['Order']['tesoriere_fattura_importo_'].' &euro;';
                }
                
                if(isset($val['Order']['tot_importo'])) {
                    $results[$key]['Order']['tot_importo_'] = number_format($val['Order']['tot_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['Order']['tot_importo_e'] = $results[$key]['Order']['tot_importo_'].' &euro;';
                }
                else
                if(isset($val['tot_importo'])) {
                    $results[$key]['tot_importo_'] = number_format($val['tot_importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $results[$key]['tot_importo_e'] = $results['Order']['tot_importo_'].' &euro;';
                }
            }               
        }
        
        return $results;
    }       
}