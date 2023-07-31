<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class CartDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $carts)
    {
    	$results = [];
	    // debug($carts);

	    if($carts instanceof \Cake\ORM\ResultSet) {
			foreach($carts as $numResult => $cart) {
				$results[$numResult] = $this->_decorate($user, $cart);
			}
	    }
	    else 
	    if($carts instanceof \App\Model\Entity\Cart) {
			$results = $this->_decorate($user, $carts);  	
	    }
        else {
            foreach($carts as $numResult => $cart) {
                $results[$numResult] = $this->_decorate($user, $cart);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $cart) {

        // debug($cart);
        $results = $cart->toArray();

        $final_qta = 0;
        $final_price = 0;

        ($cart->qta_forzato > 0 ) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;
        $results['final_qta'] = $final_qta;

        if($cart->qta_forzato > 0) {
            $results['is_qta_mod'] = true;
        }
        else {
            $results['is_qta_mod'] = false;
        }
        if($cart->importo_forzato > 0) {
            $results['final_price'] = $cart->importo_forzato;
            $results['is_import_mod'] = true;
        }
        else {
            $results['final_price'] = ($final_qta * $cart->articles_order->prezzo);
            $results['is_import_mod'] = false;
        } 

        // https://api.cakephp.org/3.8/class-Cake.View.Helper.TimeHelper.html
        // eeee d MMMM Y = mercoledì 8 febbraio 2023
        $results['date_human'] = $cart->date->i18nFormat('eeee d MMMM Y');

        switch($cart->stato) {
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

        return $results;
    }

	function name() {
		return $this->results;
	}
}