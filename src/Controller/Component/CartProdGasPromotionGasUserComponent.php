<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CartProdGasPromotionGasUserComponent extends CartSuperComponent {

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
     * qta = qta originale, ma la ricalcolo nel caso fosse cambiata
     * qta_new = qta aggiornata
     *
      se order_id = prod_gas_promotion_id per le promozioni GAS-USERS
     */
    public function managementCart($user, $organization_id, $order, $articles_order, $debug=false) {

        $results = [];
        $results['esito'] = true;
        $results['code'] = '';
        $results['msg'] = '';
        $results['results'] = '';

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

        $qta = (int)$articles_order['cart']['qta'];                     
        
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

                    // disabilito buildRules per l'order_id e' riferito a prod_gas_promotion_id
                    if (!$cartsTable->save($cart, ['checkRules' => false])) {
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

                    // disabilito buildRules per l'order_id e' riferito a prod_gas_promotion_id
                    if (!$cartsTable->save($cart, ['checkRules' => false])) {
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

        // debug($results);
        if(Configure::read('Logs.cart')) Log::write('debug', $results);

        if($results['esito']) {

            $articlesOrdersTable = TableRegistry::get('ArticlesOrdersPromotion');
            
            // debug($articlesOrdersTable);
            if(Configure::read('Logs.cart')) Log::write('debug', 'FACTORY articlesOrdersTable->alias '.$articlesOrdersTable->getAlias());

            if($articlesOrdersTable!==false) 
                $updateResults = $articlesOrdersTable->aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $articles_order, $debug);
        }

        return $results;
    }
}