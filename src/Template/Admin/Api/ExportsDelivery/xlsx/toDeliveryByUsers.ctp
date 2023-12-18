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

$sheet->setCellValue('A2', __('User')); 
if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y')
	$sheet->setCellValue('B2', __('Contacts')); 

$sheet->setCellValue('C2', __('SupplierOrganization'));
$sheet->setCellValue('D2', __('Total Carts'));
$sheet->setCellValue('E2', __('Trasport'));
$sheet->setCellValue('F2', __('CostMore'));
$sheet->setCellValue('G2', __('CostLess'));
$sheet->setCellValue('H2', __('Importo totale ordine'));

$num_row=3;
foreach($results as $result) {

	$sheet->setCellValue('A'.($num_row), $result['user']['name']);
	if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y') 
		$sheet->setCellValue('B'.($num_row), $result['user']['email'].' '.$result['user']['phone']);
	
	foreach($result['orders'] as $order) {
		$sheet->setCellValue('C'.($num_row), $order['order']->suppliers_organization->name);
		$sheet->setCellValue('D'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['tot_importo_only_cart']));		
		$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_trasport']));		
		$sheet->setCellValue('f'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_cost_more']));		
		$sheet->setCellValue('G'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_cost_less']));		
		$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['tot_importo']));
		
		$num_row++;
	} //end foreach($result['orders'] as $order) 

	$sheet->setCellValue('G'.($num_row), __('Total user'));		
	$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_importo']));		

	$num_row++;
	
} // foreach($results as $result)

$num_row++;
$sheet->setCellValue('G'.($num_row), __('Total delivery'));
$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/