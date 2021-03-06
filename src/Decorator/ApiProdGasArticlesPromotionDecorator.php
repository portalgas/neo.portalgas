<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiProdGasArticlesPromotionDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $articles_orders, $promotion=null)
    {
        $results = [];
	    // var_dump($articles_orders);

	    if($articles_orders instanceof \Cake\ORM\ResultSet) {

            foreach($articles_orders as $numResult => $articles_order) {
                if($articles_order instanceof \App\Model\Entity\ProdGasArticlesPromotion) {  // devo ancora creare l'ordine            
                    $results[$numResult] = $this->_decorateProdGasArticlesPromotion($user, $articles_order, $promotion);
                }
                else
                if($articles_order instanceof \App\Model\Entity\ArticlesOrder) { // gia' creato l'ordine 
        			$results[$numResult] = $this->_decorate($user, $articles_order, $promotion);
                }
            }
	    }
        else         
        if($articles_orders instanceof \App\Model\Entity\ProdGasArticlesPromotion) {  // devo ancora creare l'ordine
            $results = $this->_decorateProdGasArticlesPromotion($user, $articles_orders, $promotion);   
        }
        else 
        if($articles_orders instanceof \App\Model\Entity\ArticlesOrder) { // gia' creato l'ordine
            $results = $this->_decorate($user, $articles_orders, $promotion);   
        }
        else {
            foreach($articles_orders as $numResult => $articles_order) {
                $results[$numResult] = $this->_decorate($user, $articles_order, $promotion);
            }
        }

		$this->results = $results;
    }

    /*
     * devo ancora creare l'ordine
     */
    private function _decorateProdGasArticlesPromotion($user, $prod_gas_article_promotion, $promotion) {

        // debug($prod_gas_article_promotion);

        $results = [];
             

        $results['name'] = $prod_gas_article_promotion->article->name;
            
        if(empty($prod_gas_article_promotion->article->codice)) {
            $results['sku'] = '';
            $results['codice'] = '';
        }
        else {
            $results['sku'] = $prod_gas_article_promotion->article->codice;
            $results['codice'] = $prod_gas_article_promotion->article->codice;
        }

        $results['img1'] = $this->_getArticleImg1($prod_gas_article_promotion);        
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        if(empty($prod_gas_article_promotion->prezzo_unita)) {
            $results['prezzo_unita'] = 0;
            $results['price'] = 0;
        }
        else {
            $results['prezzo_unita'] = $prod_gas_article_promotion->prezzo_unita;
            $results['price'] = $prod_gas_article_promotion->prezzo_unita;
        }

        if(empty($prod_gas_article_promotion->article->pezzi_confezione))
            $results['package'] = 1;
        else
            $results['package'] = $prod_gas_article_promotion->article->pezzi_confezione;

        if(empty($prod_gas_article_promotion->article->qta_minima))
            $results['qta_minima'] = 1;
        else
            $results['qta_minima'] = $prod_gas_article_promotion->article->qta_minima;

        if(empty($prod_gas_article_promotion->article->qta_massima))
            $results['qta_massima'] = 0;
        else
            $results['qta_massima'] = $prod_gas_article_promotion->article->qta_massima;

        if(empty($prod_gas_article_promotion->article->qta_multipli))
            $results['qta_multipli'] = 1;
        else
            $results['qta_multipli'] = $prod_gas_article_promotion->article->qta_multipli;

        $results['qta_minima_order'] = $prod_gas_article_promotion->article->qta_minima_order;
        $results['qta_massima_order'] = $prod_gas_article_promotion->article->qta_massima_order;
        $results['qta_massima_order'] = $prod_gas_article_promotion->article->qta_massima_order;
        
        /*
         * dati da article
         */
        $results['article']['stato'] = $prod_gas_article_promotion->article->stato;
        $results['qta'] = $prod_gas_article_promotion->article->qta; 
        if(empty($prod_gas_article_promotion->article->bio))
            $results['is_bio'] = '';
        else {
            if($prod_gas_article_promotion->article->bio=='Y')
                $results['is_bio'] = true;
            else
                $results['is_bio'] = false;
        }

        if(empty($prod_gas_article_promotion->article->nota)) {
            $results['nota'] = '';
            $results['descri'] = '';
        }
        else {
            $results['nota'] = $prod_gas_article_promotion->article->nota;
            $results['descri'] = $prod_gas_article_promotion->article->nota;
        }

        if(empty($prod_gas_article_promotion->article->ingredienti))
            $results['ingredients'] = '';
        else
            $results['ingredients'] = $prod_gas_article_promotion->article->ingredienti;

        $results['um'] = $prod_gas_article_promotion->article->um;   
        $results['um_rif'] = $prod_gas_article_promotion->article->um_riferimento;   

        /*
         * price e' il prezzo scontato
         */
        $results['um_rif_label'] = $this->_getArticlePrezzoUM($results['price'], $results['qta'], $results['um'], $results['um_rif']);          
        $results['conf'] = $results['qta'].' '.$results['um'];

        /*
         * promotions
         */
        $results['promotion'] = $promotion;

        // sono gia' valorizzati quando si associano gli articoli all'ordine
        $results['qta'] = $prod_gas_article_promotion->qta;
        $results['qta_minima_order'] = $prod_gas_article_promotion->qta;
        $results['qta_massima_order'] = $prod_gas_article_promotion->qta;

        $results['price_pre_discount'] = $prod_gas_article_promotion->article->prezzo;

        /*
         * totale importo qta * prezzo
         */
        $results['importo_originale'] = ($prod_gas_article_promotion->qta * $prod_gas_article_promotion->article->prezzo);
        $results['importo_scontato'] = $prod_gas_article_promotion->importo;

        // debug($results);

        return $results;
    }

    /*
     * gia' creato l'ordine
     */
	private function _decorate($user, $articles_order, $promotion) {

        // debug($articles_order);

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
        $results['isOpenToPurchasable'] = $lifeCycleOrdersTable->isOpenToPurchasable($user, $articles_order->order->state_code);
        $results['type_draw'] = $articles_order->order->type_draw; // ENUM('SIMPLE', 'COMPLETE', 'PROMOTION')

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

        /*
         * price e' il prezzo scontato
         */
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

        // debug($results);

        return $results;
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