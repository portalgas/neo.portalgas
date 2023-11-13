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
	$num_row=0;
	foreach($orders as $order) {

		$num_row++;

		$sheet->setCellValue('A'.($num_row), __('Gas Group'));
		$sheet->setCellValue('B'.($num_row), $order->gas_group->name);

		if(isset($opts['deliveries_orders']) && $opts['deliveries_orders']=='Y') {
			$sheet->setCellValue('C'.($num_row), __('Delivery'));
			$sheet->setCellValue('D'.($num_row), $order->delivery->luogo.' '.$order->delivery->data->i18nFormat('eeee d MMMM'));			
		}

		$num_row++;

		$sheet->setCellValue('A'.($num_row), __('Bio')); 
		$sheet->setCellValue('B'.($num_row), __('Code'));
		$sheet->setCellValue('C'.($num_row), __('Name')); 
		$sheet->setCellValue('D'.($num_row), __('Conf')); 
		$sheet->setCellValue('E'.($num_row), __('Prezzo/UM')); 
		$sheet->setCellValue('F'.($num_row), __('PrezzoUnita')); 
		$sheet->setCellValue('G'.($num_row), __('Qta')); 
		$sheet->setCellValue('H'.($num_row), __('Importo'));

		$totale_ordine = 0;
		foreach($order->article_orders as $article_order) {

			$num_row++; 

			$totale_ordine += $article_order->cart->final_price;
			
			$article_order->article->is_bio ? $is_bio = 'Si': $is_bio = 'No';

			$sheet->setCellValue('A'.($num_row), $is_bio);
			$sheet->setCellValue('B'.($num_row), $article_order->article->codice);
			$sheet->setCellValue('C'.($num_row), $article_order->name);
			$sheet->setCellValue('D'.($num_row), $article_order->article->conf);
			$sheet->setCellValue('E'.($num_row), $article_order->article->um_rif_label);
			$sheet->setCellValue('F'.($num_row), $article_order->prezzo);
			$sheet->setCellValue('G'.($num_row), $article_order->cart->final_qta);
			$sheet->setCellValue('H'.($num_row), $article_order->cart->final_price);

		} // end foreach($article_orders as $article_order)

		$num_row++; 
		$sheet->setCellValue('A'.($num_row), '');
		$sheet->setCellValue('B'.($num_row), '');
		$sheet->setCellValue('C'.($num_row), '');
		$sheet->setCellValue('D'.($num_row), '');
		$sheet->setCellValue('E'.($num_row), '');
		$sheet->setCellValue('F'.($num_row), '');
		$sheet->setCellValue('G'.($num_row), __('Totale ordine'));
		$sheet->setCellValue('H'.($num_row), $totale_ordine);

		$totale += $totale_ordine;

		$num_row++; 

	} // end foreach($orders as $order) 

	$sheet->setCellValue('A'.($num_row+3), __('Totale'));
	$sheet->setCellValue('B'.($num_row+3), $totale);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/