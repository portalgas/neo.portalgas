<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\CartDecorator;
use App\Decorator\ApiCartDecorator;
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

				$where = ['organization_id' => $organization_id,
                        'order_id' => $order_id,
                        'user_id' => $user_id,
                        'article_organization_id' => $cart->article_organization_id,
                        'article_id' => $cart->article_id
                ]; 

                $cartSplits = $cartSplitsTable->find()->where($where)->all();
                if($cartSplits->count()>0) {
                    $results[$numResult]['cart_splits'] = $cartSplits;
                }
                else {
                    $results[$numResult]['cart_splits'] = [];
                }

                $cart_decorate = new CartDecorator($this->_user, $cart);
                $results[$numResult]['cart'] = [];
                $results[$numResult]['cart'] = $cart_decorate->results;
                
            } // foreach($carts as $numResult => $cart)
        } // end if(!empty($carts))

		return $results;	
	}

	public function getByOrderGroupByFamilies($user, $organization_id, $order_id, $user_id) {
		
        $i=0;
		$results = [];
        $article_ids = [];
        
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Orders.organization_id' => $organization_id, 'Orders.id' => $order_id];
        $order = $ordersTable->find()->where($where)->first();

        /*
         * recupero famiglie
         * */
        $cartSplitsTable = TableRegistry::get('CartSplits');
        $where = ['CartSplits.organization_id' => $organization_id,
                    'CartSplits.order_id' => $order_id,
                    'CartSplits.user_id' => $user_id];
        $cartSplitNames = $cartSplitsTable->find()->select(['name'])->where($where)->group(['name'])->all();        

        foreach($cartSplitNames as $cartSplitName) {
            /*
            * dati suddivisi per la famiglia
            * */            
            $where = ['CartSplits.organization_id' => $organization_id,
                        'CartSplits.order_id' => $order_id,
                        'CartSplits.user_id' => $user_id,
                        'CartSplits.name' => $cartSplitName->name];
            $cartSplits = $cartSplitsTable->find()
                                            ->contain(['Carts' => ['ArticlesOrders', 'Articles']])
                                            ->where($where)
                                            ->all();  

            $name = trim($cartSplitName->name);
            if(empty($name)) $name = $user->username;

            $results[$i] = [];
            $results[$i]['name'] = $name;
            $results[$i]['cart_splits'] = $cartSplits->toArray();
            $i++;
        } // end foreach($cartSplitNames as $cartSplitName)

        if(!empty($results)) {
            foreach($results as $numResult => $result) {
                foreach($result['cart_splits'] as $numResult2 => $cart_split) {
                    $articles_order = $cart_split['cart']['articles_order'];
                    $articles_order['article'] = $cart_split['cart']['article'];
                    $articles_order_decorate = new ApiArticleOrderDecorator($this->_user, $cart_split['cart']['articles_order'], $order);
                    $results[$numResult]['cart_splits'][$numResult2]['cart']['articles_order'] = $articles_order_decorate->results;
    
                    $cart_decorate = new CartDecorator($this->_user, $cart_split['cart']);
                    $results[$numResult]['cart_splits'][$numResult2]['cart'] = $cart_decorate->results;
                    
                    $article_ids[$cart_split['article_id']] = $cart_split['article_id'];
                } 
            } // end foreach($results as $numResult => $result)

        } // end if(!empty($results))

        /*
         * recupero acquisti non suddivisi
         */
        $where = [];
        if(!empty($article_ids))
            $where = ['Carts.article_id not in ' => $article_ids];

        $cartsTable = TableRegistry::get('Carts');
        $carts = $cartsTable->getByOrder($user, $organization_id, $order_id, $user_id, $where, []);

        if(!empty($carts)) {
            $carts = $carts->toArray();
            foreach($carts as $numResult => $cart) {

                $articles_order = null;
                $articles_order = $cart['articles_order'];
                $articles_order['article'] = $cart['article'];
                $articles_order_decorate = new ApiArticleOrderDecorator($this->_user, $articles_order, $order);
                $carts[$numResult]['articles_order'] = $articles_order_decorate->results;

                $cart_decorate = new ApiCartDecorator($this->_user, $cart, $articles_order);
                $carts[$numResult] = $cart_decorate->results;
                
            } // foreach($carts as $numResult => $cart)            
        }

		return ['cart_splits' => $results, 'carts' => $carts];	
	}    
}