<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Order-'.$order_type_id)); 

if(!empty($orders)) {

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

	$totale = 0;
	$i=0;
	$ii=0;
	$iii=0;
	foreach($orders as $order) {

		$i++;
		$i = ($i+2);

		$sheet->setCellValue('A'.($i+$ii+$iii), __('Gas Group'));
		$sheet->setCellValue('B'.($i+$ii+$iii), $order->gas_group->name);
		
		if(isset($opts['deliveries_orders']) && $opts['deliveries_orders']=='Y') {
			$sheet->setCellValue('C'.($i+$ii+$iii), __('Delivery'));
			$sheet->setCellValue('D'.($i+$ii+$iii), $order->delivery->luogo.' '.$order->delivery->data->i18nFormat('eeee d MMMM'));			
		}

		foreach($order->users as $user) {

			$ii++;

			/* 
			 * header user
			 */
			$sheet->setCellValue('A'.($i+$ii+$iii), $user->user->name); 
			$sheet->setCellValue('B'.($i+$ii+$iii), $user->user->email); 

		
			/* 
			 * header articles
			 */
			$ii++;
			$sheet->setCellValue('A'.($i+$ii+$iii), __('Bio')); 
			$sheet->setCellValue('B'.($i+$ii+$iii), __('Code')); 
			$sheet->setCellValue('C'.($i+$ii+$iii), __('Name')); 
			$sheet->setCellValue('D'.($i+$ii+$iii), __('Conf')); 
			$sheet->setCellValue('E'.($i+$ii+$iii), __('Prezzo/UM')); 
			$sheet->setCellValue('F'.($i+$ii+$iii), __('PrezzoUnita')); 
			$sheet->setCellValue('G'.($i+$ii+$iii), __('Qta')); 
			$sheet->setCellValue('H'.($i+$ii+$iii), __('Importo'));
						
			$totale_user = 0;
			foreach($user->article_orders as $article_order) {

				$iii++;
				$totale_user += $article_order->cart->final_price;
				
				$article_order->article->is_bio ? $is_bio = 'Si': $is_bio = 'No';

				$sheet->setCellValue('A'.($i+$ii+$iii), $is_bio); 
				$sheet->setCellValue('B'.($i+$ii+$iii), $article_order->article->codice); 
				$sheet->setCellValue('C'.($i+$ii+$iii), $article_order->name); 
				$sheet->setCellValue('D'.($i+$ii+$iii), $article_order->article->conf); 
				$sheet->setCellValue('E'.($i+$ii+$iii), $article_order->article->um_rif_label); 
				$sheet->setCellValue('F'.($i+$ii+$iii), $article_order->prezzo); 
				$sheet->setCellValue('G'.($i+$ii+$iii), $article_order->cart->final_qta); 
				$sheet->setCellValue('H'.($i+$ii+$iii), $article_order->cart->final_price); 

			} // end foreach($article_orders as $ii => $article_order)

			$i++;
			$sheet->setCellValue('A'.($i+$ii+$iii), ''); 
			$sheet->setCellValue('B'.($i+$ii+$iii), ''); 
			$sheet->setCellValue('C'.($i+$ii+$iii), ''); 
			$sheet->setCellValue('D'.($i+$ii+$iii), ''); 
			$sheet->setCellValue('E'.($i+$ii+$iii), '');
			$sheet->setCellValue('F'.($i+$ii+$iii), ''); 
			$sheet->setCellValue('G'.($i+$ii+$iii), __('Totale gasista')); 
			$sheet->setCellValue('H'.($i+$ii+$iii), $totale_user); 
	
			$totale += $totale_user;
		} // foreach($order->users as $i => $user)

	} // end foreach($orders as $order) 

	$sheet->setCellValue('A'.($i+$ii+$iii+2), __('Totale'));
	$sheet->setCellValue('B'.($i+$ii+$iii+2), $totale);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/