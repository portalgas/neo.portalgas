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
	$html .= '			<th scope="col">' . __('N') . '</th>';
	$html .= '			<th scope="col">' . __('SupplierOrganization') . '</th>';
	if($format=='HTML')
		$html .= '			<th scope="col" class="text-center"></th>';
	$html .= '			<th scope="col">' . __('StateOrder') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('Total Carts') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('Trasport') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('CostMore') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('CostLess') . '</th>';
	$html .= '			<th scope="col" class="text-center">' . __('Importo totale ordine') . '</th>';
	$html .= '	</tr>';
	$html .= '	</thead><tbody>';

	foreach($results as $numResult => $result) {
		$html .= '<tr>';
		$html .= '	<td>'.($numResult + 1).'</td>';
		$html .= '	<td>'.$result['suppliers_organization']->name.'</td>';
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
		$html .= '	<td>'.__($result['order']['state_code'].'-intro').'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order_only_cart']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['trasport']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_more']).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo((-1 * $result['order']['cost_less'])).'</td>';
		$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order']).'</td>';
		$html .= '</td>';
	} // end foreach($article_orders as $numResult => $article_order)

	$html .= '	<tr>';
	$html .= '		<th class="no-border"></th>';
	$html .= '		<th class="no-border"></th>';
	if($format=='HTML') 
		$html .= '<th class="no-border"></th>';
	$html .= '		<th class="text-right no-border">' . __('Total delivery') . '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_order_only_cart). '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_trasport). '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_cost_more). '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo((-1 * $delivery_tot_cost_less)). '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_importo). '</th>';
	$html .= '	</tr>';

	$html .= '	</tbody>';
	$html .= '	</table>';			
}
echo $html;
?>