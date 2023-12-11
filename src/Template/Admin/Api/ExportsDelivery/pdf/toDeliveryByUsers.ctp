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

	$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">';
	$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
	$html .= '	<tr>';
	$html .= '			<th scope="col">' . __('User') . '</th>';
	if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y')
		$html .= '<th scope="col">' . __('Contacts') . '</th>';
	else 
		$html .= '<th></th>';
	$html .= '			<th scope="col">' . __('SupplierOrganization') . '</th>';
	if($format=='HTML')
		$html .= '			<th scope="col" class="text-center"></th>';
	$html .= '			<th scope="col" class="text-center">' . __('Total carts') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('Trasport') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('CostMore') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('CostLess') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('Importo totale ordine') . '</th>';
	$html .= '	</tr>';
	$html .= '	</thead><tbody>';

	foreach($results as $result) {

		$rowspan = count($result['orders']);
		
		$html .= '<tr>';
		$html .= '	<td rowspan="'.($rowspan+1).'">'.$result['user']['name'].'</td>';
		if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y') {
			$html .= '	<td rowspan="'.($rowspan+1).'">';
			$html .= $this->HtmlCustom->mail($result['user']['email']);
			if(!empty($result['user']['phone']))
				$html .= $result['user']['phone'];
			$html .= '</td>';	
		}
		else 
			$html .= '<td rowspan="'.($rowspan+1).'"></td>';
		
		foreach($result['orders'] as $order) {
			
			$html .= '<tr>';
			$html .= '	<td>'.$order['order']->suppliers_organization->name.'</td>';
			if($format=='HTML' && !empty($order['order']->suppliers_organization->supplier->img1)) {
				$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $order['order']->suppliers_organization->supplier->img1);
				$img_path_supplier = $_portalgas_app_root . $img_path_supplier;
	
				$url = '';
				if(file_exists($img_path_supplier)) {
					$url = sprintf($_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $order['order']->suppliers_organization->supplier->img1);
					$html .= '<td><img src="'.$url.'" width="'.Configure::read('Supplier.img.preview.width').'" /></td>';
				}			
			}
	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['tot_importo_only_cart']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_trasport']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_cost_more']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_cost_less']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['tot_importo']).'</td>';	
			$html .= '</tr>';

		} // end foreach($result['orders'] as $order)
		
		$html .= '	<tr>';
		$html .= '		<th colspan="';
		($format=='HTML')? $html .= '6' : $html .= '5';
		$html .= '" class="no-border"></th>';
		$html .= '		<th colspan="2" class="text-right no-border">' . __('Total user') . '</th>';
		$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($result['user']['tot_user_importo']). '</th>';
		$html .= '	</tr>';		
	} // end foreach($results as $result)

	$html .= '	<tr>';
	$html .= '		<th colspan="';
	($format=='HTML')? $html .= '6' : $html .= '5';
	$html .= '" class="no-border"></th>';
	$html .= '		<th colspan="2" class="text-right no-border">' . __('Total delivery') . '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_importo). '</th>';
	$html .= '	</tr>';

	$html .= '	</tbody>';
	$html .= '	</table>';			
}
echo $html;
?>