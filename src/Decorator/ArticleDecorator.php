<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ArticleDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($articles)
    {
        $results = [];
        $i=0;
	    // debug($articles);

	    if($articles instanceof \Cake\ORM\ResultSet) {
			foreach($articles as $article) {
                if($article->has('article_details') && !empty($article->article_details))  {
                    $results[$i] = $this->_decorate($article);
                    $i++;
                }
			}
	    }
	    else 
	    if($articles instanceof \App\Model\Entity\Article) {
            if($articles->has('article_details') && !empty($articles->article_details))  
			    $results = $this->_decorate($articles);  	
	    }

		$this->results = $results;
    }

	private function _decorate($article) {

        $results = [];
        if(count($article->article_details)>1) 
    		$results = $article;

        /*
        * ctrl se l'articolo ha varianti
         */
        if(count($article->article_details)>1) 
            $has_variants = true;
        else 
            $has_variants = false;   
        $results['has_variants'] = $has_variants; 

		foreach($article->article_details as $numResult2 => $article_detail) {

            $sold_out = false;	           
            $qty_max = 0;
            $store = '';
            $price_pre_discount = 0;
            $price = 0;

            /*
             * name
             */
            $names = $this->_getArticleName($article, $article_detail);
            $article_detail->names = $names;
            $results['article_details'][$numResult2]['names'] = $names;  
           
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
                
            if($has_variants) {
                $results['article_details'][$numResult2]['conf'] = $article_detail->qty.' '.$article_detail->um->name;
                $results['article_details'][$numResult2]['um_rif_label'] = $this->_getArticlePrezzoUM($price, $article_detail->qty, $article_detail->um, $article_detail->um_rif);
                $results['article_details'][$numResult2]['qty_max'] = $qty_max;
                $results['article_details'][$numResult2]['store'] = $store;
                $results['article_details'][$numResult2]['sold_out'] = $sold_out;
                $results['article_details'][$numResult2]['price_pre_discount'] = $price_pre_discount;
                $results['article_details'][$numResult2]['price'] = $price;  
            }
            else {
                $stores = [];
                if(isset($article->article_details[0]->stores[0])) {
                    $stores = $article->article_details[0]->stores[0];
                    unset($article->article_details[0]->stores);    
                }
                
                $discounts = [];
                if(isset($article->article_details[0]->discounts[0])) {
                    $discounts = $article->article_details[0]->discounts[0];
                    unset($article->article_details[0]->discounts);
                }
                    
                $article_details = $article->article_details[0];
                unset($article->article_details);

                $results = $article;

                $results['article_details'] = $article_details;
                $results['conf'] = $article_detail->qty.' '.$article_detail->um->name;
                $results['um_rif_label'] = $this->_getArticlePrezzoUM($price, $article_detail->qty, $article_detail->um, $article_detail->um_rif);
                $results['qty_max'] = $qty_max;
                $results['store'] = $store;
                $results['sold_out'] = $sold_out;
                $results['price_pre_discount'] = $price_pre_discount;
                $results['price'] = $price;
                $results['stores'] = $stores; 
                $results['discounts'] = $discounts; 


            }  // end if($has_variants)           
        }

        return $results;
    }

	function name() {
		return $this->results;
	}
    
    /**
     * For illustration purposes $this->fname (and $this->lname, similarly) does
     * the following internally:
     *
     * function fname() {
     *     return $this->attributes['fname'];
     * }
     *
     * You do not need to define getters for any of the attributes that are
     * available from the passed data array that you instantiate the decorator
     * with.
     */
}