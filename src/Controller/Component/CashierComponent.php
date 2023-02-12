<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CashierComponent extends Component {

	private $_where = [];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request

        $this->_where['Deliveries'] = ['Deliveries.stato_elaborazione' => 'OPEN',
            							'Deliveries.sys' => 'N'];
        $this->_where['Orders'] = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];        
    }

	public function getListDeliveries($user, $debug=false) {

        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getsList($user, $user->organization->id, $this->_where);

		if($debug) debug($results);
		
		return $results;
	}

	public function getDeliveries($user, $debug=false) {

        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->withOrdersGets($user, $user->organization->id, $this->_where);
		
		if($debug) debug($results);
		
		return $results;
	}
}