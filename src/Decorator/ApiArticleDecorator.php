<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ApiArticleDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($articles)
    {
    	$results = [];
	    // debug($articles);

	    if($articles instanceof \Cake\ORM\ResultSet) {
			foreach($articles as $numResult => $row) {
				$results[$numResult] = $this->_decorate($row);
			}
	    }
	    else 
	    if($articles instanceof \App\Model\Entity\ArticlesOrder) {
			$results = $this->_decorate($articles);  	
	    }
        else {
            foreach($articles as $numResult => $row) {
                $results[$numResult] = $this->_decorate($row);
            }
        }

		$this->results = $results;
    }

	private function _decorate($row) {

        // debug($row);
        
        $sold_out = false;	           
        $qty_max = 0;
        $store = null; // non gestito
        $price_pre_discount = 0;
        $price = 0;

        $results = [];
             
        /*
         * setto tag con gli id
         */
        $ids = [];
        $ids['organization_id'] = $row->organization_id;
        $ids['order_id'] = $row->order_id;
        $ids['article_organization_id'] = $row->article_organization_id;
        $ids['article_id'] = $row->article_id;
        $results['ids'] = $ids;

        $results['stato'] = $row->article->stato;
        $results['article_order']['stato'] = $row->stato;
        $results['article_order']['stato'] = $row->send_mail;

        $results['has_variants'] = false; // e' sempre articolo e la sua variante
        $results['name'] = $row->name;
            
        if(empty($row->codice))
            $results['sku'] = '';
        else
            $results['sku'] = $row->article->codice;

        if(empty($row->article->nota))
            $results['descri'] = '';
        else
            $results['descri'] = $row->article->nota;

        if(empty($row->article->ingredienti))
            $results['ingredients'] = '';
        else
            $results['ingredients'] = $row->article->ingredienti;

        $results['img1'] = $this->_getArticleImg1($row);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        if(empty($row->article->slug))
            $results['slug'] = '';
        else
            $results['slug'] = $row->article->slug.'-'.$results['sku'];

        if(empty($row->prezzo))
            $results['price'] = '';
        else
            $results['price'] = $row->prezzo;

        if(empty($row->pezzi_confezione))
            $results['package'] = '';
        else
            $results['package'] = $row->pezzi_confezione;

        if(empty($row->qta_minima))
            $results['qty_min'] = '';
        else
            $results['qty_min'] = $row->qta_minima;

        if(empty($row->qta_multipli))
            $results['qty_multiple'] = '';
        else
            $results['qty_multiple'] = $row->qta_multipli;

        if(empty($row->article->bio))
            $results['is_bio'] = '';
        else {
            if($row->article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        $results['qty_cart'] = $row->qta_cart; 
        $results['qty_minima_order'] = $row->qta_minima_order;
        $results['qty_massima_order'] = $row->qta_massima_order;
        $results['qty_multipli'] = $row->qta_multipli;
        $results['qty'] = $row->article->qta;  
        $results['price_min'] = 0;
        $results['price_max'] = 0;  

        $results['um'] = $row->article->um;   
        $results['um_rif'] = $row->article->um_riferimento;   

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($results['price'], $results['qty'], $results['um'], $results['um_rif']);          
        $results['conf'] = $results['qty'].' '.$results['um'];

        /*
         * qty max
         */
        if(empty($row->qta_massima))
            $results['qty_max'] = 0;
        else
            $results['qty_max'] = $row->qta_massima;

        $results['store'] = $store; // non gestito
        $results['cart'] = $row->cart; 

        /*
         * promotions
         */
        if(isset($row->prod_gas_articles_promotion) && !empty($row->prod_gas_articles_promotion)) {
            $results['promotion'] = $row->prod_gas_articles_promotion;

            // sono gia' valorizzati quando si associano gli articoli all'ordine
            $results['qty_minima_order'] = $row->prod_gas_articles_promotion->qta;
            $results['qty_massima_order'] = $row->prod_gas_articles_promotion->qta;

            $results['price_pre_discount'] = $row->article->prezzo;
        }

        /*
         * order.staste_code RI-OPEN-VALIDATE
         */
        if(isset($row['riopen'])) {
            if(isset($row['riopen']['differenza_da_ordinare'])) 
                $results['riopen']['differenza_da_ordinare'] = $row['riopen']['differenza_da_ordinare'];
            if(isset($row['riopen']['differenza_importo'])) 
                $results['riopen']['differenza_importo'] = $row['riopen']['differenza_importo'];
        }
           
        return $results;
    }

	function name() {
		return $this->results;
	}
}