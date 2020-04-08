<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class SummaryOrderComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	public function getTotImportoByOrder($user, $organization_id, $order_id, $options, $debug=false) {

        $summaryOrdersTable = TableRegistry::get('SummaryOrders');

        $where = ['SummaryOrders.organization_id' => $organization_id,
   				  'SummaryOrders.order_id' => $order_id];
		if($debug) debug($where);

        $results = $summaryOrdersTable->find()
				            ->select(['sum' => $query->func()->sum('SummaryOrders.importo')])
    				        ->where($where)
                            ->first();

		if($debug) debug($results);
		
		return $results;
	}

	public function getByOrder($user, $organization_id, $order_id, $options, $debug=false) {

        $summaryOrdersTable = TableRegistry::get('SummaryOrders');

        $where = ['SummaryOrders.organization_id' => $organization_id,
   				  'SummaryOrders.order_id' => $order_id];
		if($debug) debug($where);

        $results = $summaryOrdersTable->find()
    						->contain(['Users'])
                            ->where($where)
                            ->order(['Users.id'])
                            ->all();

		if($debug) debug($results);
		
		return $results;
	}

    public function getByUserByOrder($user, $organization_id, $user_id, $order_ids, $options, $debug=false) {

        $summaryOrdersTable = TableRegistry::get('SummaryOrders');

        $where = ['SummaryOrders.organization_id' => $organization_id,
                  'SummaryOrders.user_id' => $user_id,
                  'SummaryOrders.order_id IN ' => $order_ids];
        if($debug) debug($where);

        $results = $summaryOrdersTable->find()
                            ->contain(['Users'])
                            ->where($where)
                            ->all();

        if($debug) debug($results);
        
        return $results;
    }

    /* 
     * estraggi i dettaglio di pagamenti di un ordine (SummaryOrders) dello user
     */
    public function getByUserByDelivery($user, $organization_id, $user_id, $delivery_id, $options, $debug=false) {
ini_set("precision", 14);
ini_set("serialize_precision", 14);
        $summaryOrdersTable = TableRegistry::get('SummaryOrders');

        $where = ['SummaryOrders.organization_id' => $organization_id,
                  'SummaryOrders.user_id' => $user_id,
                  'SummaryOrders.delivery_id' => $delivery_id];
        if($debug) debug($where);

        $results = $summaryOrdersTable->find()
                            ->contain(['Users', 'Orders' => ['SuppliersOrganizations']])
                            ->where($where)
                            ->all();

        if($debug) debug($results);
        
        return $results;
    }

    /* 
     * dato i dettaglio di pagamenti di un ordine (SummaryOrders) dello user
     * crea in SummaryDelivery il totale dovuto (somma SummaryOrders.importo) 
     */
    public function getSummaryDeliveryByUser($user, $organization_id, $user_id, $delivery_id, $summary_orders, $debug=false) {

        $results = [];

        if(!empty($summary_orders)) {
            
            $tot_importo = 0;
            $tot_importo_pagato = 0;
            foreach ($summary_orders as $summary_order) {
                $tot_importo = ($tot_importo + $summary_order->importo);
                $tot_importo_pagato = ($tot_importo_pagato + $summary_order->importo_pagato);
            } // foreach ($summary_orders as $summary_order)

            $results['organization_id'] = $organization_id;
            $results['user_id'] = $user_id;
            $results['delivery_id'] = $delivery_id;
            $results['tot_importo'] = $tot_importo;
            $results['tot_importo_pagato'] = $tot_importo_pagato;
            // debug($results);
        }

        if($debug) debug($results);
        
        return $results;
    }    
}