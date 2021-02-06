<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiProdGasArticlesPromotionDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $prod_gas_articles_promotions, $promotion=null)
    {
	    // debug($prod_gas_articles_promotions);

	    if($prod_gas_articles_promotions instanceof \Cake\ORM\ResultSet) {
			foreach($prod_gas_articles_promotions as $numResult => $prod_gas_articles_promotion) {
				$results[$numResult] = $this->_decorate($user, $prod_gas_articles_promotion, $promotion);
			}
	    }
	    else 
	    if($prod_gas_articles_promotions instanceof \App\Model\Entity\ProdGasArticlesPromotion) {
			$results = $this->_decorate($user, $prod_gas_articles_promotions, $promotion);  	
	    }
        else {
            foreach($prod_gas_articles_promotions as $numResult => $prod_gas_articles_promotion) {
                $results[$numResult] = $this->_decorate($user, $prod_gas_articles_promotion, $promotion);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $prod_gas_articles_promotion, $promotion) {

        // debug($prod_gas_articles_promotion);

        $prod_gas_articles_promotion->article->img1 = $this->_getArticleImg1($prod_gas_articles_promotion);        
        $prod_gas_articles_promotion->article->img1_width = Configure::read('Article.img.preview.width');

        /*
         * dati da article
         */
        if(empty($prod_gas_articles_promotion->article->bio))
            $prod_gas_articles_promotion->article->is_bio = false;
        else {
            if($prod_gas_articles_promotion->article->bio=='Y')
                $prod_gas_articles_promotion->article->is_bio = true;
            else
                $prod_gas_articles_promotion->article->is_bio = false;
        }

        $prod_gas_articles_promotion->article->um_rif_label = $this->_getArticlePrezzoUM($prod_gas_articles_promotion->article->prezzo, $prod_gas_articles_promotion->article->qta, $prod_gas_articles_promotion->article->um, $prod_gas_articles_promotion->article->um_rif);          
        $prod_gas_articles_promotion->article->conf = $prod_gas_articles_promotion->article->qta.' '.$prod_gas_articles_promotion->article->um;

        $prod_gas_articles_promotion->importo_originale = ($prod_gas_articles_promotion->qta * $prod_gas_articles_promotion->article->prezzo);

        // debug($prod_gas_articles_promotion);

        return $prod_gas_articles_promotion;
    }

    protected function _getArticleImg1($row) {
        
        // debug($row);
        
        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Article.img.path.full'), $row->organization_id, $row->article->img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $results = '';
        if(!empty($row->article->img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $results = $portalgas_fe_url . $img_path;
        } 
        else
            $results = Configure::read('Article.img.no');
        
        return $results; 
    }

	function name() {
		return $this->results;
	}
}