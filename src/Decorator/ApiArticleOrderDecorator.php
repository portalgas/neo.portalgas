<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiArticleOrderDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $articles_orders, $order)
    {
    	$results = [];
	    // debug($articles_orders);

	    if($articles_orders instanceof \Cake\ORM\ResultSet) {
			foreach($articles_orders as $numResult => $articles_order) {
				$results[$numResult] = $this->_decorate($user, $articles_order, $order);
			}
	    }
	    else 
	    if($articles_orders instanceof \App\Model\Entity\ArticlesOrder) {
			$results = $this->_decorate($user, $articles_orders, $order);  	
	    }
        else {
            foreach($articles_orders as $numResult => $articles_order) {
                $results[$numResult] = $this->_decorate($user, $articles_order, $order);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $articles_order, $order) {

        // debug($articles_order);
        
        $sold_out = false;	           
        $qta_max = 0;
        $store = null; // non gestito
        $price_pre_discount = 0;
        $price = 0;

        $results = [];
             
        /*
         * setto tag con gli id
         */
        $ids = [];
        $ids['organization_id'] = $articles_order->organization_id;
        $ids['order_id'] = $articles_order->order_id;
        $ids['article_organization_id'] = $articles_order->article_organization_id;
        $ids['article_id'] = $articles_order->article_id;
        $results['ids'] = $ids;

        /*
         * dati ordine
         */
        $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
        $results['isOpenToPurchasable'] = $lifeCycleOrdersTable->isOpenToPurchasable($user, $order->state_code);
        $results['type_draw'] = $order->type_draw; // ENUM('SIMPLE', 'COMPLETE', 'PROMOTION')

        $results['has_variants'] = false; // e' sempre articolo e la sua variante
        $results['name'] = $articles_order->name;
        $results['stato'] = $articles_order->stato;
        $results['send_mail'] = $articles_order->send_mail;
            
        if(empty($articles_order->codice)) {
            $results['sku'] = '';
            $results['codice'] = '';
        }
        else {
            $results['sku'] = $articles_order->article->codice;
            $results['codice'] = $articles_order->article->codice;
        }

        $results['img1'] = $this->_getArticleImg1($articles_order);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        /*
        if(empty($articles_order->article->slug))
            $results['slug'] = '';
        else
            $results['slug'] = $articles_order->article->slug.'-'.$results['sku'];
        */

        if(empty($articles_order->prezzo))
            $results['price'] = 0;
        else
            $results['price'] = $articles_order->prezzo;

        if(empty($articles_order->pezzi_confezione))
            $results['package'] = 1;
        else
            $results['package'] = $articles_order->pezzi_confezione;

        if(empty($articles_order->qta_minima))
            $results['qta_minima'] = 1;
        else
            $results['qta_minima'] = $articles_order->qta_minima;

        if(empty($articles_order->qta_massima))
            $results['qta_massima'] = 0;
        else
            $results['qta_massima'] = $articles_order->qta_massima;

        if(empty($articles_order->qta_multipli))
            $results['qta_multipli'] = 1;
        else
            $results['qta_multipli'] = $articles_order->qta_multipli;

        $results['qta_cart'] = $articles_order->qta_cart; 
        $results['qta_minima_order'] = $articles_order->qta_minima_order;
        $results['qta_massima_order'] = $articles_order->qta_massima_order;
        $results['qta_massima_order'] = $articles_order->qta_massima_order;

        /*
         * dati da article
         */
        $results['article']['stato'] = $articles_order->article->stato;
        $results['qta'] = $articles_order->article->qta; 
        if(empty($articles_order->article->bio))
            $results['is_bio'] = '';
        else {
            if($articles_order->article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        if(empty($articles_order->article->nota))
            $results['descri'] = '';
        else
            $results['descri'] = $articles_order->article->nota;

        if(empty($articles_order->article->ingredienti))
            $results['ingredients'] = '';
        else
            $results['ingredients'] = $articles_order->article->ingredienti;

        $results['um'] = $articles_order->article->um;   
        $results['um_rif'] = $articles_order->article->um_riferimento;   

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($results['price'], $results['qta'], $results['um'], $results['um_rif']);          
        $results['conf'] = $results['qta'].' '.$results['um'];

        /*
         * cart
         * se non ci sono ancora acquisti 
         * 'user_id' => (int) 0,   
         * 'qta' => (int) 0,
         * 'qta_new' => (int) 0 
         */
        $results['cart'] = $articles_order->cart; 
        if(isset($articles_order->cart) && !empty($articles_order->cart->user_id)) { 
         
            $final_qta = 0;
            $final_price = 0;

            ($articles_order->cart->qta_forzato > 0 ) ? $final_qta = $articles_order->cart->qta_forzato: $final_qta = $articles_order->cart->qta;
            $results['cart']['final_qta'] = $final_qta;

            if($articles_order->cart->qta_forzato > 0) {
                $results['cart']['is_qta_mod'] = true;
            }
            else {
                $results['cart']['is_qta_mod'] = false;
            }
            if($articles_order->cart->importo_forzato > 0) {
                $results['cart']['final_price'] = $articles_order->cart->importo_forzato;
                $results['cart']['is_import_mod'] = true;
            }
            else {
                $results['cart']['final_price'] = ($final_qta * $articles_order->prezzo);
                $results['cart']['is_import_mod'] = false;
            }
        }

        /*
         * promotions
         */
        if(isset($articles_order->prod_gas_articles_promotion) && !empty($articles_order->prod_gas_articles_promotion)) {
            $results['promotion'] = $articles_order->prod_gas_articles_promotion;

            // sono gia' valorizzati quando si associano gli articoli all'ordine
            $results['qta_minima_order'] = $articles_order->prod_gas_articles_promotion->qta;
            $results['qta_massima_order'] = $articles_order->prod_gas_articles_promotion->qta;

            $results['price_pre_discount'] = $articles_order->article->prezzo;
        }

        /*
         * order.staste_code RI-OPEN-VALIDATE
         */
        if(isset($articles_order['riopen'])) {
            if(isset($articles_order['riopen']['differenza_da_ordinare'])) 
                $results['riopen']['differenza_da_ordinare'] = $articles_order['riopen']['differenza_da_ordinare'];
            if(isset($articles_order['riopen']['differenza_importo'])) 
                $results['riopen']['differenza_importo'] = $articles_order['riopen']['differenza_importo'];
        }
           
        return $results;
    }

	function name() {
		return $this->results;
	}
}