<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery, ['year'=> true]);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', '');
$sheet->setCellValue('B1', __('Delivery'));
$sheet->setCellValue('C1', $delivery_label);
$sheet->setCellValue('D1', $delivery_data);

$sheet->setCellValue('B2', __('Total delivery'));
$sheet->setCellValue('C2', $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));

$sheet->setCellValue('A3', '');
$user_id_old = 0;

if(!empty($results)) {

	$i=3;
	foreach($results as $numResult => $result) {

		$i++;
		$sheet->setCellValue('A'.$i, ($numResult+1));
		$sheet->setCellValue('B'.$i, $result['suppliers_organization']->name);
		$sheet->setCellValue('C'.$i, __($result['order']['state_code'].'-intro'));

		$i++;
		$sheet->setCellValue('B'.$i, __('Total Carts'));
		$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order_only_cart']));

		if($result['order']['trasport']>0) {
			$i++;
			$sheet->setCellValue('B'.$i, __('Trasport'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['trasport']));
		}
		if($result['order']['cost_more']>0) {
			$i++;
			$sheet->setCellValue('B'.$i, __('CostMore'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_more']));

		}
		if($result['order']['cost_less']>0) {
			$i++;
			$sheet->setCellValue('B'.$i, __('CostLess'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_less']));
		}
		if($result['order']['trasport']>0 || $result['order']['cost_more']>0 || $result['order']['cost_less']>0) {
			$i++;
			$sheet->setCellValue('B'.$i, __('Importo totale ordine'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order']));
		}

		if(isset($result['order']['carts'])) {
			$i++;
			$sheet->setCellValue('B'.$i, '');
			$sheet->setCellValue('C'.$i, '');
			$sheet->setCellValue('D'.$i, '');
			$sheet->setCellValue('E'.$i, '');
			$sheet->setCellValue('F'.$i, '');
			$sheet->setCellValue('G'.$i, '');
			if($opts['referent_modify_suppliers']=='Y') {
				$sheet->setCellValue('H'.$i, __('Quantità'));
				$sheet->setCellValue('I'.$i, __('Importo'));
				$sheet->setCellValue('J'.$i, __('Quantità'));
				$sheet->setCellValue('K'.$i, __('e importo totali'));
			}

			$i++;
			$sheet->setCellValue('B'.$i, __('Name'));
			$sheet->setCellValue('C'.$i, '');
			$sheet->setCellValue('D'.$i, '');
			$sheet->setCellValue('E'.$i, __('Prezzo unità'));
			$sheet->setCellValue('F'.$i, __('Quantità'));
			$sheet->setCellValue('G'.$i, __('Importo'));
			if($opts['referent_modify_suppliers']=='Y') {
				$sheet->setCellValue('H'.$i, "dell'utente");
				$sheet->setCellValue('I'.$i, "dell'utente");
				$sheet->setCellValue('J'.$i, "modificati dal referente");
				$sheet->setCellValue('K'.$i, "modificati dal referente");
				$sheet->setCellValue('L'.$i, __('Importo forzati'));
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
					$sheet->setCellValue('B'.$i, __('User'));
					$sheet->setCellValue('C'.$i, $cart->user->name);
				}

				$i++;
				$sheet->setCellValue('B'.$i, $cart->articles_order->name);
				$sheet->setCellValue('C'.$i, '');
				$sheet->setCellValue('D'.$i, '');
				$sheet->setCellValue('E'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->articles_order->prezzo));
				$sheet->setCellValue('F'.$i, $final_qta);
				$sheet->setCellValue('G'.$i, $this->HtmlCustomSiteExport->excelimporto($final_price));
				if($opts['referent_modify_suppliers']=='Y') {
					/*
					* qta originali, quelli del gasista
					*/
					$sheet->setCellValue('H'.$i, $cart->qta);
					$sheet->setCellValue('I'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->qta * $cart->articles_order->prezzo));

					/*
					* qta modificata dal referente
					*/
					$sheet->setCellValue('J'.$i, $cart->qta_forzato);
					($cart->qta_forzato>0) ? $tot = $this->HtmlCustomSiteExport->excelimporto($cart->qta_forzato * $cart->articles_order->prezzo): $tot = 0;
					$sheet->setCellValue('K'.$i, $tot);

					/*
					* importo modificata dal referente
					*/
					($cart->importo_forzato>0) ? $tot = $this->HtmlCustomSiteExport->excelimporto($cart->importo_forzato): $tot = 0;
					$sheet->setCellValue('L'.$i, $tot);
				}

				if($opts['cart_nota_suppliers']=='Y')
					if(!empty($cart->nota)) {
						$i++;
						$sheet->setCellValue('B'.$i, '');
						$sheet->setCellValue('C'.$i, 'Nota');
						$sheet->setCellValue('D'.$i, $cart->nota);
					}

				$user_id_old = $cart->user_id;

			} // foreach($result['order']['carts'] as $cart)
		} // if(isset($result['order']['carts']))

		// totale gasista
        if($user_id_old>0) {
            $i++;
            $this->HtmlCustomSiteExport->toExcelDeliveryBySuppliersAndCartsDrawUserTotale($sheet, $i, $result['order']['users'][$user_id_old], $format, $opts);
        }

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
