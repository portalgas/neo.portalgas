<?php
/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);

$html = '';
$html .= '<h3>Consegna</h3>';

$_headers = [];
$_article_orders = [];
$_users = [];
if(!empty($orders)) {
	foreach($orders as $numResult => $order) {
		// debug($order);
		// debug('delivery '.$order->delivery->luogo.' suppliers_organization '.$order->suppliers_organization->name.' gas_group '.$order->gas_group->name);
		$_headers[$numResult]->delivery->luogo = $order->delivery->luogo;
		$_headers[$numResult]->suppliers_organization->name = $order->suppliers_organization->name;
		$_headers[$numResult]->gas_group->name = $order->gas_group->name;

		foreach($order->article_orders as $article_order) {
			
			// debug($article_order->name.' '.$article_order->article->img1);
			
			$_article_orders[$numResult][$article_order->article_id]->article_id = $article_order->article_id;
			$_article_orders[$numResult][$article_order->article_id]->name = $article_order->name;
			$_article_orders[$numResult][$article_order->article_id]->prezzo = $article_order->prezzo;
			$_article_orders[$numResult][$article_order->article_id]->qta_cart = $article_order->qta_cart;
			$_article_orders[$numResult][$article_order->article_id]->article->img1 = $article_order->article->img1;

			foreach($article_order->carts as $cart) {

				if(!isset($_users[$cart->user_id]->article))
					$i=0;
				else 
					$i = count($_users[$cart->user_id]->article);

				// $_users[$cart->user_id]->article[$i] = $_article_orders[$article_order->article_id];
				$_users[$numResult][$cart->user_id]->article[$i]->name = $article_order->name;
				$_users[$numResult][$cart->user_id]->article[$i]->prezzo = $article_order->prezzo;
				$_users[$numResult][$cart->user_id]->article[$i]->qta_cart = $article_order->qta_cart;
				$_users[$numResult][$cart->user_id]->article[$i]->article->img1 = $article_order->article->img1;

				$_users[$numResult][$cart->user_id]->article[$i]->cart->qta = $cart->qta;
				$_users[$numResult][$cart->user_id]->article[$i]->cart->qta_forzato = $cart->qta_forzato;
				$_users[$numResult][$cart->user_id]->article[$i]->cart->importo_forzato = $cart->importo_forzato;
				$_users[$numResult][$cart->user_id]->article[$i]->cart->user_id = $cart->user_id;

				$_users[$numResult][$cart->user_id]->user->id = $cart->user->id;
				$_users[$numResult][$cart->user_id]->user->name = $cart->user->name;
				$_users[$numResult][$cart->user_id]->user->email = $cart->user->email;

				// debug($i.') '.$article_order->name.' cart '.$cart->qta.' user '.$cart->user->email.' '.$cart->user_id);
			}			
		}
	}
}
debug($_headers);
debug($_users);
debug($_article_orders);
echo $html;
?>