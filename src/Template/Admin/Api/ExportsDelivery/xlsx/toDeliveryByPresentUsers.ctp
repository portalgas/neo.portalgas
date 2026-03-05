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
if(isset($opts['users_present_contacts']) && $opts['users_present_contacts']=='Y')
	$sheet->setCellValue('C2', __('Contacts')); 

if(isset($opts['users_detail_importo']) && $opts['users_detail_importo']=='Y') 
	$sheet->setCellValue('D2', __('ImportoTotaleOrdine'));
else
	$sheet->setCellValue('D2', '');
if(isset($opts['users_detail_qta']) && $opts['users_detail_qta']=='Y') 
	$sheet->setCellValue('E2', __('QuantitaTotaleOrdine'));
else
	$sheet->setCellValue('E2', '');
	

$num_row=3;
foreach($results as $numResult => $result) {

	$sheet->setCellValue('A'.($num_row), ($numResult+1));
	$sheet->setCellValue('B'.($num_row), $result['user']['name']);
	if(isset($opts['users_present_contacts']) && $opts['users_present_contacts']=='Y') 
		$sheet->setCellValue('C'.($num_row), $result['user']['email'].' '.$result['user']['phone'].' '.$result['user']['address']);
	
	if(isset($opts['users_detail_importo']) && $opts['users_detail_importo']=='Y')			
		$sheet->setCellValue('D'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_importo']));	
	if(isset($opts['users_detail_qta']) && $opts['users_detail_qta']=='Y')	
		$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($result['user']['tot_user_qta']));	

	$num_row++;
	
} // foreach($results as $result)

$num_row++;
if(isset($opts['users_detail_importo']) && $opts['users_detail_importo']=='Y')	
	$sheet->setCellValue('D'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_importo));
if(isset($opts['users_detail_qta']) && $opts['users_detail_qta']=='Y')	
	$sheet->setCellValue('E'.($num_row), $this->HtmlCustomSiteExport->excelImporto($delivery_tot_qta));

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
/*
$stream = new CallbackStream(function () use ($writer) {
	$writer->save('php://output');
});*/