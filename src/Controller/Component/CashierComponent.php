<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CashierComponent extends Component {

	private $_where_delivery = ['Deliveries.stato_elaborazione' => 'OPEN',
            					'Deliveries.sys' => 'N'];
    private $_where_order = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	public function getListDeliveries($user, $debug=false) {

        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getsList($user, $user->organization->id, $this->_where_delivery, $this->_where_order);

		if($debug) debug($results);
		
		return $results;
	}

	public function getDeliveries($user, $debug=false) {

        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->gets($user, $user->organization->id, $this->_where_delivery, $this->_where_order);
		
		if($debug) debug($results);
		
		return $results;
	}
}