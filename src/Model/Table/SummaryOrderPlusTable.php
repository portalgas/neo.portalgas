<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class SummaryOrderPlusTable extends Table
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
            'foreignKey' => ['organization_id', 'delivery_id'],
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER',
        ]);
    }

	public function mySave($user, $model, $request, $debug=false) {
		
		if($debug) debug($request);
		
		switch ($model) {
			case 'SummaryOrderTrasport':
				App::import('Model', 'SummaryOrderTrasport');
				$Model = new SummaryOrderTrasport;
				$modelTable = Configure::read('DB.prefix')."summary_order_trasports";
				
				$prefix_db = 'trasport';	
				$prefix_order = 'trasport';	
				$prefix = 'trasport_importo';	// e' il valore calcolare a runtime
				
				$msg_insert = __('Insert Trasport');
				$msg_delete = __('Delete Trasport');
				$msg_saved = __('Trasport has been saved');
			break;
			case 'SummaryOrderCostMore':
				App::import('Model', 'SummaryOrderCostMore');
				$modelTable = Configure::read('DB.prefix')."summary_order_cost_mores";
				$Model = new SummaryOrderCostMore;
				
				$prefix_db = 'cost_more';	
				$prefix_order = 'cost_more';
				$prefix = 'cost_more_importo';	// e' il valore calcolare a runtime
				
				$msg_insert = __('Insert CostMore');
				$msg_delete = __('Delete CostMore');
				$msg_saved = __('CostMore has been saved');
			break;
			case 'SummaryOrderCostLess':
				App::import('Model', 'SummaryOrderCostLess');
				$modelTable = Configure::read('DB.prefix')."summary_order_cost_lesses";
				$Model = new SummaryOrderCostLess;
				
				$prefix_db = 'cost_less';
				$prefix_order = 'cost_less';	
				$prefix = 'cost_less_importo';	// e' il valore calcolare a runtime
				
				$msg_insert = __('Insert CostLess');
				$msg_delete = __('Delete CostLess');
				$msg_saved = __('CostLess has been saved');				
			break;
			default:
				die("AjaxGasCode::getData model [$model] non valido");
			break;			
		}
		
		$order_id = $request['data']['order_id'];
		$importo_order = $request['data'][$prefix_order];
		$options = $request['data']['summay-order-plus-options'];
		$actionSubmit = $request['data'][$model]['actionSubmit'];
							
		 /*
		  *  actionSubmit = submitImportoInsert   inserisce importo del ...
		  *  actionSubmit = submitImportoUpdate   aggiorna importo del ...
		  *  actionSubmit = submitImportoDelete   elimina importo del ...
		  *  actionSubmit = submitElabora		  salva per ogni utente la % di ... 
		 */
		try {
		
			switch ($actionSubmit) {
				case 'submitImportoInsert':	/* inserisce importo del trasporto */
					
					/*
					 * ripulisco SummaryOrderTrasport anche se gia' vuoto
					 */
					$Model->delete_to_order($user, $order_id, $debug);

					/*
					 * aggiorno SummaryOrder....
					 * 		importo_... = 0 (dettaglio per ogni utente)
					*/
					$Model->populate_to_order($user, $order_id, $debug);
					
					/*
					 * aggiorno Order
					*/	   					
					$sql ="UPDATE ".Configure::read('DB.prefix')."orders
						   SET
								".$prefix_db." = ".$this->importoToDatabase($importo_order).",
								".$prefix_db."_type = null,
								modified = '".date('Y-m-d H:i:s')."'
						  WHERE
								organization_id = ".(int)$user->organization['Organization']['id']." and id = ".$order_id;
					if($debug) debug($sql);
					$this->query($sql);
					
					return $msg_insert;	   					
				break;
				case 'submitImportoUpdate': /* aggiorna importo del ... */
							
					$Model->delete_to_order($user, $order_id, $debug);
					$Model->populate_to_order($user, $order_id, $debug);
						
					/*
					 * aggiorno Order
					*/
					$sql ="UPDATE `".Configure::read('DB.prefix')."orders`
						   SET
								".$prefix_db." = ".$this->importoToDatabase($importo_order).",
								".$prefix_db."_type = null,
								modified = '".date('Y-m-d H:i:s')."'
						  WHERE
								organization_id = ".(int)$user->organization['Organization']['id']." and id = ".$order_id;
					if($debug) debug($sql);
					$this->query($sql);
														
					return $msg_insert;
				break;
				case 'submitImportoDelete': /* elimina importo del .... */
					/*
					 * ripulisco SummaryOrderTrasport, .... anche se gia' vuoto
					*/
					$Model->delete_to_order($user, $order_id, $debug);
					
					/*
					 * ripulisco Order
					*/
					$Model->delete_importo_to_order($user, $order_id, $debug);
														
					return $msg_delete;
				break;
				case 'submitElabora': /* salva per ogni utente la % di trasporto... */
					
					/*
					 * popolo SummaryOrder...
					 * 		ho SummaryOrder....importo_trasport = 0 (dettaglio di ogni utente) => dopo lo popolo con i campi del form 
					*/
					$modelResults = $Model->select_to_order($user, $order_id, $debug);
					if(empty($modelResults))
						$Model->populate_to_order($user, $order_id, $debug);
					
					/*
					 * aggiorno Order
					 */
					$options_type_db = null;
					switch ($options) {
						case "options-qta":
							$options_type_db = 'QTA';
							break;
						case "options-weight":
							$options_type_db = 'WEIGHT';
							break;
						case "options-users":
							$options_type_db = 'USERS';
							break;
						deafult:
							die('SummaryOrderPlu valore options_type_db ['.$options_type_db.' inatteso!');
						break;
					}
					
					$sql ="UPDATE ".Configure::read('DB.prefix')."orders SET
								".$prefix_db." = ".$this->importoToDatabase($importo_order).",
								".$prefix_db."_type = '$options_type_db',
								modified = '".date('Y-m-d H:i:s')."'
							WHERE
								organization_id = ".(int)$user->organization['Organization']['id']." and id = ".$order_id;
					if($debug) debug($sql);
					$this->query($sql);
					
					if(isset($request['data']['Data']))
					foreach($request['data']['Data'] as $key => $value) {
						$user_id = $key;
						$summary_order_plus_importo = $this->importoToDatabase($value);
								
						if($debug) debug(['user_id '.$user_id, 'summary_order_plus_importo '.$summary_order_plus_importo]);
																					
						$sql = "UPDATE ".$modelTable." SET
							importo_".$prefix_db." = '$summary_order_plus_importo',
							modified = '".date('Y-m-d H:i:s')."'
						WHERE
							organization_id = ".(int)$user->organization['Organization']['id']."
							and order_id = ".(int)$order_id." 
							and user_id = ".(int)$user_id;
						if($debug) debug($sql);
						$result = $Model->query($sql);
					}	
					
					return $msg_saved;
				break;
					
			} // end swicth
			
		}catch (Exception $e) {
			CakeLog::write('error',$sql);
			CakeLog::write('error',$e);
			if($debug) debug($e);
		}		
	}
	
	
    /*
     * aggiunge ad un ordine le eventuali 
     *  SummaryOrder
     *  SummaryOrderAggregate 
     *  SummaryOrderTrasport spese di trasporto
     *  SummaryOrderMore spese generiche
     *  SummaryOrderLess sconti
     *
     *  call 
     *      ExportDocs::userCart
     *      Delivery::tabsAjaxUserCartDeliveries 
     *
     */
    public function addSummaryOrder($user, $order, $user_id, $debug=false) {
        
        $order_id = $order->id;	
        $organization_id = $order->organization_id;		

        /*
        * dati dell'ordine
        */
        $hasTrasport = $order->hasTrasport; /* trasporto */
        $trasport = $order->trasport;
        $hasCostMore = $order->hasCostMore; /* spesa aggiuntiva */
        $cost_more = $order->cost_more;
        $hasCostLess = $order->hasCostLess;  /* sconto */
        $cost_less = $order->cost_less;
        $typeGest = $order->typeGest;   /* AGGREGATE / SPLIT */

        $resultsSummaryOrder = [];
        $resultsSummaryOrderAggregate = [];
        $resultsSummaryOrderTrasport = [];
        $resultsSummaryOrderCostMore = [];
        $resultsSummaryOrderCostLess = [];

		$summaryOrdersTable = TableRegistry::get('SummaryOrders');

		$resultsSummaryOrder = $summaryOrdersTable->getByUserByOrder($user, $organization_id, $user_id, $order_id, $options=[], $debug);
			
        if($hasTrasport=='Y') {
        	$summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');

            $resultsSummaryOrderTrasport = $summaryOrderTrasportsTable->getByUserByOrder($user, $organization_id, $user_id, $order_id, $options=[], $debug);
        }
        if($hasCostMore=='Y') {
            $summaryOrderCostMoresTable = TableRegistry::get('SummaryOrderCostMores');

            $resultsSummaryOrderCostMore = $summaryOrderCostMoresTable->getByUserByOrder($user, $organization_id, $user_id, $order_id, $options=[], $debug);
        }
        if($hasCostLess=='Y') {
            $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');

            $resultsSummaryOrderCostLess = $summaryOrderCostLessesTable->getByUserByOrder($user, $organization_id, $user_id, $order_id, $options=[], $debug);
        }

        $summaryOrderAggregatesTable = TableRegistry::get('SummaryOrderAggregates');

        $resultsSummaryOrderAggregate = $summaryOrderAggregatesTable->getByUserByOrder($user, $organization_id, $user_id, $order_id, $options=[], $debug); // se l'ordine e' ancora aperto e' vuoto

        $results = new \stdClass();
        $results->summary_order           = $resultsSummaryOrder;
        $results->summary_order_aggregate = $resultsSummaryOrderAggregate;
        $results->summary_order_trasport  = $resultsSummaryOrderTrasport;
        $results->summary_order_cost_more = $resultsSummaryOrderCostMore;
        $results->summary_order_cost_less = $resultsSummaryOrderCostLess;

		if($debug) debug($results);

        return $results;
    }	
}