<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Order-'.$order_type_id)); 

if(!empty($article_orders)) {

	$sheet->setCellValue('A2', __('Bio')); 
	$sheet->setCellValue('B2', __('Name')); 
	$sheet->setCellValue('C2', __('Conf')); 
	$sheet->setCellValue('D2', __('Prezzo/UM')); 
	$sheet->setCellValue('E2', __('PrezzoUnita')); 
	$sheet->setCellValue('F2', __('Qta')); 
	$sheet->setCellValue('G2', __('Importo')); 

	$totale_ordine = 0;
	foreach($article_orders as $numResult => $article_order) {

		$totale_ordine += $article_order->cart->final_price;

		$article_order->article->is_bio ? $is_bio = 'Si': $is_bio = 'No';

		$sheet->setCellValue('A'.($numResult+2), $is_bio);
		$sheet->setCellValue('B'.($numResult+2), $article_order->name);
		$sheet->setCellValue('C'.($numResult+2), $article_order->article->conf);
		$sheet->setCellValue('D'.($numResult+2), $article_order->article->um_rif_label);
		$sheet->setCellValue('E'.($numResult+2), $article_order->prezzo);
		$sheet->setCellValue('F'.($numResult+2), $article_order->cart->final_qta);
		$sheet->setCellValue('G'.($numResult+2), $article_order->cart->final_price);

	} // end foreach($article_orders as $numResult => $article_order)

	$sheet->setCellValue('A'.($numResult+3), '');
	$sheet->setCellValue('B'.($numResult+3), '');
	$sheet->setCellValue('C'.($numResult+3), '');
	$sheet->setCellValue('D'.($numResult+3), '');
	$sheet->setCellValue('E'.($numResult+3), '');
	$sheet->setCellValue('F'.($numResult+3), __('Totale ordine'));
	$sheet->setCellValue('G'.($numResult+3), $totale_ordine);
		
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
