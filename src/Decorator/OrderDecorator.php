<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class OrderDecorator  extends AppDecorator {
	
	public $serializableAttributes = array('id', 'name');
	public $results; 

    public function __construct($user, $orders)
    {
    	$results = [];
	    // debug($orders);

	    if($orders instanceof \Cake\ORM\ResultSet) {
			foreach($orders as $numResult => $order) {
				$order = $this->_decorate($user, $order);
				if(!empty($order)) // per i GasGroup posso scartarli 	
					$results[$numResult] = $order;
			}
	    }
	    else 
	    if($orders instanceof \App\Model\Entity\Order) {
			$order = $this->_decorate($user, $orders);
			if(!empty($order)) // per i GasGroup posso scartarli 	
				$results = $order;
	    }

		$this->results = $results;
    }

	private function _decorate($user, $order) {

		$debug = false;

		/* 
		* ctrl se e' un ordine di gruppo e se appartiene al gruppo del referente 
		*/   
		if($user->organization->paramsConfig['hasGasGroups']=='Y') {
			if(!empty($order->gas_group_id)) {
				$gasGroupsTable = TableRegistry::get('GasGroups');
				$gas_groups = $gasGroupsTable->getsByIdsUser($user, $user->organization->id, $user->id);
				if(!in_array($order->gas_group_id, $gas_groups)) {
					$order = null; 
					return $order;
				}
			}
		}

		/* 
		* DES
		*/   
		if($user->organization->paramsConfig['hasDes']=='Y') {		

		}
		
        $results = [];
        $results = $order;
		/*
		* ordine saldato dai gasisti
		* ordine pagato al produttore
		*/
		$lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
		
		// $user->acl['isReferenteTesoriere'] non esiste
		$results->orderStateNext = $lifeCycleOrdersTable->getOrderStateNext($user, $order, false, $debug);

		$results->PaidUsers = $lifeCycleOrdersTable->getPaidUsers($user, $order, $debug);
	
		$results->PaidSupplier = $lifeCycleOrdersTable->getPaidSupplier($user, $order, $debug);
		
		$results->can_state_code_to_close = $lifeCycleOrdersTable->canStateCodeToClose($user, $order, $debug);
		
		$results->msgGgArchiveStatics = $lifeCycleOrdersTable->msgGgArchiveStatics($user, $order, $debug);
		
		/*
		 * recupero richiesta di pagamento 
		 */ 
		$results->request_payment_num = '';
		$results->request_payment_id = '';
		if($user->organization->template->payToDelivery == 'POST' || $user->organization->template->payToDelivery=='ON-POST') {
			$requestPaymentsTable = TableRegistry::get('RequestPayments');
			 $results->request_payment_num = $requestPaymentsTable->getRequestPaymentNumByOrderId($user, $order->id);
			 $results->request_payment_id = $requestPaymentsTable->getRequestPaymentIdByOrderId($user, $order->id);
		}
                    
        return $results;
    }

	function name() {
		return $this->results;
	}
}