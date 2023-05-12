<?php
declare(strict_types=1);

namespace App\Decorator\Export;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use App\Decorator\AppDecorator;

class OrdersGasParentGroupsToArticlesDecorator extends AppDecorator {
	
	public $serializableAttributes = array('id', 'name');
	public $results; 

    public function __construct($user, $orders)
    {
		if(empty($orders)) 
			return [];

		foreach($orders as $order) {
		
			foreach($order->article_orders as $article_order) {
				
				if(!isset($this->results[$article_order->article_id])) {
					$this->results[$article_order->article_id] = new \stdClass();
					$this->results[$article_order->article_id]->article = new \stdClass();
					$this->results[$article_order->article_id]->cart = new \stdClass();	
				}

				// debug($order->id.' '.$article_order->name.' '.$article_order->article->img1);
				
				$this->results[$article_order->article_id]->article_id = $article_order->article_id;
				$this->results[$article_order->article_id]->name = $article_order->name;
				$this->results[$article_order->article_id]->prezzo = $article_order->prezzo;
				$this->results[$article_order->article_id]->prezzo_ = number_format($article_order->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
				$this->results[$article_order->article_id]->prezzo_e = number_format($article_order->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro';		
				if(!isset($this->results[$article_order->article_id]->qta_cart))
					$this->results[$article_order->article_id]->qta_cart = $article_order->qta_cart;
				else
					$this->results[$article_order->article_id]->qta_cart += $article_order->qta_cart;
				
				if(empty($article_order->article->bio))
					$this->results[$article_order->article_id]->article->is_bio = '';
				else {
					if($article_order->article->bio=='Y')
						$this->results[$article_order->article_id]->article->is_bio = true;
					else
						$this->results[$article_order->article_id]->article->is_bio = false;
				}
				$this->results[$article_order->article_id]->article->img1 = $this->_getArticleImg1($article_order);
				$this->results[$article_order->article_id]->article->img1_width = Configure::read('Article.img.preview.width');
				$this->results[$article_order->article_id]->article->um_rif_label = $this->_getArticlePrezzoUM($article_order->prezzo, $article_order->article->qta, $article_order->article->um, $article_order->article->um_riferimento);          
				$this->results[$article_order->article_id]->article->conf = $article_order->article->qta.' '.$article_order->article->um;

				foreach($article_order->carts as $cart) {
					
					/*
					* calcolo la qta perche' article_order->qta_cart non considera le qta_forzate
					*/
					$final_qta = 0;
					($cart->qta_forzato > 0 ) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;
					if(!isset($this->results[$article_order->article_id]->cart->final_qta))
						$this->results[$article_order->article_id]->cart->final_qta = $final_qta;
					else
						$this->results[$article_order->article_id]->cart->final_qta += $final_qta;

					if($cart->qta_forzato > 0) {
						$this->results[$article_order->article_id]->cart->is_qta_mod = true;
					}
					else {
						$this->results[$article_order->article_id]->cart->is_qta_mod = false;
					}
					if($cart->importo_forzato > 0) {
						$this->results[$article_order->article_id]->cart->final_price += $cart->importo_forzato;
						$this->results[$article_order->article_id]->cart->is_import_mod = true;
					}
					else {
						if(!isset($this->results[$article_order->article_id]->cart->final_price))
							$this->results[$article_order->article_id]->cart->final_price = ($final_qta * $article_order->prezzo);
						else
							$this->results[$article_order->article_id]->cart->final_price += ($final_qta * $article_order->prezzo);
						$this->results[$article_order->article_id]->cart->is_import_mod = false;
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