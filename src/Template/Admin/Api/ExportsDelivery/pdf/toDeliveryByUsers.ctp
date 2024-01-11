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
	$html .= '	<th scope="col">' . __('User') . '</th>';
	if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y')
		$html .= '<th scope="col">' . __('Contacts') . '</th>';
	else 
		$html .= '<th></th>';
	$html .= '	  <th scope="col">';
	if($opts['users_detail_orders']=='Y') 
		$html .= __('SupplierOrganization');
	$html .='</th>';
	if($format=='HTML')
		$html .= '	<th scope="col" class="text-center"></th>';
	$html .= '	<th scope="col" class="text-center">' . __('Total Carts') . '</th>';
	$html .= '	<th scope="col" class="text-center">' . __('Trasport') . '</th>';
	$html .= '	<th scope="col" class="text-center">' . __('CostMore') . '</th>';
	$html .= '	<th scope="col" class="text-center">' . __('CostLess') . '</th>';
	$html .= '	<th scope="col" class="text-center">' . __('Importo totale ordine') . '</th>';
	$html .= '</tr>';
	$html .= '</thead><tbody>';

	foreach($results as $result) {

		($opts['users_detail_orders']=='Y') ? $rowspan = count($result['orders']): $rowspan = 1;
				
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
		
		if($opts['users_detail_orders']=='Y')
		foreach($result['orders'] as $order) {
			
			$html .= '<tr>';
			$html .= '	<td>'.$order['order']->suppliers_organization->name.'</td>';
			$html .= '<td>';
			if($format=='HTML') {
				if(!empty($order['order']->suppliers_organization->supplier->img1)) {
					$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $order['order']->suppliers_organization->supplier->img1);
					$img_path_supplier = $_portalgas_app_root . $img_path_supplier;
		
					$url = '';
					if(file_exists($img_path_supplier)) {
						$url = sprintf($_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $order['order']->suppliers_organization->supplier->img1);
						$html .= '<img src="'.$url.'" width="'.Configure::read('Supplier.img.preview.width').'" />';
					}
				}			
			}
			$html .= '</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['tot_importo_only_cart']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_trasport']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_cost_more']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['importo_cost_less']).'</td>';	
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($order['tot_importo']).'</td>';	
			$html .= '</tr>';

		} // end foreach($result['orders'] as $order)
		
		$html .= '	<tr>';
		if($opts['users_detail_orders']=='Y') {
			$html .= '	<th></th>';
			$html .= '  <th></th>';	
		}
		$html .= '	<th class="text-right">';
		if($opts['users_detail_orders']=='Y') 
			$html .= __('Total user');
		$html .= '</th>';
		if($format=='HTML')
			$html .= '	<th class="text-center"></th>';
		$html .= '	<th class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_importo_only_cart']).'</th>';
		$html .= '	<th class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_trasport']).'</th>'; 
		$html .= '	<th class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_cost_more']).'</th>';
		$html .= '	<th class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_cost_less']).'</th>';
		$html .= '	<th class="text-center">'.$this->HtmlCustom->importo($result['user']['tot_user_importo']).'</th>';	
		$html .= '	</tr>';		
	} // end foreach($results as $result)

	$html .= '	<tr>';
	$html .= '	<th class="no-border"></th>';
	$html .= '  <th class="no-border"></th>';
	$html .= '	<th class="text-right no-border"></th>';
	if($format=='HTML')
		$html .= '	<th class="text-center no-border"></th>';
	$html .= '	<th class="text-center no-border"></th>'; // Total Carts
	$html .= '	<th class="text-center no-border"></th>'; // Trasport
	$html .= '	<th class="text-center no-border"></th>'; // CostMore
	$html .= '	<th class="text-right no-border">'.__('Total delivery').'</th>'; // CostLess
	$html .= '	<th class="text-center no-border">'.$this->HtmlCustom->importo($delivery_tot_importo).'</th>';
	$html .= '	</tr>';

	$html .= '	</tbody>';
	$html .= '	</table>';			
}
echo $html;
?>