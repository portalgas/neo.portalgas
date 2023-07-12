<?php
use Cake\Core\Configure;

/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);

$html = '';
$html .= '<h3>'.__('Order-'.$order_type_id).'</h3>';

/*
	* ordine titolare
	*/
$html .= '<h3><b>'.__('Supplier').'</b> ';
if(!empty($orderParent->suppliers_organization->supplier->img1))
	$html .= '<img src="'.$orderParent->suppliers_organization->supplier->img1.'" width="'.Configure::read('Supplier.img.preview.width').'" />';
$html .= ' '.$orderParent->suppliers_organization->name;
$html .= ' <small>';
if(!empty($orderParent->suppliers_organization->supplier->address_full))
	$html .= $orderParent->suppliers_organization->supplier->address_full.' ';
if(!empty($orderParent->suppliers_organization->supplier->telefono))
	$html .= $orderParent->suppliers_organization->supplier->telefono.' ';
if(!empty($orderParent->suppliers_organization->supplier->mail))
	$html .= $orderParent->suppliers_organization->supplier->mail.' ';
$html .= '</small>';
if(isset($opts['delivery_order_parent']) && $opts['delivery_order_parent']=='Y')
	$html .= '<h4><b>'.__('Delivery').'</b>: '.$orderParent->delivery->luogo.' '.$orderParent->delivery->data->i18nFormat('eeee d MMMM').'</h4>';	
$html .='</h3>';

if(!empty($orders)) {

	$totale = 0;
	foreach($orders as $order) {
		
		$html .= '<h3><b>'.__('Gas Group').'</b>: '.$order->gas_group->name.'</h3>';
		if(isset($opts['deliveries_orders']) && $opts['deliveries_orders']=='Y')
			$html .= '<h4><b>'.__('Delivery').'</b>: '.$order->delivery->luogo.' '.$order->delivery->data->i18nFormat('eeee d MMMM').'</h4>';

		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">';
		$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
		$html .= '	<tr>';
		$html .= '			<th scope="col" width="5%">' . __('Bio') . '</th>';
		if($format=='HTML')
			$html .= '			<th scope="col" width="15%" class="text-left"></th>';
		$html .= '			<th scope="col" width="5%" class="text-left">' . __('Code') . '</th>';
		$html .= '			<th scope="col" width="20%" class="text-left">' . __('Name') . '</th>';
		$html .= '			<th scope="col" width="10%" class="text-center">' . __('Conf') . '</th>';
		$html .= '			<th scope="col" width="10%" class="text-center">' . __('Prezzo/UM') . '</th>';
		$html .= '			<th scope="col" width="15%" class="text-center">&nbsp;' . __('PrezzoUnita') . '</th>';
		$html .= '			<th scope="col" width="5%" class="text-center">' . __('Qta') . '</th>';
		$html .= '			<th scope="col" width="15%" class="text-center">' . __('Importo') . '</th>';
		$html .= '	</tr>';
		$html .= '	</thead><tbody>';

		$totale_ordine = 0;
		foreach($order->article_orders as $numResult => $article_order) {

			$totale_ordine += $article_order->cart->final_price;
			
			$article_order->article->is_bio ? $is_bio = '<img src="/img/is-bio.png" title="bio" width="20" />': $is_bio = '';

			$html .= '<tr>';
			$html .= '	<td class="text-center">'.$is_bio.'</td>';
			if($format=='HTML')
				$html .= '	<td><img src="'.$article_order->article->img1.'" width="'.Configure::read('Article.img.preview.width').'" /></td>';
			$html .= '	<td>'.$article_order->article->codice.'</td>';
			$html .= '	<td>'.$article_order->name.'</td>';
			$html .= '	<td class="text-center">'.$article_order->article->conf.'</td>';
			$html .= '	<td class="text-center">'.$article_order->article->um_rif_label.'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order->prezzo).'</td>';
			$html .= '	<td class="text-center">';
			$html .= $article_order->cart->final_qta;
			$html .= '  </td>';
			$html .= '	<td class="text-center">';
			$html .= $this->HtmlCustom->importo($article_order->cart->final_price);
			$html .= '  </td>';
			$html .= '</tr>';
		} // end foreach($article_orders as $numResult => $article_order)

		$html .= '	<tr>';
		$html .= '		<td colspan="';
		($format=='HTML')? $html .= '6' : $html .= '5';
		$html .= '" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right no-border">' . __('Totale ordine') . '</th>';
		$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($totale_ordine). '</th>';
		$html .= '	</tr>';

		$html .= '	</tbody>';
		$html .= '	</table>';	

		$totale += $totale_ordine;

	} // end foreach($orders as $order) 

	$html .= '<h3 class="text-center">'.__('Totale').' '.$this->HtmlCustom->importo($totale). '</h3>';
}
echo $html;
?>