<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ApiArticleDecorator  extends AppDecorator {
	
	public $serializableAttributes = array('id', 'name');
	public $results; 

    public function __construct($articles)
    {
    	$results = [];
	    // debug($articles);

	    if($articles instanceof \Cake\ORM\ResultSet) {
			foreach($articles as $numResult => $article) {
                $article_detail = $articles->article_details[0];
				$results[$numResult] = $this->_decorate($article, $article_detail);
			}
	    }
	    else 
	    if($articles instanceof \App\Model\Entity\Article) {
            $article_detail = $articles->article_details[0];
			$results = $this->_decorate($articles, $article_detail);  	
	    }

		$this->results = $results;
    }

	private function _decorate($article, $article_detail) {

        $sold_out = false;	           
        $qty_max = 0;
        $store = '';
        $price_pre_discount = 0;
        $price = 0;

        $results = [];
        $results['id'] = $article_detail->id;
        $results['article_id'] = $article->id;
        $results['has_variants'] = false; // e' sempre articolo e la sua variante
        $results['name'] = $this->_getArticleName($article, $article_detail);
            
        if(empty($article_detail->sku))
            $results['sku'] = $article->sku;
        else
            $results['sku'] = $article_detail->sku;

        if(empty($article_detail->descri))
            $results['descri'] = $article->descri;
        else
            $results['descri'] = $article_detail->descri;

        $results['img1'] = $this->_getArticleImg1($article, $article_detail);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        if(empty($article_detail->slug))
            $results['slug'] = $article->slug;
        else
            $results['slug'] = $article->slug.'-'.$article_detail->sku;

        $results['price_min'] = $article->price_min;
        $results['price_max'] = $article->price_max;
        $results['package'] = $article->package;
        $results['qty_min'] = $article->qty_min;
        $results['qty_multiple'] = $article->qty_multiple;    
        $results['ingredients'] = $article->ingredients;    
        $results['is_bio'] = $article->is_bio;    
   
        $results['qty'] = $article_detail->qty;    

        $results['um_code'] = $article_detail->um->code; 
        $results['um_name'] = $article_detail->um->name;  

        $results['um_rif_code'] = $article_detail->um_rif->code; 
        $results['um_rif_name'] = $article_detail->um_rif->name;   

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($price, $article_detail->qty, $article_detail->um, $article_detail->um_rif);          
        $results['conf'] = $article_detail->qty.' '.$article_detail->um->name;

        /*
         * qty max
         */
        if($article->qty_max>0)
            $qty_max = $article->qty_max;
        else {
            if($article_detail->has('stores') && !empty($article_detail->stores)) {
                $store = $article_detail->stores[0]->qty;
                $qty_max = $article_detail->stores[0]->qty;
                if($qty_max==0)
                    $sold_out = true;
            } 
        }
        $results['qty_max'] = $qty_max;

        $store = '';
        if($article_detail->has('stores') && !empty($article_detail->stores)) {
            $store = $article_detail->stores[0]->qty;
        }

        if($article_detail->has('discounts') && !empty($article_detail->discounts)) {
            $price_pre_discount = $article_detail->price;
            $price = $article_detail->discounts[0]->price;
        }
        else 
            $price = $article_detail->price;
                        
        $results['store'] = $store;
        $results['sold_out'] = $sold_out;
        $results['price_pre_discount'] = $price_pre_discount;
        $results['price'] = $price;     
        
        return $results;
    }

	function name() {
		return $this->results;
	}
}