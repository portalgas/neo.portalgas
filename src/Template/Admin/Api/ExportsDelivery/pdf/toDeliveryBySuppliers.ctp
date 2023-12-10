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
		$html .= '<tr>';
		$html .= '	<td>'.$result['suppliers_organization']->name.'</td>';
		if($format=='HTML' && !empty($result['suppliers_organization']->supplier->img1)) {
			$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
			$img_path_supplier = $_portalgas_app_root . $img_path_supplier;

			$url = '';
			if(file_exists($img_path_supplier)) {
				$url = sprintf($_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $result['suppliers_organization']->supplier->img1);
				$html .= '<td><img src="'.$url.'" width="'.Configure::read('Supplier.img.preview.width').'" /></td>';
			}			
		}

			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order_only_cart']).'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['trasport']).'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_more']).'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['cost_less']).'</td>';
			$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($result['order']['tot_order']).'</td>';
			$html .= '</tr>';
	} // end foreach($article_orders as $numResult => $article_order)

	$html .= '	<tr>';
	$html .= '		<td colspan="';
	($format=='HTML')? $html .= '4' : $html .= '3';
	$html .= '" class="no-border"></td>';
	$html .= '		<th colspan="2" class="text-right no-border">' . __('Total delivery') . '</th>';
	$html .= '		<th class="text-center no-border">' .$this->HtmlCustom->importo($delivery_tot_importo). '</th>';
	$html .= '	</tr>';

	$html .= '	</tbody>';
	$html .= '	</table>';			
}
echo $html;
?>