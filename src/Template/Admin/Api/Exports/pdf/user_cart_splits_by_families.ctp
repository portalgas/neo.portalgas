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

	if(count($results['cart_splits'])==0 && count($results['carts'])==0)	 {
		$html .= '<div class="alert alert-warning">Per quest\'ordine non sono stati effettuati acquisti</div>';
	}
	else {
		$totale_ordine = 0;

		/*
		 * suddivisione
		 * */
		if(count($results['cart_splits'])>0)
		foreach($results['cart_splits'] as $numResult => $result) {
			
			$html .= '<h2>'.$result['name'].'</h2>';
			
			$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
			$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
			$html .= '	<tr>';
			$html .= '			<th width="5%">' . __('Bio') . '</th>';
			$html .= '			<th width="25%" class="text-left">' . __('Name') . '</th>';
			$html .= '			<th width="10%">' . __('Conf') . '</th>';
			$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
			$html .= '			<th width="15%">&nbsp;' . __('PrezzoUnita') . '</th>';
			$html .= '			<th width="10%">' . __('Qta') . '</th>';
			$html .= '			<th width="5%">' . __('Importo') . '</th>';
			if(!empty($order->summary_order_trasport)) {
				$html .= '			<th width="10%">' . __('CostTrasport') . '</th>';
			}
			else {
				$html .= '			<th width="10%"></th>';
			}
			$html .= '	</tr>';
			$html .= '	</thead><tbody>';

			foreach($result['cart_splits'] as $numResult2 => $cart_split) {

				$cart = $cart_split['cart'];
				$article_order = $cart_split['cart']['articles_order'];
				$article = $cart_split['cart']['article'];

				$article_order['is_bio'] ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';

				if($order->isOpenToPurchasable)   /* aperto per acquistare */
					$totale_ordine += ($cart['qta_new'] * $article_order['price']);
				else {
					$totale_ordine += ($cart_split['qta'] * $article_order['price']);
				}

				$html .= '<tr>';
				$html .= '	<td class="text-center">'.$is_bio.'</td>';
				$html .= '	<td>'.$article_order['name'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
				$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
				$html .= '	<td class="text-center">';
				$html .= $cart_split['qta'];
				$html .= '  </td>';
				$html .= '	<td class="text-center">';
				$html .= $this->HtmlCustom->importo(($cart_split['qta'] * $article_order['price']));
				$html .= '  </td>';
				if(!empty($order->summary_order_trasport))
					$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($cart_split['trasport']).'</td>';
				else				
					$html .= '	<td></td>';			
				$html .= '</tr>';
										
			} // end foreach($result['cart_splits'] as $numResult2 => $cart_split)
			$html .= '	</tbody>';
			$html .= '	</table>';
		} // end foreach($results as $numResult => $result)

		/*
		 * NON suddivisi
		 * */
		if(count($results['carts'])>0) {

			$html .= '<h2>Acquisti non suddivisi</h2>';

			$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
			$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
			$html .= '	<tr>';
			$html .= '			<th width="5%">' . __('Bio') . '</th>';
			$html .= '			<th width="30%" class="text-left">' . __('Name') . '</th>';
			$html .= '			<th width="10%">' . __('Conf') . '</th>';
			$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
			$html .= '			<th width="15%">&nbsp;' . __('PrezzoUnita') . '</th>';
			$html .= '			<th width="10%">' . __('Qta') . '</th>';
			$html .= '			<th width="10%">' . __('Importo') . '</th>';
			$html .= '	</tr>';
			$html .= '	</thead><tbody>';

			foreach($results['carts'] as $cart) {
			
				$article_order = $cart['articles_order'];

				$article_order['is_bio'] ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';

				if($order->isOpenToPurchasable)   /* aperto per acquistare */
					$totale_ordine += ($cart['qta_new'] * $article_order['price']);
				else {
					/* ordine chiuso agli acquisti */
					$totale_ordine += $cart['final_price'];
				}

				$html .= '<tr>';
				$html .= '	<td class="text-center">'.$is_bio.'</td>';
				$html .= '	<td>'.$article_order['name'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
				$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
				$html .= '	<td class="text-center">';
				$html .= $cart['final_qta'];
				if($cart['is_qta_mod'])
					$html .= '<span>*</span>';
				$html .= '  </td>';
				$html .= '	<td class="text-center">';
				$final_price = $cart['final_price'];
				$html .= $this->HtmlCustom->importo($final_price);
				if($cart['is_import_mod'])
					$html .= '<span>*</span>';
				$html .= '  </td>';
				$html .= '</tr>';

			}
			$html .= '	</tbody>';
			$html .= '	</table>';
		} // end if(count($results['carts'])>0) 

		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
		$html .= '	<tbody>';
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
		$label = __('Totale ordine').' '.$this->HtmlCustom->importo($totale_ordine);
		switch ($user->organization->template->payToDelivery) {
			case 'POST':
				$label = sprintf(__('TotaleConfirmTesoriere'), $this->HtmlCustom->importo($totale_ordine));
			break;
			case 'ON':
			case 'ON-POST':
				$label = sprintf(__('TotaleConfirmCassiere'), $this->HtmlCustom->importo($totale_ordine));
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