<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery, $opts = ['year'=> true]);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', __('Delivery')); 
$sheet->setCellValue('B1', $delivery_label); 
$sheet->setCellValue('C1', $delivery_data); 

if(!empty($results)) {

	$sheet->setCellValue('A2', __('SupplierOrganization')); 
	$sheet->setCellValue('B2', __('StateOrder')); 
	$sheet->setCellValue('C2', __('Total Carts')); 
	$sheet->setCellValue('D2', __('Trasport')); 
	$sheet->setCellValue('E2', __('CostMore')); 
	$sheet->setCellValue('F2', __('CostLess')); 
	$sheet->setCellValue('G2', __('Importo totale ordine')); 

	$i=3;
	foreach($results as $result) {

		$sheet->setCellValue('A'.($i), $result['suppliers_organization']->name);
		$sheet->setCellValue('B'.($i), __($result['order']['state_code'].'-intro'));
		$sheet->setCellValue('C'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order_only_cart']));
		$sheet->setCellValue('D'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['trasport']));
		$sheet->setCellValue('E'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_more']));
		$sheet->setCellValue('F'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_less']));
		$sheet->setCellValue('G'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order']));

		$i++;
	} // end foreach($results as $result)

	$sheet->setCellValue('F'.($i), __('Total delivery'));
	$sheet->setCellValue('G'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));
} // end if(!empty($results))

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
