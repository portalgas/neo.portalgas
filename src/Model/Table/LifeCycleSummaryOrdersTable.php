<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class LifeCycleSummaryOrdersTable extends Table
{
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
            'foreignKey' => 'delivery_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER',
        ]);
    }

	/*
	 * ctrl che tutti i gasiti abbiano saldato a cassiere / tesoriere => Order.stato successivo in base al template
	 * tutti i SummaryOrders di un ordine hanno saldato_a is NOT null
	 */
	public function isSummaryOrderAllSaldato($user, $orderResult, $debug=false) {
	
		// $debug=false; // function richiamata da Cron che ha il debug a true
	
		$esito = [];

		if(empty($orderResult)) {
			$esito['CODE'] = "500";
			$esito['MSG'] = "Parametri errati";
			return $esito; 
		}	
				
		$ordersTable = TableRegistry::get('Orders');
				
		if(!is_object($orderResult))
			$orderResult = $ordersTable->getById($user, $user->organization->id, $orderResult, $debug);
		
		if($debug) debug("LifeCycleSummaryOrdersTable::isSummaryOrderAllSaldato order_id ".$orderResult->id);

		/*
		 * prima ctrl che sia popolata
		 */		 
		$summaryOrdersTable = TableRegistry::get('SummaryOrders');
		$summaryOrderResults = $summaryOrdersTable->getByOrder($user, $orderResult->organization_id, $orderResult->id);
		if($debug) debug('SummaryOrder->getByOrder count '.$summaryOrderResults->count());
		if(empty($summaryOrderResults) || $summaryOrderResults->count()==0) {
			if($debug) debug("LifeCycleSummaryOrdersTable::isSummaryOrderAllSaldato order_id ".$orderResult->id." SummaryOrder non popolato!");			
			
			return false;
		}
		
        /*
         * ctrl se ci sono gasisti che non hanno saldato
         */
        $summaryOrdersTable = TableRegistry::get('SummaryOrders');
        
        $where = [];
        $where = ['SummaryOrders.organization_id' => $user->organization->id,
				  'SummaryOrders.order_id' => $orderResult->id];
		$where += $this->getConditionIsNotSaldato($user);
        $summaryOrderResults = $summaryOrdersTable->find()
        										->where($where)
        										->all();
		if($debug) debug($where);
        // if($debug) debug(["LifeCycleSummaryOrdersTable::isSummaryOrderAllSaldato order_id ".$orderResult->id, $options]);
		if(empty($summaryOrderResults) || $summaryOrderResults->count()==0) {
			if($debug) debug("LifeCycleSummaryOrdersTable::isSummaryOrderAllSaldato order_id ".$orderResult->id." tutti (".$summaryOrderResults->count().") hanno saldato");				
			return true;
		}
		else {
			if($debug) debug("LifeCycleSummaryOrdersTable::isSummaryOrderAllSaldato order_id ".$orderResult->id." NON tutti hanno saldato");				
			
			return false;
		}
	}
		
	/* 
	 * ctrl se puo' aggiungere ad un ordine le eventuali 
	 *  SummaryOrder 
	 *  SummaryOrderTrapsort spese di trasporto
	 *  SummaryOrderMore spese generiche
	 *  SummaryOrderLess sconti
	 */		
	public function canAddSummaryOrder($user, $order_state_code) {
		
		if($order_state_code == 'PROCESSED-POST-DELIVERY' ||  //  In carico al referente dopo la consegna
			$order_state_code == 'PROCESSED-ON-DELIVERY' ||  //  in carico al cassiere
			$order_state_code == 'INCOMING-ORDER' ||  // In carico al referente con la merce arrivata
			$order_state_code == 'WAIT-PROCESSED-TESORIERE' || 
			$order_state_code == 'PROCESSED-TESORIERE' || 
			$order_state_code == 'TO-PAYMENT' || 
			$order_state_code == 'TO-REQUEST-PAYMENT' || 
			$order_state_code == 'USER-PAID' || 
			$order_state_code == 'SUPPLIER-PAID' || 
			$order_state_code == 'WAIT-REQUEST-PAYMENT-CLOSE' || 
			$order_state_code == 'CLOSE') 
			return true;
		else
			return false;
	}
	
	private function _getSaldatoA($orderResult) {
		
		$saldato_a = null;
		
		switch($orderResult->state_code) {
			case 'CREATE-INCOMPLETE':
			case 'OPEN-NEXT':
			case 'OPEN':
			case 'RI-OPEN-VALIDATE':
			case 'PROCESSED-BEFORE-DELIVERY':
			case 'PROCESSED-POST-DELIVERY':
			case 'INCOMING-ORDER':  // merce arrivata
			break;
			case 'PROCESSED-ON-DELIVERY':  // in carico al Cassiere
				$saldato_a = 'CASSIERE';	
			break;
			case 'WAIT-PROCESSED-TESORIERE':
			case 'PROCESSED-TESORIERE':	
			case 'TO-REQUEST-PAYMENT':
			case 'TO-PAYMENT':
				$saldato_a = 'TESORIERE';
			break;
			case 'USER-PAID':					
			case 'SUPPLIER-PAID':
			case 'WAIT-REQUEST-PAYMENT-CLOSE':
			case 'CLOSE':
			break;
			default:
				die("LifeCycleSummaryOrdersTable::_getSaldatoA Order.state_code non previsto [".$orderResult->state_code."]");
			break;			
		}
			
		return $saldato_a; 
	}
    
    /*
     * condizione per considerare un SummaryOrder pagato saldato_a != null
     *   e non 'SummaryOrder.importo = SummaryOrder.importo_pagato'
     * saldato_a ENUM('CASSIERE','TESORIERE')
     */
    public function getConditionIsSaldato($user) {
        return ['SummaryOrders.saldato_a is not null'];
    } 

    /*
     * condizione per considerare un SummaryOrder pagato saldato_a = null
     *   e non 'SummaryOrder.importo != SummaryOrder.importo_pagato'
     * saldato_a ENUM('CASSIERE','TESORIERE')
     */
    public function getConditionIsNotSaldato($user) {
        return ['SummaryOrders.saldato_a is null'];
    }     
}