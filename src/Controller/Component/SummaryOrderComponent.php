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

        $where = ['SummaryOrders.organization_id' => $organization->id,
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

        $where = ['SummaryOrders.organization_id' => $organization->id,
   				  'SummaryOrders.order_id' => $order_id];
		if($debug) debug($where);

        $results = $summaryOrdersTable->find()
    						->contains(['Users'])
                            ->where($where)
                            ->order(['Users.id'])
                            ->all();

		if($debug) debug($results);
		
		return $results;
	}

	public function getByUser($user, $organization_id, $user_id, $options, $debug=false) {

        $summaryOrdersTable = TableRegistry::get('SummaryOrders');

        $where = ['SummaryOrders.organization_id' => $organization->id,
   				  'SummaryOrders.user_id' => $user_id];
		if($debug) debug($where);

        $results = $summaryOrdersTable->find()
    						->contains(['Users'])
                            ->where($where)
                            ->first();

		if($debug) debug($results);
		
		return $results;
	}
}