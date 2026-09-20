<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\CartDecorator;
use Cake\Log\Log;

class CartSplitComponent extends Component {

	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = []) {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
        $this->_registry->load('Cart');		
	}

	public function getByOrder($user, $organization_id, $order_id, $user_id) {
		
		$results = [];
        $cartsTable = TableRegistry::get('Carts');
        $carts = $cartsTable->getByOrder($user, $organization_id, $order_id, $user_id, [], []);

        if(!empty($carts)) {

            $ordersTable = TableRegistry::get('Orders');
            $where = ['Orders.organization_id' => $organization_id, 'Orders.id' => $order_id];
            $order = $ordersTable->find()->where($where)->first();
	
            $cartSplitsTable = TableRegistry::get('CartSplits');
            $carts = $carts->toArray();
            foreach($carts as $numResult => $cart) {

                $articles_order = null;
                $articles_order = $cart['articles_order'];
                $articles_order['article'] = $cart['article'];
                $articles_order_decorate = new ApiArticleOrderDecorator($this->_user, $articles_order, $order);
                $results[$numResult] = $articles_order_decorate->results;

				$where = ['organization_id' => $cart->organization_id,
                        'order_id' => $cart->order_id,
                        'user_id' => $user_id,
                        'article_organization_id' => $cart->article_organization_id,
                        'article_id' => $cart->article_id
                ]; 

                $cartSplits = $cartSplitsTable->find()->where($where)->all();
                if($cartSplits->count()>0) {

					$cart_decorate = new CartDecorator($this->_user, $cart);
					
					$results[$numResult]['cart'] = [];
                    $results[$numResult]['cart'] = $cart_decorate->results;
                    $results[$numResult]['cart_splits'] = $cartSplits;
                }
                else {
                    $results[$numResult]['cart_splits'] = [];
                    /*
                    $results[$numResult]['cart_splits'] = [];
                    $results[$numResult]['cart_splits'][0]['id'] = 1;
                    $results[$numResult]['cart_splits'][0]['organization_id'] = $cart->organization_id;
                    $results[$numResult]['cart_splits'][0]['order_id'] = $cart->order_id;
                    $results[$numResult]['cart_splits'][0]['user_id'] = $cart->user_id;
                    $results[$numResult]['cart_splits'][0]['article_organization_id'] = $cart->article_organization_id;
                    $results[$numResult]['cart_splits'][0]['article_id'] = $cart->article_id;
                    $results[$numResult]['cart_splits'][0]['name'] = 'Mio';
                    $results[$numResult]['cart_splits'][0]['qta'] = (!empty($cart->qta_forzato)) ? $cart->qta_forzato: $cart->qta;
                    $results[$numResult]['cart_splits'][0]['trasport'] = !empty($summaryOrderTrasport) ? $summaryOrderTrasport->importo_trasport: null;

                    $results[$numResult]['cart_splits'][1]['id'] = 2;
                    $results[$numResult]['cart_splits'][1]['organization_id'] = $cart->organization_id;
                    $results[$numResult]['cart_splits'][1]['order_id'] = $cart->order_id;
                    $results[$numResult]['cart_splits'][1]['user_id'] = $cart->user_id;
                    $results[$numResult]['cart_splits'][1]['article_organization_id'] = $cart->article_organization_id;
                    $results[$numResult]['cart_splits'][1]['article_id'] = $cart->article_id;
                    $results[$numResult]['cart_splits'][1]['name'] = 'Test';
                    $results[$numResult]['cart_splits'][1]['qta'] = 1;
                    $results[$numResult]['cart_splits'][1]['trasport'] = !empty($summaryOrderTrasport) ? $summaryOrderTrasport->importo_trasport: null; 
                    */                   
                }
            } // foreach($carts as $numResult => $cart)
        } // end if(!empty($carts))

		return $results;	
	}
}