<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CartComponent extends Component {

	private $_where_delivery = ['Deliveries.stato_elaborazione' => 'OPEN',
            					'Deliveries.sys' => 'N'];
    private $_where_order = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	public function getUsersByDelivery($user, $delivery_id, $options=[], $debug=false) {

		$results = [];

		/*
		 * estraggo ordini
		 */ 
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $user->organization->id,
   				  'Orders.delivery_id' => $delivery_id];
        if(isset($options['orders_state_code']))
            $where = ['Orders.state_code' => $options['orders_state_code']];

        $orderResults = $ordersTable->find()
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();
        $order_ids = [];
        if(!empty($orderResults)) {
            foreach($orderResults as $orderResult) {
                $order_ids[] = $orderResult->id;
            }
			debug($order_ids);

			/*
			 * estraggo acquisti
			 */ 
			$results = $this->getUsersByOrders($user, $order_ids, $options, $debug);        

        } // end if(!empty($orderResults)) 

		if($debug) debug($results);
		
		return $results;
	}

	public function getUsersByOrders($user, $order_ids, $options=[], $debug=false) {

        $cartsTable = TableRegistry::get('Carts');

        $where = ['Orders.organization_id' => $user->organization->id,
   				  'Orders.id IN ' => $order_ids,
   				  'Users.organization_id' => $user->organization->id];

        $results = $cartsTable->find()
        						->contains(['Users'])
                                ->where($where)
                                ->order(['Users.name'])
                                ->all();
		
		if($debug) debug($results);
		
		return $results;
	}
}