<?php
/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);

$html = '';

$totale_consegna = 0;
foreach($results as $result) {
	$html .= '<h2>Produttore '.$result->suppliers_organization->name.'</h2>';
	if(!empty($result->suppliers_organization->supplier->descrizione))
		$html .= '<h3>'.$result->suppliers_organization->supplier->descrizione.'</h3>';

	$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
	$html .= '	<tr>';
	$html .= '			<th width="5%">' . __('Bio') . '</th>';
	$html .= '			<th width="30%" class="text-left">' . __('Name') . '</th>';
	$html .= '			<th width="10%">' . __('Conf') . '</th>';
	$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
	$html .= '			<th width="20%">&nbsp;' . __('PrezzoUnita') . '</th>';
	$html .= '			<th width="5%">' . __('Qta') . '</th>';
	$html .= '			<th width="10%">' . __('Importo') . '</th>';
	$html .= '	</tr>';
	$html .= '	</thead><tbody>';

	$totale_ordine = 0;
	foreach($result->article_orders as $article_order) {
		
		// debug($article_order);

		$article_order['is_bio'] ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';

		if($result->isOpenToPurchasable) 
            $totale_ordine += $article_order['cart']['final_price'];
        else {
              /* ordine chiuso agli acquisti */
              $totale_ordine += ($article_order['cart']['qta_new'] * $article_order['price']);
        }
		
		$html .= '<tr>';
		$html .= '	<td class="text-center">'.$is_bio.'</td>';
		$html .= '	<td>'.$article_order['name'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
		$html .= '	<td class="text-center">'.$article_order['cart']['final_qta'].'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['cart']['final_price']).'</td>';
		$html .= '</tr>';

	}

	/*
	 * totale ordine
	 */ 
    if(!empty($result->summary_order_trasport)) {
		$html .= '	<tr>';
		$html .= '		<td colspan="4" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right" class="no-border">' . __('CostTrasport') . '</th>';
		$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($result->summary_order_trasport->importo_trasport). '</th>';
		$html .= '	</tr>'; 

		$totale_ordine += $result->summary_order_trasport->importo_trasport; 	
    }

    if(!empty($result->summary_order_cost_more)) {
		$html .= '	<tr>';
		$html .= '		<td colspan="4" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right" class="no-border">' . __('CostMore') . '</th>';
		$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($result->summary_order_cost_more->importo_cost_more). '</th>';
		$html .= '	</tr>'; 

		$totale_ordine += $result->summary_order_trasport->importo_cost_more;		
    }

    if(!empty($result->summary_order_cost_less)) {
		$html .= '	<tr>';
		$html .= '		<td colspan="4" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right" class="no-border">' . __('CostLess') . '</th>';
		$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($result->summary_order_cost_less->importo_cost_less). '</th>';
		$html .= '	</tr>';

		$totale_ordine += $result->summary_order_trasport->importo_cost_less;
    }

	$html .= '	<tr>';
	$html .= '		<td colspan="4" class="no-border"></td>';
	$html .= '		<th colspan="2" class="text-right" class="no-border">' . __('Totale ordine') . '</th>';
	$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($totale_ordine). '</th>';
	$html .= '	</tr>';

	$totale_consegna += $totale_ordine; 

	$html .= '	</tbody>';
	$html .= '	</table>';	

    /*
     * R E F E R E N T I 
     */ 
    if(isset($result->suppliers_organization->suppliers_organizations_referents)) {
    	$html .= '<div class="box-referents">';
		foreach ($result->suppliers_organization->suppliers_organizations_referents as $referent) {
		    $html .= '<div class="referent">';
		    
		    if($referent->type!='REFERENTE')
			    $html .= '('.strtolower($referent->type).') ';
		    $html .= $referent->user->name.' '.$referent->user->email;	
		    // debug($referent->user->user_profiles);
		    foreach ($referent->user->user_profiles as $user_profile) {
		    	if($user_profile->profile_key=='profile.phone' && $user_profile->profile_value!='')
	                $html .= ' - '.$user_profile->profile_value.' - '; 
		    	if($user_profile->profile_key=='profile.satispay' && $user_profile->profile_value=='Y')
	                $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />'; 
		    	if($user_profile->profile_key=='profile.satispay_phone' && $user_profile->profile_value=='Y')
	                $html .= ' - '.$user_profile->profile_value.' - '; 
		    }
		    $html .= '</div>';
		}
		$html .= '</div>';
	} 

} // loop orders

/*
 * totale consegna
 */
$html .= '<div class="box-totali">';
$html .= __('Totale consegna').' '.$this->HtmlCustom->importo($totale_consegna);
$html .= '</div>';

/*
 * storerooms
 */
// debug($storeroomResults);
if ($user->organization->paramsConfig['hasStoreroom'] == 'Y' && $user->organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {

	$totale_dispensa = 0;
	if(!empty(($storeroomResults))) {
		$html .= '<h2>'.__('Storeroom').'</h2>';

		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
		$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
		$html .= '	<tr>';
		$html .= '		<th width="5%">' . __('Bio') . '</th>';
		$html .= '		<th width="35%" class="text-left">' . __('Name') . '</th>';
		$html .= '		<th width="10%">' . __('Conf') . '</th>';
		$html .= '		<th width="20%">&nbsp;' . __('PrezzoUnita') . '</th>';
		$html .= '		<th width="10%">' . __('Qta') . '</th>';
		$html .= '		<th width="20%">' . __('Importo') . '</th>';
		$html .= '	</tr>';
		$html .= '</thead><tbody>';

		foreach($storeroomResults as $result) {
			
			// debug($result);

			$result->article->bio=='Y' ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';
			$conf = ($result->qta.' '.$result->article->um);
			$importo = ($result->qta * $result->prezzo);
			$totale_dispensa += $importo;

			$html .= '<tr>';
			$html .= '	<td class="text-center">'.$is_bio.'</td>';
			$html .= '	<td>'.$result->name.'</td>';
			$html .= '	<td class="text-center">'.$conf.'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result->prezzo).'</td>';
			$html .= '	<td class="text-center">'.$result->qta.'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($importo).'</td>';
					
			$html .= '</tr>';

		}

		$html .= '	<tr>';
		$html .= '		<td colspan="3" class="no-border"></td>';
		$html .= '		<th colspan="2" class="text-right" class="no-border">' . __('Totale dispensa') . '</th>';
		$html .= '		<th class="no-border">' .$this->HtmlCustom->importo($totale_dispensa). '</th>';
		$html .= '	</tr>';

		$html .= '	</tbody>';
		$html .= '	</table>';	

	} 
}

echo $html;
?>