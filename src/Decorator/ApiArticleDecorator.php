<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiArticleDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $articles)
    {
    	$results = [];
	    // debug($articles);

	    if($articles instanceof \Cake\ORM\ResultSet) {
			foreach($articles as $numResult => $article) {
				$results[$numResult] = $this->_decorate($user, $article);
			}
	    }
	    else 
	    if($articles instanceof \App\Model\Entity\Article) {
			$results = $this->_decorate($user, $articles);  	
	    }
        else {
            foreach($articles as $numResult => $article) {
                $results[$numResult] = $this->_decorate($user, $article);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $article) {

        // debug($article);
        
        $results = [];

        /* 
         * ctrl se la gestione 
         *  e' del gas/referente => edit 
         *  e' del produttore o des => no edit
         */
        if(!isset($article->owner_supplier_organization)) {
            if($user->organization->id==$article->organization_id)
                $article->can_edit = true;
            else 
                $article->can_edit = false;
        }
        else {
            if($article->owner_supplier_organization->organization_id==$article->organization_id && 
               $article->owner_supplier_organization->id==$article->supplier_organization_id)
                $article->can_edit = true;
            else 
                $article->can_edit = false;
        }
        $results = $article->toArray();
        $results['img1'] = $this->_getArticleImg1($article);
        $results['img1_width'] = Configure::read('Article.img.preview.width');
        $results['img1_size'] = $this->_getArticleImg1Size($article);

        $results['is_select'] = false; // per vue js x gestire eventuali checkbox 

        if(empty($article->prezzo)) {
            $results['prezzo'] = 0;
            $results['prezzo_'] = '0,00';
            $results['prezzo_e'] = $results['prezzo_'].' &euro';
        }
        else {
            $results['prezzo'] = $article->prezzo;
            $results['prezzo_'] = number_format($article->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
            $results['prezzo_e'] = $results['prezzo_'].' &euro';
        }

        if(empty($article->bio))
            $results['is_bio'] = '';
        else {
            if($article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        $results['um_rif_label'] = $this->_getArticlePrezzoUM($article->prezzo, $article->qta, $article->um, $article->um_rif);          
        $results['conf'] = $article->qta.' '.$article->um;
      
        return $results;
    }

	function name() {
		return $this->results;
	}
}