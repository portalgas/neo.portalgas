<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ApiArticleOrderDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($articles_orders)
    {
    	$results = [];
	    // debug($articles_orders);

	    if($articles_orders instanceof \Cake\ORM\ResultSet) {
			foreach($articles_orders as $numResult => $articles_order) {
				$results[$numResult] = $this->_decorate($articles_order);
			}
	    }
	    else 
	    if($articles_orders instanceof \App\Model\Entity\ArticlesOrder) {
			$results = $this->_decorate($articles_orders);  	
	    }
        else {
            foreach($articles_orders as $numResult => $articles_order) {
                $results[$numResult] = $this->_decorate($articles_order);
            }
        }

		$this->results = $results;
    }

	private function _decorate($articles_order) {

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

        $results['has_variants'] = false; // e' sempre articolo e la sua variante
        $results['name'] = $articles_order->name;
        $results['stato'] = $articles_order->stato;
        $results['send_mail'] = $articles_order->send_mail;
            
        if(empty($articles_order->codice))
            $results['sku'] = '';
        else
            $results['sku'] = $articles_order->article->codice;

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
         */
        $results['cart'] = $articles_order->cart; 

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