<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CartComponent extends CartSuperComponent {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    /*
     * qta = qta originale, ma la ricalcolo nel caso fosse cambiata
     * qta_new = qta aggiornata
     */
    public function managementCart($user, $organization_id, $order, $articles_order, $debug=false) {

        $results = [];
        $results['esito'] = true;
        $results['code'] = '';
        $results['msg'] = '';
        $results['results'] = '';

        if (empty($articles_order) || !isset($articles_order['cart'])) {
            $results['esito'] = false;
            $results['code'] = 500;
            $results['msg'] = 'Login scaduta';
            $results['results'] = 'Login scaduta';
        }

        $organization_id = $organization_id; // $articles_order['cart']['organization_id'];
        $user_id = $user->id; // $articles_order['cart']['user_id'];
        $order_id = $articles_order['cart']['order_id'];
        $article_organization_id = $articles_order['cart']['article_organization_id'];
        $article_id = $articles_order['cart']['article_id'];

        if(Configure::read('Logs.cart')) Log::write('debug', 'user '.$user->id);
        if(Configure::read('Logs.cart')) Log::write('debug', $articles_order);

        /*
         * qta = qta originale, ma la ricalcolo nel caso fosse cambiata
         * qta_new = qta aggiornata
         */
        $cartsTable = TableRegistry::get('Carts');

        $qta = $cartsTable->getQtaCartByArticle($user, $organization_id, $order_id, $article_organization_id, $article_id, $debug);
        if(Configure::read('Logs.cart')) Log::write('debug', 'Carts.qta totale acquisti '.$qta);
        $qta_new = (int)$articles_order['cart']['qta_new'];
        if(Configure::read('Logs.cart')) Log::write('debug', 'Acquisto corrente '.$qta_new);

        /*
         * action
         */
        $action = '';
        if($qta_new==0) 
           $action = 'DELETE';
        else 
        if(empty($articles_order['cart']['user_id']))
           $action = 'INSERT';
        else 
           $action = 'UPDATE';
        
        if($debug) debug('action '.$action);
        if(Configure::read('Logs.cart')) Log::write('debug', 'Action '.$action);

        /*
         * ctrl validita
         * RI-OPEN-VALIDATE
         */
        if(isset($article['riopen'])) {
            $results = $this->_ctrlValiditaRiOpen($user, $articles_order, $qta, $debug);
            if($debug) debug($results);
        }

        /*
         * ctrl validita
         */
        if($results['esito']) {
            $results = $this->_ctrlValidita($user, $articles_order, $qta_new, $qta, $action);
            if($debug) debug($results);
        }

        if($results['esito']) {
            
            switch (strtoupper($action)) {
                case 'DELETE':
                    $where = ['Carts.organization_id' => $organization_id,
                              'Carts.order_id' => $order_id,
                              'Carts.user_id' => $user_id,
                              'Carts.article_organization_id' => $article_organization_id,
                              'Carts.article_id' => $article_id];
                    $cart = $cartsTable->find()
                                    ->where($where)
                                    ->first(); 

                    if(Configure::read('Logs.cart')) Log::write('debug', 'DELETE CART DATA:');
                    if(Configure::read('Logs.cart')) Log::write('debug', $cart);

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

                    $cashesUsersTable = TableRegistry::get('CashesUsers');

                    /*
                     * lo prendo dall'ordine perche' il listino puo' gestirlo un altro
                     * $supplier_organization_id = $results['Article']['supplier_organization_id'];
                     */
                    $supplier_organization_id = $order['supplier_organization_id'];

                    if($organization_id == Configure::read('social_market_organization_id'))
                        $esito_ctrl_limit_cart = true;
                    else
                        $esito_ctrl_limit_cart = $cashesUsersTable->ctrlLimitCart($user, $organization_id, $supplier_organization_id, $qta, $qta_new, $articles_order['price'], $debug);

                    if($esito_ctrl_limit_cart) {

                        $data = [];
                        $data['organization_id'] = $organization_id;
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['article_organization_id'] = $article_organization_id;
                        $data['article_id'] = $article_id;
                        $data['qta'] = $qta_new;
                        $data['deleteToReferent'] = 'N';
                        $data['qta_forzato'] = 0;
                        $data['importo_forzato'] = 0;
                        $data['nota'] = '';
                        $data['inStoreroom'] = 'N';
                        $data['stato'] = 'Y';

                        $cart = $cartsTable->newEntity();
                        $cart = $cartsTable->patchEntity($cart, $data);
                        if($debug) debug($cart);
                        if(Configure::read('Logs.cart')) Log::write('debug', 'INSERT CART DATA:');
                        if(Configure::read('Logs.cart')) Log::write('debug', $cart);

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

                    }
                    else {
                        $results['esito'] = false;
                        $results['code'] = 200;
                        $results['msg'] = __('cart_msg_limit_cash');  
                        $results['results'] = '';
                    }
                break;
                case 'UPDATE':

                    /* 
                     * ctrl Cassa 
                     * solo se aumento la qta
                     */
                    $esito_ctrl_limit_cart = true; 
                    if($qta_new > $qta) {

                        $cashesUsersTable = TableRegistry::get('CashesUsers');

                        /*
                         * lo prendo dall'ordine perche' il listino puo' gestirlo un altro
                         * $supplier_organization_id = $results['Article']['supplier_organization_id'];
                         */
                        $supplier_organization_id = $order['supplier_organization_id'];
                        if($organization_id == Configure::read('social_market_organization_id'))
                            $esito_ctrl_limit_cart = true;
                        else
                            $esito_ctrl_limit_cart = $cashesUsersTable->ctrlLimitCart($user, $organization_id, $supplier_organization_id, $qta, $qta_new, $articles_order['price'], $debug);
                    }

                    if($esito_ctrl_limit_cart) {
              
                        $where = ['Carts.organization_id' => $organization_id,
                                  'Carts.order_id' => $order_id,
                                  'Carts.user_id' => $user_id,
                                  'Carts.article_organization_id' => $article_organization_id,
                                  'Carts.article_id' => $article_id];
                        $cart = $cartsTable->find()
                                       ->where($where)
                                       ->first();

                        $data = [];
                        $data['qta'] = $qta_new;

                        $cart = $cartsTable->patchEntity($cart, $data);
                        if($debug) debug($cart);
                        if(Configure::read('Logs.cart')) Log::write('debug', 'UPDATE CART DATA:');
                        if(Configure::read('Logs.cart')) Log::write('debug', $cart);

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
                    }
                    else {
                        $results['esito'] = false;
                        $results['code'] = 200;
                        $results['msg'] = __('cart_msg_limit_cash');  
                        $results['results'] = '';
                    }                         
                break;
                default:
                    
                break;
            } // end switch (strtoupper($action))

        } // end if($results['esito'])

        // debug($results);
        if(Configure::read('Logs.cart')) Log::write('debug', $results);
        
        if($results['esito']) {

            $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
            $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $order);
            
            // debug($articlesOrdersTable);
            if(Configure::read('Logs.cart')) Log::write('debug', 'FACTORY articlesOrdersTable->alias '.$articlesOrdersTable->getAlias());
            
            if($articlesOrdersTable!==false) 
                $updateResults = $articlesOrdersTable->aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $articles_order, $debug);
        }
        
        return $results;
    }

    /*
     * RI-OPEN-VALIDATE
     * concorrenza tra users, ctrl che non sia gia' completato il collo
     */
    private function _ctrlValiditaRiOpen($user, $articles_order, $qta, $debug=false) {

        $results = [];
        $esito = true;
        $msg = '';

        $pezzi_confezione = (int)$articles_order['package']; // pezzi_confezione
        if($qta >= $pezzi_confezione) {
            $delta = ($qta % $pezzi_confezione); 
            
            if($delta==0) {
                $msg = __('cart_msg_riopen_package_close');
                $esito = false;
            }
        }
        if(Configure::read('Logs.cart')) Log::write('debug', '_ctrlValiditaRiOpen pezzi_confezione '.$pezzi_confezione);

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