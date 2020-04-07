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

    /* 
     * estrae solo gli users che hanno effettuato acquisti in base alla consegna
     */
	public function getUsersByDelivery($user, $delivery_id, $options=[], $debug=false) {

		$results = [];

		/*
		 * estraggo ordini
		 */ 
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $user->organization->id,
   				  'Orders.delivery_id' => $delivery_id];
        if(isset($options['where'])) {
            if(isset($options['where']['Orders.state_code']))
                    $where += ['Orders.state_code' => $options['where']['Orders.state_code']];
        }
            
		if($debug) debug($where);

        $orderResults = $ordersTable->find()
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();
        $order_ids = [];
        if(!empty($orderResults)) {
            foreach($orderResults as $orderResult) {
                $order_ids[] = $orderResult->id;
            }
			if($debug) debug($order_ids);

			/*
			 * estraggo acquisti
			 */ 
			$results = $this->getUsersByOrders($user, $order_ids, $options, $debug);        

        } // end if(!empty($orderResults)) 

		if($debug) debug($results);
		
		return $results;
	}

    /* 
     * estrae solo gli users che hanno effettuato acquisti in base agli ordini
     */
	public function getUsersByOrders($user, $order_ids, $options=[], $debug=false) {

        $results = [];

        $cartsTable = TableRegistry::get('Carts');

        $where = ['Carts.organization_id' => $user->organization->id,
   				  'Carts.order_id IN ' => $order_ids,
   				  'Users.organization_id' => $user->organization->id];

        $fields = ['Users.organization_id', 'Users.id', 'Users.name', 'Users.username', 'Users.email'];

        $cartResults = $cartsTable->find()
        						->contain(['Users'])
                                ->select($fields)
                                ->where($where)
                                ->order(['Users.name'])
                                ->group($fields)
                                ->all();
		
		if($debug) debug($cartResults);
		
        /*
         * il recordset e' object(App\Model\Entity\Cart) 
         *    'user' => object(App\Model\Entity\User) => js user.user.name!! 
         *   => lo normalizzo
         */
        if(!empty($cartResults)) {
            foreach ($cartResults as $numResults => $cartResult) {
                $results[] = $cartResult['user'];
            }
        }

		return $results;
	}
}