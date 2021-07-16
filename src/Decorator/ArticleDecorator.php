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
                $results[$i] = $this->_decorate($article);
                $i++;
			}
	    }
	    else 
	    if($articles instanceof \App\Model\Entity\Article) {
		    $results = $this->_decorate($articles);  	
	    }

		$this->results = $results;
    }

	private function _decorate($article) {

        $results = [];

        $sold_out = false;	           
        $qty_max = 0;
        $store = '';
        $price_pre_discount = 0;
        $price = 0;

        $results['name'] = $article->name;

        $results['sku'] = $article->codice;
        $results['codice'] = $article->codice;

        $results['price'] = $article->prezzo;
        $results['prezzo'] = $article->prezzo;

        $results['img'] = $article->img1;  
        $results['img1'] = $this->_getArticleImg1($article);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        $results['stato'] = $article->stato;
        $results['qta'] = $article->qta; 
        if(empty($article->bio))
            $results['is_bio'] = '';
        else {
            if($article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        if(empty($article->nota))
            $results['descri'] = '';
        else
            $results['descri'] = $article->nota;

        if(empty($article->ingredienti))
            $results['ingredients'] = '';
        else
            $results['ingredients'] = $article->ingredienti;

        $results['um'] = $article->um;   
        $results['um_rif'] = $article->um_riferimento;   

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($results['price'], $results['qta'], $results['um'], $results['um_rif']);          
        $results['conf'] = $results['qta'].' '.$results['um'];
        
        $results['package'] = $article->pezzi_confezione;
        $results['pezzi_confezione'] = $article->pezzi_confezione;
        $results['qta_minima'] = $article->qta_minima;
        $results['qta_massima'] = $article->qta_massima;
        $results['qta_multipli'] = $article->qta_multipli;

        return $results;
    }

	function name() {
		return $this->results;
	}    
}