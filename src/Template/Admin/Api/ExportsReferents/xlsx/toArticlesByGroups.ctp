<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Order-'.$order_type_id)); 

/*
* ordine titolare
*/
$sheet->setCellValue('A2', __('Supplier')); 
$sheet->setCellValue('B2', $orderParent->suppliers_organization->name); 

if(!empty($orderParent->suppliers_organization->supplier->address_full))
	$sheet->setCellValue('C2', $orderParent->suppliers_organization->supplier->address_full); 
if(!empty($orderParent->suppliers_organization->supplier->telefono))
	$sheet->setCellValue('D2', $orderParent->suppliers_organization->supplier->telefono);
if(!empty($orderParent->suppliers_organization->supplier->mail))
	$sheet->setCellValue('E2', $orderParent->suppliers_organization->supplier->mail);

if(isset($opts['delivery_order_parent']) && $opts['delivery_order_parent']=='Y') {
	$sheet->setCellValue('A3', __('Delivery'));
	$sheet->setCellValue('B3', $orderParent->delivery->luogo.' '.$orderParent->delivery->data->i18nFormat('eeee d MMMM'));
}

if(!empty($orders)) {

	$totale = 0;
	$i=0;
	foreach($orders as $order) {

		$i = ($i+3);

		$sheet->setCellValue('A'.($i), __('GasGroup'));
		$sheet->setCellValue('B'.($i), $order->gas_group->name);

		if(isset($opts['deliveries_orders']) && $opts['deliveries_orders']=='Y') {
			$sheet->setCellValue('C'.($i), __('Delivery'));
			$sheet->setCellValue('D'.($i), $order->delivery->luogo.' '.$order->delivery->data->i18nFormat('eeee d MMMM'));			
		}

		$sheet->setCellValue('A'.($i+1), __('Bio')); 
		$sheet->setCellValue('B'.($i+1), __('Name')); 
		$sheet->setCellValue('C'.($i+1), __('Conf')); 
		$sheet->setCellValue('D'.($i+1), __('Prezzo/UM')); 
		$sheet->setCellValue('E'.($i+1), __('PrezzoUnita')); 
		$sheet->setCellValue('F'.($i+1), __('Qta')); 
		$sheet->setCellValue('G'.($i+1), __('Importo'));

		$totale_ordine = 0;
		$ii=0;
		foreach($order->article_orders as $article_order) {

			$ii++; 

			$totale_ordine += $article_order->cart->final_price;
			
			$article_order->article->is_bio ? $is_bio = 'Si': $is_bio = 'No';

			$sheet->setCellValue('A'.($i+1+$ii), $is_bio);
			$sheet->setCellValue('B'.($i+1+$ii), $article_order->name);
			$sheet->setCellValue('C'.($i+1+$ii), $article_order->article->conf);
			$sheet->setCellValue('D'.($i+1+$ii), $article_order->article->um_rif_label);
			$sheet->setCellValue('E'.($i+1+$ii), $article_order->prezzo);
			$sheet->setCellValue('F'.($i+1+$niiumResult), $article_order->cart->final_qta);
			$sheet->setCellValue('G'.($i+1+$ii), $article_order->cart->final_price);

		} // end foreach($article_orders as $article_order)

		$sheet->setCellValue('A'.($i+1+$ii+1), '');
		$sheet->setCellValue('B'.($i+1+$ii+1), '');
		$sheet->setCellValue('C'.($i+1+$ii+1), '');
		$sheet->setCellValue('D'.($i+1+$ii+1), '');
		$sheet->setCellValue('E'.($i+1+$ii+1), '');
		$sheet->setCellValue('F'.($i+1+$ii+1), __('Totale ordine'));
		$sheet->setCellValue('G'.($i+1+$ii+1), $totale_ordine);

		$totale += $totale_ordine;

		$i++;

	} // end foreach($orders as $order) 

	$sheet->setCellValue('A'.($i+1+$ii+3), __('Totale'));
	$sheet->setCellValue('B'.($i+1+$ii+3), $totale);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/