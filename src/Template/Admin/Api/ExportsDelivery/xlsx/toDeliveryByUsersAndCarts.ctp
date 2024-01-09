<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery, ['year'=> true]);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Delivery')); 
$sheet->setCellValue('B1', $delivery_label); 
$sheet->setCellValue('C1', $delivery_data); 

$sheet->setCellValue('A2', ''); 

if(!empty($results)) {

	$i=2;
	foreach($results as $numResult => $result) {

		$i++;
		$sheet->setCellValue('A'.$i, $result['user']->name);
		
		$i++;
		$sheet->setCellValue('A'.$i, '');
		$sheet->setCellValue('B'.$i, __('Total user'));
		$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['user']['user_tot_importo']));

		if($result['user']['user_importo_trasport']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, '');
			$sheet->setCellValue('B'.$i, __('Trasport'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['user']['user_importo_trasport']));			
		}
		if($result['user']['user_importo_cost_more']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, '');
			$sheet->setCellValue('B'.$i, __('CostMore'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['user']['user_importo_cost_more']));

		}
		if($result['user']['user_importo_cost_less']>0) {
			$i++;
			$sheet->setCellValue('A'.$i, '');
			$sheet->setCellValue('B'.$i, __('CostLess'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($result['user']['user_importo_cost_less']));
		}

		$user_totale = $result['user']['user_tot_importo'];
		if(!empty($result['user']['user_importo_trasport'])) {
			$user_totale += $result['user']['user_importo_trasport']; 
		}
		if(!empty($result['user']['user_importo_cost_more'])) {
			$user_totale += $result['user']['user_importo_cost_more']; 
		}
		if(!empty($result['user']['user_importo_cost_less'])) {
			$user_totale += $result['user']['user_importo_cost_less']; 
		}                
		if($user_totale != $result['user']['user_tot_importo']) {
			$i++;
			$sheet->setCellValue('A'.$i, '');
			$sheet->setCellValue('B'.$i, __('Importo totale ordine'));
			$sheet->setCellValue('C'.$i, $this->HtmlCustomSiteExport->excelImporto($user_totale));
		}		
		
		foreach($result['user']['orders'] as $order) {	

			$i++;
			$sheet->setCellValue('A'.$i, $order['suppliers_organization']->name);
			$sheet->setCellValue('B'.$i, __($order['order']['state_code'].'-intro'));

			if($opts['referent_modify_users']=='Y') {
				$i++;
				$sheet->setCellValue('A'.$i, '');
				$sheet->setCellValue('B'.$i, '');
				$sheet->setCellValue('C'.$i, '');
				$sheet->setCellValue('D'.$i, '');
				if($opts['referent_modify_users']=='Y') {
					$sheet->setCellValue('E'.$i, __('Qta'));
					$sheet->setCellValue('F'.$i, __('Importo'));
					$sheet->setCellValue('G'.$i, __('Quantità e importo totali'));
				}
			} // end if($opts['referent_modify_users']=='Y') 

			$i++;
			$sheet->setCellValue('A'.$i, __('Name'));
			$sheet->setCellValue('B'.$i, __('PrezzoUnita'));
			$sheet->setCellValue('C'.$i, __('Qta'));
			$sheet->setCellValue('D'.$i, __('Importo'));
			if($opts['referent_modify_users']=='Y') {
				$sheet->setCellValue('E'.$i, "dell 'utente");
				$sheet->setCellValue('F'.$i, "dell 'utente");
				$sheet->setCellValue('G'.$i, 'modificati dal referente');
				$sheet->setCellValue('H'.$i, 'modificati dal referente');
				$sheet->setCellValue('I'.$i, __('Importo forzati'));
			} // end if($opts['referent_modify_users']=='Y') 			

			foreach($order['carts'] as $cart) {
			
				$final_price = $this->HtmlCustomSite->getCartFinalPrice($cart);
				($cart->qta_forzato>0) ? $final_qta = $cart->qta_forzato: $final_qta = $cart->qta;

				$i++;
				$sheet->setCellValue('A'.$i, $cart->articles_order->name);
				$sheet->setCellValue('B'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->articles_order->prezzo));
				$sheet->setCellValue('C'.$i, $final_qta);
				$sheet->setCellValue('D'.$i, $this->HtmlCustomSiteExport->excelimporto($final_price));
				$sheet->setCellValue('E'.$i, $final_qta);
				if($opts['referent_modify_users']=='Y') {
					/*
					* qta originali, quelli del gasista
					*/					
					$sheet->setCellValue('E'.$i, $cart->qta);
					$sheet->setCellValue('F'.$i, $this->HtmlCustomSiteExport->excelimporto($cart->qta * $cart->articles_order->prezzo));
					/*
					* qta modificata dal referente
					*/
					if($cart->qta_forzato>0)
						$qta_forzato = $cart->qta_forzato;
					else 
						$qta_forzato = '-';
					$sheet->setCellValue('G'.$i, $qta_forzato);

					if($cart->qta_forzato>0)
						$qta_forzato = $this->HtmlCustomSiteExport->excelimporto($cart->qta_forzato * $cart->articles_order->prezzo);
					else 
						$qta_forzato = '-';					
					$sheet->setCellValue('H'.$i, $qta_forzato);
					/* 
					* importo modificata dal referente
					*/
					if($cart->importo_forzato>0)
						$importo_forzato = $this->HtmlCustomSiteExport->excelimporto($cart->importo_forzato);
					else 
						$importo_forzato = '-';										
					$sheet->setCellValue('I'.$i, $importo_forzato);
				} // end if($opts['referent_modify_users']=='Y') 

				if($opts['cart_nota_users']=='Y')
				if(!empty($cart->nota)) {

					$i++;
					$sheet->setCellValue('A'.$i, '');
					$sheet->setCellValue('B'.$i, 'Nota');
					$sheet->setCellValue('C'.$i, $cart->nota);
				}

			} // end foreach($order['carts'] as $cart) 

		} // end foreach($result['user']['orders'] as $order)

		// totale gasista dell'ordine
		$i++;
		$sheet->setCellValue('A'.$i, '');
		$sheet->setCellValue('B'.$i, '');
		$sheet->setCellValue('C'.$i, __('Total user'));
		$sheet->setCellValue('D'.$i, $order['user_order_tot_qta']);
		

		$tmp = '';
		$tmp .= $this->HtmlCustomSiteExport->excelimporto($order['user_order_tot_importo']);
		if(isset($order['user_order_importo_trasport']))
			$tmp .= " \n".__('Trasport').' '.$this->HtmlCustomSiteExport->excelimporto($order['user_order_importo_trasport']).' +';
		if(isset($order['user_order_importo_cost_more']))
			$tmp .= " \n".__('CostMore').' '.$this->HtmlCustomSiteExport->excelimporto($order['user_order_importo_cost_more']).' +';
		if(isset($order['user_order_importo_cost_less']))
			$tmp .= " \n".__('CostLess').' '.$this->HtmlCustomSiteExport->excelimporto((-1 * $order['user_order_importo_cost_less'])).' -';

		$user_totale = $order['user_order_tot_importo'];
		if(isset($order['user_order_importo_trasport'])) {
			$user_totale += $order['user_order_importo_trasport']; 
		}
		if(isset($order['user_order_importo_cost_more'])) {
			$user_totale += $order['user_order_importo_cost_more']; 
		}
		if(isset($order['user_order_importo_cost_less'])) {
			$user_totale += $order['user_order_importo_cost_less']; 
		}                
		if($user_totale != $order['user_order_tot_importo']) {
			$tmp .= " \n".$this->HtmlCustomSiteExport->excelimporto($user_totale).' =';
		}
		$sheet->setCellValue('E'.$i, $tmp);

	} // end foreach($result['user']['orders'] as $order)
} // end if(!empty($results))

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
