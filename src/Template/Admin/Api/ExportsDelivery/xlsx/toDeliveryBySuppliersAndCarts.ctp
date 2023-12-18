<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Delivery')); 
$sheet->setCellValue('B1', $delivery_label); 
$sheet->setCellValue('C1', $delivery_data); 

$sheet->setCellValue('A2', __('Total delivery'));
$sheet->setCellValue('B2', $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));

$sheet->setCellValue('A3', ''); 

if(!empty($results)) {

	$i=3;
	foreach($results as $numResult => $result) {

		$i++;
		$sheet->setCellValue('A'.$i, $result['suppliers_organization']->name);
		
		$i++;
		$sheet->setCellValue('A'.$i, __('Total carts'));
		$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order_only_cart']));

		if($result['order']['trasport']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, __('Trasport'));
			$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['trasport']));			
		}
		if($result['order']['cost_more']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, __('CostMore'));
			$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_more']));

		}
		if($result['order']['cost_less']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, __('CostLess'));
			$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_less']));
		}
		if($result['order']['trasport']>0 || $result['order']['cost_more']>0 || $result['order']['cost_less']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, __('Importo totale ordine'));
			$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order']));
		}		
		
		$i++;
		$sheet->setCellValue('A'.$i, '');
		$sheet->setCellValue('B'.$i, '');
		$sheet->setCellValue('C'.$i, '');
		$sheet->setCellValue('D'.$i, '');
		$sheet->setCellValue('E'.$i, '');
		$sheet->setCellValue('F'.$i, '');
		if($opts['referent_modify']=='Y') {
			$sheet->setCellValue('G'.$i, __('Quantità'));
			$sheet->setCellValue('H'.$i, __('Importo'));
			$sheet->setCellValue('I'.$i, __('Quantità'));
			$sheet->setCellValue('J'.$i, __('e importo totali'));
		}		

		$i++;
		$sheet->setCellValue('A'.$i, __('Name'));
		$sheet->setCellValue('B'.$i, '');
		$sheet->setCellValue('C'.$i, '');
		$sheet->setCellValue('D'.$i, __('Prezzo unità'));
		$sheet->setCellValue('E'.$i, __('Quantità'));
		$sheet->setCellValue('F'.$i, __('Importo'));
		if($opts['referent_modify']=='Y') {
			$sheet->setCellValue('G'.$i, "dell'utente");
			$sheet->setCellValue('H'.$i, "dell'utente");
			$sheet->setCellValue('I'.$i, "modificati dal referente");
			$sheet->setCellValue('J'.$i, "modificati dal referente");
			$sheet->setCellValue('K'.$i, __('Importo forzati'));
		}
		
		$user_id_old = 0;
		foreach($result['order']['carts'] as $cart) {
			
			$final_price = $this->HtmlCustomSite->getCartFinalPrice($cart);
			($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;

			// header user
			if($cart->user_id!=$user_id_old) {

				if($user_id_old>0) {
					// totale gasista
					$i++;
					$this->HtmlCustomSiteExport->toExcelDeliveryBySuppliersAndCartsDrawUserTotale($sheet, $i, $result['order']['users'][$user_id_old], $format, $opts);
				}

				$i++;
				$sheet->setCellValue('A'.$i, __('User'));
				$sheet->setCellValue('B'.$i, $cart->user->name);
			}

			$i++;
			$sheet->setCellValue('A'.$i, $cart->articles_order->name);
			$sheet->setCellValue('B'.$i, '');
			$sheet->setCellValue('C'.$i, '');
			$sheet->setCellValue('D'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->articles_order->prezzo));
			$sheet->setCellValue('E'.$i, $final_qta);
			$sheet->setCellValue('F'.$i, $this->HtmlCustomSiteExport->excelimporto($final_price));
			if($opts['referent_modify']=='Y') {
				/*
				* qta originali, quelli del gasista
				*/
				$sheet->setCellValue('G'.$i, $cart->qta);
				$sheet->setCellValue('H'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->qta * $cart->articles_order->prezzo));

				/*
				* qta modificata dal referente
				*/
				$sheet->setCellValue('I'.$i, $cart->qta_forzato);
				($cart->qta_forzato>0) ? $tot = $this->HtmlCustomSiteExport->excelimporto($cart->qta_forzato * $cart->articles_order->prezzo): $tot = 0;
				$sheet->setCellValue('J'.$i, $tot);

				/* 
				* importo modificata dal referente
				*/
				($cart->importo_forzato>0) ? $tot = $this->HtmlCustomSiteExport->excelimporto($cart->importo_forzato): $tot = 0;
				$sheet->setCellValue('K'.$i, $tot);
			}

			if($opts['cart_nota']=='Y')
				if(!empty($cart->nota)) {
					$i++;
					$sheet->setCellValue('A'.$i, '');
					$sheet->setCellValue('B'.$i, 'Nota');
					$sheet->setCellValue('C'.$i, $cart->nota);
				}

			$user_id_old = $cart->user_id;

		} // foreach($result['order']['carts'] as $cart)
		
		// totale gasista
		$i++;
		$this->HtmlCustomSiteExport->toExcelDeliveryBySuppliersAndCartsDrawUserTotale($sheet, $i, $result['order']['users'][$user_id_old], $format, $opts);
		
		$i++;
		$sheet->setCellValue('A'.$i, '');

	} // end foreach($results as $numResult => $result) 
} // end if(!empty($results))

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
