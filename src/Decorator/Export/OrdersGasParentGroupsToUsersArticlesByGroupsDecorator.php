<?php
declare(strict_types=1);

namespace App\Decorator\Export;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use App\Decorator\AppDecorator;

class OrdersGasParentGroupsToUsersArticlesByGroupsDecorator extends AppDecorator {
	
	public $serializableAttributes = array('id', 'name');
	public $results; 

    public function __construct($user, $orders)
    {
		if(empty($orders)) 
			return [];

		foreach($orders as $order) {
		
			if(!isset($this->results[$order->id])) {
				$this->results[$order->id] = new \stdClass();
				$this->results[$order->id]->delivery = new \stdClass();
				$this->results[$order->id]->suppliers_organization = new \stdClass();
				$this->results[$order->id]->gas_group = new \stdClass();
				$this->results[$order->id]->users = [];
			}

			// header: gruppo e consegna dell'ordine titolare
			$this->results[$order->id]->delivery->luogo = $order->delivery->luogo;
			$this->results[$order->id]->suppliers_organization->name = $order->suppliers_organization->name;
			if(!empty($order->gas_group))
				$this->results[$order->id]->gas_group->name = $order->gas_group->name;

			foreach($order->article_orders as $article_order) {
				
				// debug($order->id.' '.$article_order->name.' '.$article_order->article->img1);

				foreach($article_order->carts as $cart) {

					if(!isset($this->results[$order->id]->users[$cart->user_id])) {
						$this->results[$order->id]->users[$cart->user_id] = new \stdClass(); 
						$this->results[$order->id]->users[$cart->user_id]->user = new \stdClass(); 
						$this->results[$order->id]->users[$cart->user_id]->article = []; 
						$this->results[$order->id]->users[$cart->user_id]->article_orders = []; 
					}

					$i = count($this->results[$order->id]->users[$cart->user_id]->article);
						
					if(!isset($this->results[$order->id]->users[$cart->user_id]->article_orders[$i])) {
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]= new \stdClass();
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart = new \stdClass(); 
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article = new \stdClass();  
					} 
					
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article_id = $article_order->article_id;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->name = $article_order->name;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->prezzo = $article_order->prezzo;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->prezzo_ = number_format($article_order->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->prezzo_e = number_format($article_order->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro;';
					if(!isset($this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->qta_cart))
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->qta_cart = $article_order->qta_cart;
					else 
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->qta_cart += $article_order->qta_cart;
	
					if(empty($article_order->article->bio))
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->is_bio = '';
					else {
						if($article_order->article->bio=='Y')
							$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->is_bio = true;
						else
							$this->results[$order->id]->users[$cart->user_id]->article[$i]->article->is_bio = false;
					}

					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->img1 = $this->_getArticleImg1($article_order);
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->img1_width = Configure::read('Article.img.preview.width');
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->um_rif_label = $this->_getArticlePrezzoUM($article_order->prezzo, $article_order->article->qta, $article_order->article->um, $article_order->article->um_riferimento);          
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->article->conf = $article_order->article->qta.' '.$article_order->article->um;

					/*
					* calcolo la qta perche' article_order->qta_cart non considera le qta_forzate
					*/										
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->qta = $cart->qta;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->qta_forzato = $cart->qta_forzato;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->importo_forzato = $cart->importo_forzato;
					$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->user_id = $cart->user_id;
	
					$this->results[$order->id]->users[$cart->user_id]->user->id = $cart->user->id;
					$this->results[$order->id]->users[$cart->user_id]->user->name = $cart->user->name;
					$this->results[$order->id]->users[$cart->user_id]->user->email = $cart->user->email;
											
					$final_qta = 0;
					($cart->qta_forzato > 0 ) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;
					if(!isset($this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_qta))
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_qta = $final_qta;
					else 
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_qta += $final_qta;

					if($cart->qta_forzato > 0) {
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->is_qta_mod = true;
					}
					else {
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->is_qta_mod = false;
					}
					if($cart->importo_forzato > 0) {
						if(!isset($this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price))
							$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price = $cart->importo_forzato;
						else 
							$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price += $cart->importo_forzato;
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->is_import_mod = true;
					}
					else {
						if(!isset($this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price))
							$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price = ($final_qta * $article_order->prezzo);
						else 
							$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->final_price += ($final_qta * $article_order->prezzo);
						$this->results[$order->id]->users[$cart->user_id]->article_orders[$i]->cart->is_import_mod = false;
					}
				}
			}
		}
		// debug($this->results);
		
        return $this->results;
    }

	function name() {
		return $this->results;
	}
}