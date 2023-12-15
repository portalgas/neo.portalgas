<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$_portalgas_app_root = $config['Portalgas.App.root'];
$_portalgas_fe_url = $config['Portalgas.fe.url'];        

/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);
$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery);
$delivery_data = $this->HtmlCustomSite->drawDeliveryDateLabel($delivery);

$html = '';
$html .= '<h3>'.__('Delivery').' '.$delivery_label.' '.$delivery_data.'</h3>';

if(!empty($results)) {

	foreach($results as $result) {

		$html .= '<table cellpadding="0" cellspacing="0" border="1" width="100%" class="table">';
		$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
		$html .= '<tr>';
		$html .= '	<th scope="col" colspan="3">' . __('SupplierOrganization') . '</th>';
		if($format=='HTML')
			$html .= '<th scope="col" class="text-center"></th>';
		$html .= '	<th scope="col" class="text-center">' . __('Total carts') . '</th>';
		$html .= '	<th scope="col" class="text-center">' . __('Trasport') . '</th>';
		$html .= '	<th scope="col" class="text-center">' . __('CostMore') . '</th>';
		$html .= '	<th scope="col" class="text-center">' . __('CostLess') . '</th>';
		$html .= '	<th scope="col" class="text-center">' . __('Importo totale ordine') . '</th>';
		$html .= '</tr>';
		$html .= '</thead><tbody>';

		$html .= '<tr>';
		$html .= '	<td colspan="3">'.$result['suppliers_organization']->name.'</td>';
		if($format=='HTML') {
			$html .= '<td>';
			if(!empty($result['suppliers_organization']->supplier->img1)) {
			
			
				$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
				$img_path_supplier = $_portalgas_app_root . $img_path_supplier;
				
				$url = '';
				if(file_exists($img_path_supplier)) {
					$url = sprintf($_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
					$html .= '<img src="'.$url.'" width="'.Configure::read('Supplier.img.preview.width').'" />';
				}
			}
			$html .= '</td>';
		} 
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order_only_cart']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['trasport']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_more']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_less']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order']).'</td>';
		$html .= '</tr>';
				
		$html .= '<tr class="tr-header">';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td class="text-center">'.__('Quantità').'</td>';
		$html .= '	<td class="text-center">'.__('Importo').'</td>';
		$html .= '	<td class="text-center" colspan="2">'.__('Quantità e importo totali').'</td>';
		$html .= '	<td></td>';
		$html .= '</tr>';	
		$html .= '<tr class="tr-header">';	
		$html .= '	<td>'.__('Name').'</td>';
		$html .= '	<td class="text-center">'.__('Prezzo unità').'</td>';
		$html .= '	<td class="text-center">'.__('Quantità').'</td>';
		$html .= '	<td class="text-center">'.__('Importo').'</td>';
		$html .= '	<td class="text-center" colspan="2">dell\'utente</td>';
		$html .= '	<td class="text-center" colspan="2">modificati dal referente</td>';
		$html .= '	<td class="text-center">'.__('Importo forzati').'</td>';
		$html .= '</tr>';
		$user_id_old = 0;
		foreach($result['order']['carts'] as $cart) {
		
			$final_price = $this->HtmlCustomSite->getCartFinalPrice($cart);
			($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;

			if($cart->user->id!=$user_id_old) {
				$html .= '<tr>';
				$html .= '	<td colspan="9"><b>'.__('User').': '.$cart->user->name.'</b></td>';
				$html .= '</tr>';	
			}

			$html .= '<tr>';
			$html .= '	<td>'.$cart->articles_order->name.'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($cart->articles_order->prezzo).'</td>';
			$html .= '	<td class="text-center">'.$final_qta.'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($final_price).'</td>';
			$html .= '	<td class="text-center">';
			if($cart->qta_forzato>0)
				$html .= $cart->qta_forzato;
			else 
				$html .= '-';
			$html .= '</td>';
			$html .= '	<td class="text-center">';
			if($cart->qta_forzato>0)
				$html .= $this->HtmlCustom->importo($cart->qta_forzato * $cart->articles_order->prezzo);
			else 
				$html .= '-';
			$html .= '</td>';
			$html .= '	<td class="text-center">';
			if($cart->importo_forzato>0)
				$html .= $this->HtmlCustom->importo($cart->importo_forzato);
			else 
				$html .= '-';
			$html .= '</td>';
			$html .= '</tr>';

			$user_id_old = $cart->user->id;
		}
	
		$html .= '	<tr>';
		$html .= '		<td colspan="';
		($format=='HTML')? $html .= '6' : $html .= '5';
		$html .= '" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right no-border">' . __('Total delivery') . '</th>';
		$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_importo). '</th>';
		$html .= '	</tr>';

		$html .= '	</tbody>';
		$html .= '	</table>';	

	} // end foreach($article_orders as $numResult => $article_order)		
}
echo $html;
?>
<style>
.tr-header {
	background-color: #f5f2f2;
	font-weight: bold;
}
.tr-header td {
	
}
</style>