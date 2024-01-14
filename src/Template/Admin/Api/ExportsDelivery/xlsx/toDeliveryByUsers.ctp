<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\IOFactory;

$delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($delivery, ['year'=> true]);
$delivery_data = $this->HtmlCustomSite->excelSanify($this->HtmlCustomSite->drawDeliveryDateLabel($delivery));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('B1', __('Delivery')); 
$sheet->setCellValue('C1', $delivery_label); 
$sheet->setCellValue('D1', $delivery_data);  

$sheet->setCellValue('B2', __('User')); 
if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y')
	$sheet->setCellValue('C2', __('Contacts')); 

if($opts['users_detail_orders']=='Y') 
	$sheet->setCellValue('D2', __('SupplierOrganization'));
else
	$sheet->setCellValue('D2', '');
$sheet->setCellValue('E2', __('Total Carts'));
$sheet->setCellValue('F2', __('Trasport'));
$sheet->setCellValue('G2', __('CostMore'));
$sheet->setCellValue('H2', __('CostLess'));
$sheet->setCellValue('I2', __('Importo totale ordine'));

$num_row=3;
foreach($results as $numResult => $result) {

	$sheet->setCellValue('A'.($num_row), ($numResult+1));
	$sheet->setCellValue('B'.($num_row), $result['user']['name']);
	if(isset($opts['users_contacts']) && $opts['users_contacts']=='Y') 
		$sheet->setCellValue('C'.($num_row), $result['user']['email'].' '.$result['user']['phone']);
	
	if($opts['users_detail_orders']=='Y')
	foreach($result['orders'] as $order) {
		$sheet->setCellValue('D'.($num_row), $order['order']->suppliers_organization->name);
		$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['tot_importo_only_cart']));		
		$sheet->setCellValue('F'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_trasport']));		
		$sheet->setCellValue('G'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_cost_more']));		
		$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['importo_cost_less']));		
		$sheet->setCellValue('I'.($num_row), $this->HtmlCustomSiteExport->excelImporto($order['tot_importo']));
		
		$num_row++;
	} //end foreach($result['orders'] as $order) 

	if($opts['users_detail_orders']=='Y')
		$sheet->setCellValue('D'.($num_row), __('Total user'));			
	$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_importo_only_cart']));	
	$sheet->setCellValue('F'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_trasport']));	
	$sheet->setCellValue('G'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_cost_more']));	
	$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_cost_less']));	
	$sheet->setCellValue('I'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_importo']));		

	$num_row++;
	
} // foreach($results as $result)

$num_row++;
$sheet->setCellValue('D'.($num_row), __('Total delivery'));
$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_only_cart));
$sheet->setCellValue('F'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_trasport));
$sheet->setCellValue('G'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_cost_more));
$sheet->setCellValue('H'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_cost_less));
$sheet->setCellValue('I'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/