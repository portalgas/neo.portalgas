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

    public function managementCart($user, $organization_id, $article, $debug=false) {

        $results = [];
        $results['esito'] = true;
        $results['code'] = '';
        $results['msg'] = '';
        $results['results'] = '';

        $qty = $article['cart']['qty'];
        $qty_new = $article['cart']['qty_new'];

        $organization_id = $organization_id; // $article['cart']['organization_id'];
        $user_id = $user->id; // $article['cart']['user_id'];
        $order_id = $article['cart']['order_id'];
        $article_organization_id = $article['cart']['article_organization_id'];
        $article_id = $article['cart']['article_id'];

        $cartsTable = TableRegistry::get('Carts');

        if($qty_new==0) {
            /*
             * DELETE
             */
            $where = ['Carts.organization_id' => $organization_id,
                      'Carts.order_id' => $order_id,
                      'Carts.user_id' => $user_id,
                      'Carts.article_organization_id' => $article_organization_id,
                      'Carts.article_id' => $article_id];
            // debug($where);

            $cart = $cartsTable->find()
                            ->where($where)
                            ->first(); 
            if (!$cartsTable->delete($cart)) {
                $results['esito'] = false;
                $results['code'] = 500;
                $results['results'] = $cart->getErrors();
            }
            else {
                $results['esito'] = true;
                $results['code'] = 200;
                $results['msg'] = 'Cancellazione avvenuta con successo';  
                $results['results'] = '';                
            }   
        }
        else
        if($qty==0) {
            /*
             * INSERT
             */ 
            $data = [];
            $data['organization_id'] = $organization_id;
            $data['user_id'] = $user_id;
            $data['order_id'] = $order_id;
            $data['article_organization_id'] = $article_organization_id;
            $data['article_id'] = $article_id;
            $data['qta'] = $qty_new;
            $data['deleteToReferent'] = 'N';
            $data['qta_forzato'] = 0;
            $data['importo_forzato'] = 0;
            $data['nota'] = '';
            $data['inStoreroom'] = 'N';
            $data['stato'] = 'Y';

            $cart = $cartsTable->newEntity();
            $cart = $cartsTable->patchEntity($cart, $data);
            // debug($cart);
            if (!$cartsTable->save($cart)) {
                $results['esito'] = false;
                $results['code'] = 500;
                $results['results'] = $cart->getErrors();
            }
            else {
                $results['esito'] = true;
                $results['code'] = 200;
                $results['msg'] = 'Inserimento avvenuto con successo';  
                $results['results'] = '';
            }
        }
        else {
            /*
             * UPDATE
             */   
            $cart = $cartsTable->getByIds($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $debug);

            $data = [];
            $data['qta'] = $qty_new;

            $cart = $cartsTable->patchEntity($cart, $data);
            if (!$cartsTable->save($cart)) {
                $results['esito'] = false;
                $results['code'] = 500;
                $results['results'] = $cart->getErrors();
            }
            else {
                $results['esito'] = true;
                $results['code'] = 200;
                $results['msg'] = 'Aggiornamento avvenuto con successo';  
                $results['results'] = '';                
            }                     
        }

        return $results;
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
        if(isset($options['where'])) 
        foreach ($options['where'] as $key => $value) {
            $where += [$key => $value];
        }
            
		if($debug) debug($where);

        $orderResults = $ordersTable->find()
                                ->where($where)
                                ->order(['Orders.data_inizio'])
                                ->all();
        $order_ids = [];
        if(!empty($orderResults) && $orderResults->count()>0) {
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