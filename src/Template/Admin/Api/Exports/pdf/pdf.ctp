<?php
// debug($results);
$html = '';

foreach($results as $result) {
	$html .= '<h2>Produttore '.$result->suppliers_organization->name.'</h2>';
	if(!empty($result->suppliers_organization->supplier->descrizione))
		$html .= '<h3>'.$result->suppliers_organization->supplier->descrizione.'</h3>';

	$html .= '	<table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$html .= '	<thead>'; // con questo TAG mi ripete l'intestazione della tabella
	$html .= '		<tr>';
	$html .= '			<th width="5%">' . __('Bio') . '</th>';
	$html .= '			<th width="30%">' . __('Name') . '</th>';
	$html .= '			<th width="10%">' . __('qta') . '</th>';
	$html .= '			<th width="10%">' . __('Conf') . '</th>';
	$html .= '			<th width="15%">&nbsp;' . __('PrezzoUnita') . '</th>';
	$html .= '			<th width="10%">' . __('Prezzo/UM') . '</th>';
	$html .= '			<th width="20%">' . __('Importo') . '</th>';
	$html .= '	</tr>';
	$html .= '	</thead><tbody>';

	foreach($result->article_orders as $article_order) {
		
		// debug($article_order);

		$article_order['is_bio'] ? $is_bio = 'Bio': $is_bio = '';

		/*
		 * price
		 */
		$qta = $this->HtmlCustomSite->getArticleQta($article_order);
		$importo = $this->HtmlCustomSite->getArticleImporto($article_order);
		$cart_importo = ($importo * $qta);

		$html .= '<tr>';
		$html .= '	<td class="text-center">'.$is_bio.'</td>';
		$html .= '	<td>'.$article_order['name'].'</td>';
		$html .= '	<td class="text-center">'.$qta.'</td>';
		$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
		$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($cart_importo).'</td>';
		$html .= '</tr>';

	}

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

echo $html;
?>