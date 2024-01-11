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

if(!empty($results)) {

	$sheet->setCellValue('A2', __('N')); 
	$sheet->setCellValue('B2', __('SupplierOrganization')); 
	$sheet->setCellValue('C2', __('StateOrder')); 
	$sheet->setCellValue('D2', __('Total Carts')); 
	$sheet->setCellValue('E2', __('Trasport')); 
	$sheet->setCellValue('F2', __('CostMore')); 
	$sheet->setCellValue('G2', __('CostLess')); 
	$sheet->setCellValue('H2', __('Importo totale ordine')); 

	$i=3;
	foreach($results as $numResult => $result) {

		$sheet->setCellValue('A'.($i), ($numResult + 1));
		$sheet->setCellValue('B'.($i), $result['suppliers_organization']->name);
		$sheet->setCellValue('C'.($i), __($result['order']['state_code'].'-intro'));
		$sheet->setCellValue('D'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order_only_cart']));
		$sheet->setCellValue('E'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['trasport']));
		$sheet->setCellValue('F'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_more']));
		$sheet->setCellValue('G'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['cost_less']));
		$sheet->setCellValue('H'.($i), $this->HtmlCustomSiteExport->excelImporto($result['order']['tot_order']));

		$i++;
	} // end foreach($results as $result)

	$sheet->setCellValue('c'.($i), __('Total delivery'));	
	$sheet->setCellValue('D'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_order_only_cart));
	$sheet->setCellValue('E'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_trasport));
	$sheet->setCellValue('F'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_cost_more));
	$sheet->setCellValue('G'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_cost_less));
	$sheet->setCellValue('H'.($i), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));
} // end if(!empty($results))

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/
