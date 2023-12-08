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
	$sheet->setCellValue('B2', __('Code')); 
	$sheet->setCellValue('C2', __('Name')); 
	$sheet->setCellValue('D2', __('Conf')); 
	$sheet->setCellValue('E2', __('Prezzo/UM')); 
	$sheet->setCellValue('F2', __('PrezzoUnita')); 
	$sheet->setCellValue('G2', __('Qta')); 
	$sheet->setCellValue('H2', __('Importo')); 

	$i=3;
	$totale_ordine = 0;
	foreach($article_orders as $numResult => $article_order) {

		$totale_ordine += $article_order->cart->final_price;

		$article_order->article->is_bio ? $is_bio = 'Si': $is_bio = 'No';

		$sheet->setCellValue('A'.($i), $is_bio);
		$sheet->setCellValue('B'.($i), $article_order->article->codice);
		$sheet->setCellValue('C'.($i), $article_order->name);
		$sheet->setCellValue('D'.($i), $article_order->article->conf);
		$sheet->setCellValue('E'.($i), $article_order->article->um_rif_label);
		$sheet->setCellValue('F'.($i), $article_order->prezzo);
		$sheet->setCellValue('G'.($i), $article_order->cart->final_qta);
		$sheet->setCellValue('H'.($i), $article_order->cart->final_price);

		$i++;
	} // end foreach($article_orders as $numResult => $article_order)

	$sheet->setCellValue('A'.($i), '');
	$sheet->setCellValue('B'.($i), '');
	$sheet->setCellValue('C'.($i), '');
	$sheet->setCellValue('D'.($i), '');
	$sheet->setCellValue('E'.($i), '');
	$sheet->setCellValue('F'.($i), '');
	$sheet->setCellValue('G'.($i), __('Totale ordine'));
	$sheet->setCellValue('H'.($i), $totale_ordine);
		
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
