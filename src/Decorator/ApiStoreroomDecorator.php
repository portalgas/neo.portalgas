<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiStoreroomDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $storerooms)
    {
    	$results = [];
	    // debug($storerooms);

	    if($storerooms instanceof \Cake\ORM\ResultSet) {
			foreach($storerooms as $numResult => $storeroom) {
				$results[$numResult] = $this->_decorate($user, $storeroom);
			}
	    }
	    else 
	    if($storerooms instanceof \App\Model\Entity\Storeroom) {
			$results = $this->_decorate($user, $storerooms);
	    }
        else {
            foreach($storerooms as $numResult => $storeroom) {
                $results[$numResult] = $this->_decorate($user, $storeroom);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $storeroom) {

        // debug($storeroom);
        
        $results = [];             
            
        if(empty($storeroom->codice)) {
            $results['sku'] = '';
            $results['codice'] = '';
        }
        else {
            $results['sku'] = $storeroom->article->codice;
            $results['codice'] = $storeroom->article->codice;
        }

        $results['img1'] = $this->_getArticleImg1($storeroom);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        /*
        if(empty($storeroom->article->slug))
            $results['slug'] = '';
        else
            $results['slug'] = $storeroom->article->slug.'-'.$results['sku'];
        */

        if(empty($storeroom->prezzo))
            $results['price'] = 0;
        else
            $results['price'] = $storeroom->prezzo;

        if(empty($storeroom->pezzi_confezione))
            $results['package'] = 1;
        else
            $results['package'] = $storeroom->pezzi_confezione;

        if(empty($storeroom->qta_minima))
            $results['qta_minima'] = 1;
        else
            $results['qta_minima'] = $storeroom->qta_minima;

        if(empty($storeroom->qta_massima))
            $results['qta_massima'] = 0;
        else
            $results['qta_massima'] = $storeroom->qta_massima;

        if(empty($storeroom->qta_multipli))
            $results['qta_multipli'] = 1;
        else
            $results['qta_multipli'] = $storeroom->qta_multipli;

        $results['qta_cart'] = $storeroom->qta_cart; 
        $results['qta_minima_order'] = $storeroom->qta_minima_order;
        $results['qta_massima_order'] = $storeroom->qta_massima_order;
        $results['qta_massima_order'] = $storeroom->qta_massima_order;

        /*
         * dati da article
         */
        $results['article']['stato'] = $storeroom->article->stato;
        $results['qta'] = $storeroom->article->qta; 
        if(empty($storeroom->article->bio))
            $results['is_bio'] = '';
        else {
            if($storeroom->article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        if(empty($storeroom->article->nota))
            $results['descri'] = '';
        else
            $results['descri'] = $storeroom->article->nota;

        if(empty($storeroom->article->ingredienti))
            $results['ingredients'] = '';
        else
            $results['ingredients'] = $storeroom->article->ingredienti;

        $results['um'] = $storeroom->article->um;   
        $results['um_rif'] = $storeroom->article->um_riferimento;   

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($results['price'], $results['qta'], $results['um'], $results['um_rif']);          
        $results['conf'] = $results['qta'].' '.$results['um'];

        /*
         * cart
         * se non ci sono anocra acquisti 
         * 'user_id' => (int) 0,   
         * 'qta' => (int) 0,
         * 'qta_new' => (int) 0 
         */
        $results['cart'] = $storeroom->cart; 
        if(isset($storeroom->cart) && !empty($storeroom->cart->user_id)) { 
         
            $final_qta = 0;
            $final_price = 0;

            ($storeroom->cart->qta_forzato > 0 ) ? $final_qta = $storeroom->cart->qta_forzato: $final_qta = $storeroom->cart->qta;
            $results['cart']['final_qta'] = $final_qta;

            if($storeroom->cart->importo_forzato > 0 ) {
                $results['cart']['final_price'] = $storeroom->cart->importo_forzato;
            }
            else {
                $results['cart']['final_price'] = ($final_qta * $storeroom->prezzo);
            }
        }

        /*
         * promotions
         */
        if(isset($storeroom->prod_gas_articles_promotion) && !empty($storeroom->prod_gas_articles_promotion)) {
            $results['promotion'] = $storeroom->prod_gas_articles_promotion;

            // sono gia' valorizzati quando si associano gli articoli all'ordine
            $results['qta_minima_order'] = $storeroom->prod_gas_articles_promotion->qta;
            $results['qta_massima_order'] = $storeroom->prod_gas_articles_promotion->qta;

            $results['price_pre_discount'] = $storeroom->article->prezzo;
        }

        /*
         * order.staste_code RI-OPEN-VALIDATE
         */
        if(isset($storeroom['riopen'])) {
            if(isset($storeroom['riopen']['differenza_da_ordinare'])) 
                $results['riopen']['differenza_da_ordinare'] = $storeroom['riopen']['differenza_da_ordinare'];
            if(isset($storeroom['riopen']['differenza_importo'])) 
                $results['riopen']['differenza_importo'] = $storeroom['riopen']['differenza_importo'];
        }
           
        return $results;
    }

	function name() {
		return $this->results;
	}
}