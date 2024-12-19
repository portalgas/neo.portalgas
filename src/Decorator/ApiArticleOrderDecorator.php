<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiCartDecorator;

class ApiArticleOrderDecorator  extends AppDecorator {

	public $serializableAttributes = null; // ['id', 'name'];
	public $results;

    public function __construct($user, $articles_orders, $order=null)
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
        $results = [];

        /*
         * setto tag con gli id
         */
        $results = $articles_order->toArray();

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
        if(!empty($order)) $results['isOpenToPurchasable'] = $lifeCycleOrdersTable->isOpenToPurchasable($user, $order->state_code);
        if(!empty($order)) $results['type_draw'] = $order->type_draw; // ENUM('SIMPLE', 'COMPLETE', 'PROMOTION')

        $results['has_variants'] = false; // e' sempre articolo e la sua variante

        if(empty($articles_order->codice)) {
            $results['sku'] = '';
        }
        else {
            $results['sku'] = $articles_order->article->codice;
        }

        $results['img1'] = $this->_getArticleImg1($articles_order);
        $results['img1_width'] = Configure::read('Article.img.preview.width');

        /*
        if(empty($articles_order->article->slug))
            $results['slug'] = '';
        else
            $results['slug'] = $articles_order->article->slug.'-'.$results['sku'];
        */

        $results['is_select'] = false; // per vue js x gestire eventuali checkbox

        if(empty($articles_order->prezzo)) {
            $results['price'] = 0;
            $results['prezzo'] = 0;
            $results['prezzo_'] = '0,00';
            $results['prezzo_e'] = $results['prezzo_'].' &euro';
        }
        else {
            $results['price'] = $articles_order->prezzo;
            $results['prezzo'] = $articles_order->prezzo;
            $results['prezzo_'] = number_format($articles_order->prezzo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
            $results['prezzo_e'] = $results['prezzo_'].' &euro';
        }

        if(empty($articles_order->pezzi_confezione)) {
            $results['pezzi_confezione'] = 1;
            $results['package'] = 1;
        }
        else {
            $results['pezzi_confezione'] = $articles_order->pezzi_confezione;
            $results['package'] = $articles_order->pezzi_confezione;
        }

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

        switch($articles_order->stato) {
            case 'Y':
                $results['stato_human'] = 'Articolo può essere acquistato';
            break;
            case 'N':
                $results['stato_human'] = 'Articolo può essere acquistato';
            break;
            case 'LOCK':
                $results['stato_human'] = 'Articolo non più acquistabile perchè è bloccato';
            break;
            case 'QTAMAXORDER':
                $results['stato_human'] = 'Articolo non più acquistabile perchè raggiunta la quantità massima';
            break;
        }

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

        /*
         * tutti gli acquisti del gasista sull'articolo
         * */
        if(isset($articles_order->cart) && !empty($articles_order->cart->user_id)) {

            $cartResults = new ApiCartDecorator($user, $articles_order->cart, $articles_order);
            $results['cart'] = $cartResults->results;
        }

        /*
         * tutti gli acquisti dei gasisti sull'articolo
         * */
        if(isset($articles_order->carts)) {
            $cartResults = new ApiCartDecorator($user, $articles_order->carts, $articles_order);
            $results['carts'] = $cartResults->results;
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
