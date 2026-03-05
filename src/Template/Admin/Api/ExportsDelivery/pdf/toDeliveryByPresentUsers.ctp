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
$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery, ['year'=> true]);
$delivery_data = $this->HtmlCustomSite->drawDeliveryDateLabel($delivery);

$html = '';
$html .= '<h3>'.__('Delivery').' '.$delivery_label.' '.$delivery_data.'</h3>';

if(!empty($results)) {

	$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">';
	$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
	$html .= '	<tr>';
	$html .= '	<th scope="col">' . __('N') . '</th>';
	$html .= '	<th scope="col">' . __('User') . '</th>';
	if(isset($opts['users_present_contacts']) && $opts['users_present_contacts']=='Y')
		$html .= '<th scope="col">' . __('Contacts') . '</th>';
	if(isset($opts['users_detail_importo']) && $opts['users_detail_importo']=='Y') 
		$html .= '<th scope="col" class="text-center">'. __('ImportoTotaleOrdine').'</th>';
	if(isset($opts['users_detail_qta']) && $opts['users_detail_qta']=='Y') 
		$html .= '<th scope="col" class="text-center">'.__('QuantitaTotaleOrdine').'</th>';
	$html .= '</tr>';
	$html .= '</thead><tbody>';

	foreach($results as $numResult => $result) {
		
		$html .= '<tr>';
		$html .= '	<td>'.($numResult+1).'</td>';
		$html .= '	<td>'.$result['user']['name'].'</td>';
		if(isset($opts['users_present_contacts']) && $opts['users_present_contacts']=='Y') {
			$html .= '	<td>';
			$html .= $this->HtmlCustom->mail($result['user']['email']);
			if(!empty($result['user']['phone']))
				$html .= ' '.$result['user']['phone'];
			if(!empty($result['user']['address']))
				$html .= ' '.$result['user']['address'];
			$html .= '</td>';	
		}	
	
		if(isset($opts['users_detail_importo']) && $opts['users_detail_importo']=='Y') 
				$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_importo']).'</td>';	
		if(isset($opts['users_detail_qta']) && $opts['users_detail_qta']=='Y') 
				$html .= '	<td class="text-center">'.$result['user']['tot_user_qta'].'</td>';	
		$html .= '	</tr>';		
	} // end foreach($results as $result)

	$html .= '	<tr>';
	$html .= '	<th class="no-border"></th>';
	$html .= '	<th class="no-border"></th>';
	if(isset($opts['users_present_contacts']) && $opts['users_present_contacts']=='Y')
		$html .= '	<th class="no-border"></th>';
	if($opts['users_detail_importo']=='Y') 
		$html .= '	<th class="text-center no-border">'.$this->HtmlCustom->importo($delivery_tot_importo).'</th>';
	if($opts['users_detail_qta']=='Y') 
		$html .= '	<th class="text-center no-border">'.$delivery_tot_qta.'</th>';
	$html .= '	</tr>';

	$html .= '	</tbody>';
	$html .= '	</table>';			
}
echo $html;
?>