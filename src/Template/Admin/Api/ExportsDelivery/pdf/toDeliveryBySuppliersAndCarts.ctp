<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$_portalgas_app_root = $config['Portalgas.App.root'];
$_portalgas_fe_url = $config['Portalgas.fe.url'];        

// debug($opts);

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
$html .= '<div class="totale">';
$html .= __('Total delivery').' ';
$html .= $this->HtmlCustom->importo($delivery_tot_importo);
$html .= '</div>';

if(!empty($results)) {

	foreach($results as $numResult => $result) {

		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-borderless">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .= '	<td><h2>'.$result['suppliers_organization']->name.'</h2></td>';
		$html .= '  <td>';
		if($format=='HTML') {
			if(!empty($result['suppliers_organization']->supplier->img1)) {
			
				$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
				$img_path_supplier = $_portalgas_app_root . $img_path_supplier;
				
				$url = '';
				if(file_exists($img_path_supplier)) {
					$url = sprintf($_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
					$html .= '<img src="'.$url.'" width="'.Configure::read('Supplier.img.preview.width').'" />';
				}
			}
		} 
		$html .= '</td>';
		$html .= '</tr>';		
		$html .= '<tr>';
		$html .= '	<td class="text-right">' . __('Total Carts') . '</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order_only_cart'], '-').'</td>';
		$html .= '</tr>';		
		if($result['order']['trasport']>0) {
			$html .= '<tr>';
			$html .= '	<td class="text-right">' . __('Trasport') . '</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['trasport'], '-').'</td>';
			$html .= '</tr>';
		}
		if($result['order']['cost_more']>0) {
			$html .= '<tr>';
			$html .= '	<td class="text-right">' . __('CostMore') . '</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_more'], '-').'</td>';
			$html .= '</tr>';
		}
		if($result['order']['cost_less']>0) {
			$html .= '<tr>';
			$html .= '	<td class="text-right">' . __('CostLess') . '</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_less'], '-').'</td>';
			$html .= '</tr>';	
		}
		if($result['order']['trasport']>0 || $result['order']['cost_more']>0 || $result['order']['cost_less']>0) {
			$html .= '<tr>';
			$html .= '	<td class="text-right">' . __('Importo totale ordine') . '</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order'], '-').'</td>';
			$html .= '</tr>';	
		}
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '<br />';
			
		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-hover">';
		$html .= '<tr class="tr-header">';
		$html .= '	<td></td>';
		if($format=='HTML')
			$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		$html .= '	<td></td>';
		if($opts['referent_modify']=='Y') {
			$html .= '	<td class="text-center">'.__('Quantità').'</td>';
			$html .= '	<td class="text-center">'.__('Importo').'</td>';
			$html .= '	<td class="text-center" colspan="2">'.__('Quantità e importo totali').'</td>';
			$html .= '	<td></td>';	
		}
		$html .= '</tr>';	
		$html .= '<tr class="tr-header">';	
		$html .= '	<td colspan="3">'.__('Name').'</td>';
		if($format=='HTML')
			$html .= '	<td></td>';
		$html .= '	<td class="text-center">'.__('Prezzo unità').'</td>';
		$html .= '	<td class="text-center">'.__('Quantità').'</td>';
		$html .= '	<td class="text-right">'.__('Importo').'&nbsp;&nbsp;&nbsp;</td>';
		if($opts['referent_modify']=='Y') {
			$html .= '	<td class="text-center" colspan="2">dell\'utente</td>';
			$html .= '	<td class="text-center" colspan="2">modificati dal referente</td>';
			$html .= '	<td class="text-center">'.__('Importo forzati').'</td>';
		}
		$html .= '</tr>';
		$user_id_old = 0;
		foreach($result['order']['carts'] as $cart) {
		
			$final_price = $this->HtmlCustomSite->getCartFinalPrice($cart);
			($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;

			// header user
			if($cart->user_id!=$user_id_old) {

				if($user_id_old>0) {
					// totale gasista
					$html .= $this->HtmlCustomSiteExport->toDeliveryBySuppliersAndCartsDrawUserTotale($result['order']['users'][$user_id_old], $format, $opts);
				}

				$html .= '<tr>';
				$html .= '<td colspan="6"><b>'.__('User').': '.$cart->user->name.'</b></td>';
				if($format=='HTML')
					$html .= '<td></td>';
				if($opts['referent_modify']=='Y') 
					$html .= '<td colspan="5" class="evidence"></td>';
				$html .= '</tr>';	
			}

			$html .= '<tr>';
			$html .= '	<td colspan="3">'.$cart->articles_order->name.'</td>';
			if($format=='HTML')
				$html .= '<td></td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($cart->articles_order->prezzo).'</td>';
			$html .= '	<td class="text-center">'.$final_qta.'</td>';
			$html .= '	<td class="text-right">'.$this->HtmlCustom->importo($final_price).'&nbsp;&nbsp;&nbsp;</td>';

			if($opts['referent_modify']=='Y') {
				/*
				* qta originali, quelli del gasista
				*/
				$html .= '	<td class="text-center evidence">';
				$html .= $cart->qta;
				$html .= '  </td>';
				$html .= '	<td class="text-center evidence">';
				$html .= $this->HtmlCustom->importo($cart->qta * $cart->articles_order->prezzo);
				$html .= '</td>';
				/*
				* qta modificata dal referente
				*/
				$html .= '	<td class="text-center evidence">';
				if($cart->qta_forzato>0)
					$html .= $cart->qta_forzato;
				else 
					$html .= '-';
				$html .= '</td>';
				$html .= '	<td class="text-center evidence">';
				if($cart->qta_forzato>0)
					$html .= $this->HtmlCustom->importo($cart->qta_forzato * $cart->articles_order->prezzo);
				else 
					$html .= '-';
				$html .= '</td>';
				/* 
				* importo modificata dal referente
				*/
				$html .= '	<td class="text-center evidence">';
				if($cart->importo_forzato>0)
					$html .= $this->HtmlCustom->importo($cart->importo_forzato);
				else 
					$html .= '-';
				$html .= '</td>';
			}
			$html .= '</tr>';

			if($opts['cart_nota']=='Y')
			if(!empty($cart->nota)) {
				$html .= '<tr>';
				$html .= '	<td></td>';
				if($format=='HTML')
					$html .= '	<td></td>';
				$html .= '	<td colspan="5"><b>Nota:</b> '.$cart->nota.'</td>';
				if($opts['referent_modify']=='Y') {
					$html .= '	<td></td>';
					$html .= '	<td></td>';
					$html .= '	<td></td>';
					$html .= '	<td></td>';	
					$html .= '	<td></td>';	
				}
				$html .= '</tr>';
			}

			$user_id_old = $cart->user_id;
		} // foreach($result['order']['carts'] as $cart)
		// totale gasista
		$html .= $this->HtmlCustomSiteExport->toDeliveryBySuppliersAndCartsDrawUserTotale($result['order']['users'][$user_id_old], $format ,$opts);

		$html .= '	</tbody>';
		$html .= '	</table>';	

		// salto pagina tranne all'ultima pagina
		if($opts['salto_pagina']=='Y' && $numResult<(count($results)-1)) 
			$html .= '<div style="page-break-after: always;"></div>';

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
td.evidence {
	background-color:#F5F5F5;
}
.totale {
	font-size: 20px;
	text-align: center;
	padding: 15px;
	background-color:#F5F5F5;
}
.table-borderless > tbody > tr > td,
.table-borderless > tbody > tr > th,
.table-borderless > tfoot > tr > td,
.table-borderless > tfoot > tr > th,
.table-borderless > thead > tr > td,
.table-borderless > thead > tr > th {
    border: none;
}
</style>