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
     * qty = qty originale
     * qty_new = qty aggiornata
     */
    public function managementCart($user, $organization_id, $article, $debug=false) {

        $results = [];
        $results['esito'] = true;
        $results['code'] = '';
        $results['msg'] = '';
        $results['results'] = '';

        $qty = (int)$article['cart']['qty'];
        $qty_new = (int)$article['cart']['qty_new'];

        $organization_id = $organization_id; // $article['cart']['organization_id'];
        $user_id = $user->id; // $article['cart']['user_id'];
        $order_id = $article['cart']['order_id'];
        $article_organization_id = $article['cart']['article_organization_id'];
        $article_id = $article['cart']['article_id'];

        /*
         * action
         */
        $action = '';
        if($qty_new==0) 
           $action = 'DELETE';
        else 
        if($qty==0) 
           $action = 'INSERT';
        else 
           $action = 'UPDATE';
        
        if($debug) debug('action '.$action);

        /*
         * ctrl validita
         *
         */
        $results = $this->_ctrlValidita($user, $organization_id, $order_id, $article_organization_id, $article_id, $qty_new, $qty, $action);
        if($debug) debug($results);

        if($results['esito']) {

            $cartsTable = TableRegistry::get('Carts');

            switch (strtoupper($action)) {
                case 'DELETE':
                    $where = ['Carts.organization_id' => $organization_id,
                              'Carts.order_id' => $order_id,
                              'Carts.user_id' => $user_id,
                              'Carts.article_organization_id' => $article_organization_id,
                              'Carts.article_id' => $article_id];
                    if($debug) debug($where);

                    $cart = $cartsTable->find()
                                    ->where($where)
                                    ->first(); 
                    if (!$cartsTable->delete($cart)) {
                        if($debug) debug($cart->getErrors());
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
                break;
                case 'INSERT':
                    $data = [];
                    $data['organization_id'] = $organization_id;
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['article_organization_id'] = $article_organization_id;
                    $data['article_id'] = $article_id;
                    $data['qty'] = $qty_new;
                    $data['deleteToReferent'] = 'N';
                    $data['qty_forzato'] = 0;
                    $data['importo_forzato'] = 0;
                    $data['nota'] = '';
                    $data['inStoreroom'] = 'N';
                    $data['stato'] = 'Y';

                    $cart = $cartsTable->newEntity();
                    $cart = $cartsTable->patchEntity($cart, $data);
                    if($debug) debug($cart);
                    if (!$cartsTable->save($cart)) {
                        if($debug) debug($cart->getErrors());
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
                break;
                case 'UPDATE':
                    $cart = $cartsTable->getByIds($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $debug);

                    $data = [];
                    $data['qty'] = $qty_new;

                    $cart = $cartsTable->patchEntity($cart, $data);
                    if($debug) debug($cart);
                    if (!$cartsTable->save($cart)) {
                        if($debug) debug($cart->getErrors());
                        $results['esito'] = false;
                        $results['code'] = 500;
                        $results['results'] = $cart->getErrors();
                    }
                    else {
                        $results['esito'] = true;
                        $results['code'] = 200;
                        $results['msg'] = __('cart_msg_save_OK');
                        $results['results'] = '';                
                    }
                break;
                default:
                    
                    break;
            } // end switch (strtoupper($action))
        } // end if($results['esito'])

        return $results;
    }

    /*
     * $action = INSERT
     * $action = UPDATE-DELETE
     */
    private function _ctrlValidita($user, $organization_id, $order_id, $article_organization_id, $article_id, $qty_new, $qty, $action, $debug=false) {

        $results = [];
        $esito = true;
        $msg = '';

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $where = ['ArticlesOrders.organization_id' => $organization_id,
                  'ArticlesOrders.order_id' => $order_id,
                  'ArticlesOrders.article_organization_id' => $article_organization_id,
                  'ArticlesOrders.article_id' => $article_id];
        // debug($where);

        $articlesOrders = $articlesOrdersTable->find()
                        ->contain('Carts')
                        ->where($where)
                        ->first(); 
        //debug($articlesOrders);

        if($articlesOrders->stato=='N') {
            $msg = __('cart_msg_stato_N');
            $esito = false;
        }  
            
        if($esito && $articlesOrders->has('carts') && isset($articlesOrders->carts->stato) && $articlesOrders->carts->stato=='N') {
            $msg = __('cart_msg_stato_N');
            $esito = false;
        }  

        if($esito && $action!='INSERT') {
            if($articlesOrders->stato=='qtyMAXORDER' && ($qty_new > $qty)) {
                $msg = sprintf(__('cart_msg_qtamax_order_stop'), $articlesOrders->qty_massima_order);
                $esito = false;
            }
            else
            if($articlesOrders->stato=='LOCK' && ($qty_new > $qty)) {
                $msg = __('cart_msg_block_stop'); 
                $esito = false; 
            }
        }

        if($esito) {

            if($qty_new>0 && ($qty_new < (int)$articlesOrders->qty_minima)) {
                $msg = sprintf(__('cart_msg_qtamin'), $articlesOrders->qty_minima, $qty_new);
                $esito = false;
            }
            else          
            if((int)$articlesOrders->qty_massima > 0) {
                /*
                 * Q T A - M A X
                 */                  
                if($qty_new>0 && ($qty_new > $articlesOrders->qty_massima)) {  // ctrl qty massima riferita all'acquisto del singolo gasista
                    $msg = sprintf(__('cart_msg_qtamax'), $articlesOrders->qty_massima, $qty_new);
                    $esito = false;
                }       
            }
            else    
            /*
             * Q T A - M A X - O R D E R 
             * */
            if((int)$articlesOrders->qty_massima_order > 0) {
                
                if($qty_new > $qty) { // ctrl che l'utente non abbia diminuito la qty

                    // qty_massima_order superata: ricalcolo la qty e articlesOrder.stato = qtyMAXORDER
                    if(((int)$articlesOrders->qty_cart - $qty + $qty_new) > $articlesOrders->qty_massima_order) {
                    
                        $qty_label = ((int)$articlesOrders->qty_massima_order - (int)$articlesOrders->qty_cart + $qty); // la ricalcolo
                    
                        $msg = sprintf(__('cart_msg_qtamax_order'), $articlesOrders->qty_massima_order, $qty_label);
                        $esito = false;
                    }
                    else  // qty massima raggiunta articlesOrder.stato = qtyMAXORDER
                    if(((int)$articlesOrders->qty_cart - (int)$qty + $qty_new) == (int)$articlesOrders->qty_massima_order) {
                        // qty massima raggiunta: articlesOrder.stato = qtyMAXORDER
                    }

                }
            }
        } // end if($esito)
   
        $results['esito'] = $esito;
        $results['msg'] = $msg;

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