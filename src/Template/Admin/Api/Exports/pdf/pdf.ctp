<?php
// debug($results);
$html = '';

foreach($results as $result) {
	$html .= '<h2>Produttore '.$result->suppliers_organization->name.'</h2>';

	$html .= '	<table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$html .= '	<thead>'; // con questo TAG mi ripete l'intestazione della tabella
	$html .= '		<tr>';
	$html .= '			<th width="5%">' . __('Bio') . '</th>';
	$html .= '			<th width="30%">' . __('Name') . '</th>';
	$html .= '			<th width="10%">' . __('qta') . '</th>';
	$html .= '			<th width="15%">&nbsp;' . __('PrezzoUnita') . '</th>';
	$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
	$html .= '			<th width="20%">' . __('Importo') . '</th>';
	$html .= '	</tr>';
	$html .= '	</thead><tbody>';

	foreach($result->article_orders as $article_order) {
		
		// debug($article_order);

		$article_order['is_bio'] ? $is_bio = 'Bio': $is_bio = '';

		$html .= '<tr>';
		$html .= '	<td class="text-center">'.$is_bio.'</td>';
		$html .= '	<td>'.$article_order['name'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['cart']['qta'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
		$html .= '	<td class="text-center">'.$article_order['price'].'</td>';
		$html .= '</tr>';

	}

	$html .= '	</tbody>';
	$html .= '	</table>';	

} // loop orders

echo $html;
?>