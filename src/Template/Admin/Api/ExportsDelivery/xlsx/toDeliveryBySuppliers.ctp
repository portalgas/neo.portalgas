<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Delivery').' '.$delivery_label.' '.$delivery_data); 

if(!empty($results)) {

	$sheet->setCellValue('A2', __('SupplierOrganization')); 
	$sheet->setCellValue('B2', __('Total carts')); 
	$sheet->setCellValue('C2', __('Trasport')); 
	$sheet->setCellValue('D2', __('CostMore')); 
	$sheet->setCellValue('E2', __('CostLess')); 
	$sheet->setCellValue('F2', __('Importo totale ordine')); 

	$i=3;
	foreach($results as $result) {

		$sheet->setCellValue('A'.($i), $result['suppliers_organization']->name);
		$sheet->setCellValue('B'.($i), $this->HtmlCustomSite->excelimporto($result['order']['tot_order_only_cart']));
		$sheet->setCellValue('C'.($i), $this->HtmlCustomSite->excelimporto($result['order']['trasport']));
		$sheet->setCellValue('D'.($i), $this->HtmlCustomSite->excelimporto($result['order']['cost_more']));
		$sheet->setCellValue('E'.($i), $this->HtmlCustomSite->excelimporto($result['order']['cost_less']));
		$sheet->setCellValue('F'.($i), $this->HtmlCustomSite->excelimporto($result['order']['tot_order']));

		$i++;
	} // end foreach($results as $result)

	$sheet->setCellValue('E'.($i), __('Total delivery'));
	$sheet->setCellValue('F'.($i), $this->HtmlCustomSite->excelimporto($delivery_tot_importo));
} // end if(!empty($results))

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
