<?php
use Cake\Core\Configure;

/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);

$html = '';
if(!empty($order)) {

	$html .= '<h3>Consegna dell\'ordine '.$order->suppliers_organization->name.' di '.$user->username.'</h3>';

	if(empty($results))	 {
		$html .= '<div class="alert alert-warning">Per quest\'ordine non sono stati effettuati acquisti</div>';
	}
	else {
	
		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
		$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
		$html .= '	<tr>';
		$html .= '			<th width="5%">' . __('Bio') . '</th>';
		$html .= '			<th width="30%" class="text-left">' . __('Name') . '</th>';
		$html .= '			<th width="10%">' . __('Conf') . '</th>';
		$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
		$html .= '			<th width="20%">&nbsp;' . __('PrezzoUnita') . '</th>';
		if(!empty($order->summary_order_trasport)) {
			$html .= '			<th width="5%">&nbsp;</th>';
			$html .= '			<th width="10%">' . __('Importo') . '</th>';
		}
		else {
			$html .= '			<th width="5%">&nbsp;</th>';
			$html .= '			<th width="10%"></th>';
		}
		$html .= '	</tr>';
		$html .= '	</thead><tbody>';

		$totale_ordine = 0;
		foreach($results as $article_order) {
			
			// debug($article_order);

			$article_order['is_bio'] ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';

			if($result->isOpenToPurchasable)   /* aperto per acquistare */
				$totale_ordine += ($article_order['cart']['qta_new'] * $article_order['price']);
			else {
				/* ordine chiuso agli acquisti */
				$totale_ordine += $article_order['cart']['final_price'];
			}

			$html .= '<tr>';
			$html .= '	<td class="text-center">'.$is_bio.'</td>';
			$html .= '	<td>'.$article_order['name'].'</td>';
			$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
			$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
			$html .= '	<td class="text-center">';
			if(isset($article_order['cart']['qta']))
				$html .= $article_order['cart_split']['final_qta'];
			else
				$html .= $article_order['cart']['final_qta'];
			if($article_order['cart']['is_qta_mod'])
				$html .= '<span>*</span>';
			$html .= '  </td>';
			$html .= '	<td class="text-center">';
			$html .= $this->HtmlCustom->importo($article_order['cart']['final_price']);
			if($article_order['cart']['is_import_mod'])
				$html .= '<span>*</span>';
			$html .= '  </td>';
			$html .= '</tr>';

			if(!empty($article_order['cart_splits'])) {

				$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
				$html .= '	<tr>';
				$html .= '			<th width="5%"></th>';
				$html .= '			<th width="30%"></th>';
				$html .= '			<th width="20%" class="text-left"></th>';
				$html .= '			<th width="20%" class="text-left">'.__('CartSplit').'</th>';
				$html .= '			<th width="5%">' . __('Qta') . '</th>';
				$html .= '			<th width="10%">' . __('Importo') . '</th>';
				if(!empty($order->summary_order_trasport)) {
					$html .= '			<th width="10%">' . __('CostTrasport') . '</th>';
				}
				else {
					$html .= '			<th width="10%"></th>';
				}
				$html .= '	</tr>';
				$html .= '	</thead><tbody>';

				foreach($article_order['cart_splits'] as $cart_split) {

					$html .= '<tr>';
					$html .= '	<td></td>';
					$html .= '	<td></td>';
					$html .= '	<td></td>';
					$html .= '	<td>'.$cart_split['name'].'</td>';
					$html .= '	<td class="text-center">'.$cart_split['qta'].'</td>';
					$html .= '	<td class="text-center">'.$this->HtmlCustom->importo(($cart_split['qta'] + $article_order['price'])).'</td>';
					if(!empty($order->summary_order_trasport))
						$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order->summary_order_trasport->importo_trasport).'</td>';
					else				
						$html .= '	<td></td>';
					$html .= '</tr>';
				} // end foreach($article_order['cart_splits'] as $cart_split)
			}
			else {

			}

		} // end foreach($results->article_orders as $article_order) 

		/*
		* totale ordine
		*/ 
		if(!empty($order->summary_order_trasport)) {
			$html .= '	<tr>';
			$html .= '		<td colspan="4" class="no-border"></td>';
			$html .= '		<th colspan="2" class="text-right no-border">' . __('CostTrasport') . '</th>';
			$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($order->summary_order_trasport->importo_trasport). '</th>';
			$html .= '	</tr>'; 

			$totale_ordine += $order->summary_order_trasport->importo_trasport; 	
		}

		if(!empty($order->summary_order_cost_more)) {
			$html .= '	<tr>';
			$html .= '		<td colspan="4" class="no-border"></td>';
			$html .= '		<th colspan="2" class="text-right no-border">' . __('CostMore') . '</th>';
			$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($order->summary_order_cost_more->importo_cost_more). '</th>';
			$html .= '	</tr>'; 

			$totale_ordine += $order->summary_order_cost_more->importo_cost_more;		
		}

		if(!empty($order->summary_order_cost_less)) {
			$html .= '	<tr>';
			$html .= '		<td colspan="4" class="no-border"></td>';
			$html .= '		<th colspan="2" class="text-right no-border">' . __('CostLess') . '</th>';
			$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($order->summary_order_cost_less->importo_cost_less). '</th>';
			$html .= '	</tr>';

			$totale_ordine += $order->summary_order_cost_less->importo_cost_less;
		}

		$html .= '	<tr>';
		$html .= '		<td colspan="4" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right no-border">' . __('Totale ordine') . '</th>';
		$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($totale_ordine). '</th>';
		$html .= '	</tr>';

		$totale_consegna += $totale_ordine; 

		$html .= '	</tbody>';
		$html .= '	</table>';	

		/*
		* R E F E R E N T I 
		*/ 
		if(isset($order->referents)) {
			$options = ['pdf_img_path' => $img_path, 'br' => true];
			$html .= $this->HtmlCustomSite->boxOrizontalSupplierOrganizationreferents($order->referents, $options);
		} 

		/*
		* totale consegna
		*/
		$label = __('Totale consegna').' '.$this->HtmlCustom->importo($totale_consegna);
		switch ($user->organization->template->payToDelivery) {
			case 'POST':
				$label = sprintf(__('TotaleConfirmTesoriere'), $this->HtmlCustom->importo($totale_consegna));
			break;
			case 'ON':
			case 'ON-POST':
				$label = sprintf(__('TotaleConfirmCassiere'), $this->HtmlCustom->importo($totale_consegna));
			break;	
		}
		$html .= '<div class="box-totali">';
		$html .= $label;
		$html .= '</div>';

		$html .= '<div class="box-legenda">';
		$html .= 'Legenda: (*) Valore modificato dal referente';
		$html .= '</div>';

	} // end if(empty($results))
} // end if(!empty($order))

echo $html;
?>