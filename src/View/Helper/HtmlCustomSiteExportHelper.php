<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;
use App\Traits;

class HtmlCustomSiteExportHelper extends FormHelper
{
    use Traits\UtilTrait;

	private $debug = false;
	public  $helpers = ['Url', 'Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
        $config = Configure::read('Config');
    }

    public function toDeliveryBySuppliersAndCartsDrawUserTotale($user, $format, $opts) {

        if(empty($user))
            return '';

        $html = '';
        $html .= '<tr>';
        $html .= '<td colspan="3"></td>';
        if($format=='HTML')
            $html .= '<td></td>';
        $html .= '<td>'.__('Total user').'</td>';
        $html .= '<td class="text-center">';
        $html .= $user['tot_qta'];
        $html .= '</td>';
        $html .= '<td class="text-right">';
        $html .= $this->HtmlCustom->importo($user['tot_importo']).'&nbsp;&nbsp;&nbsp;';
        if(isset($user['importo_trasport']))
            $html .= '<br />Trasporto '.$this->HtmlCustom->importo($user['importo_trasport']).' +';
        if(isset($user['importo_cost_more']))
            $html .= '<br />Costo agg. '.$this->HtmlCustom->importo($user['importo_cost_more']).' +';
        if(isset($user['importo_cost_less']))
            $html .= '<br />Sconto '.$this->HtmlCustom->importo((-1 * $user['importo_cost_less'])).' -';
        
        $user_totale = $user['tot_importo'];
        if(isset($user['importo_trasport'])) {
            $user_totale += $user['importo_trasport']; 
        }
        if(isset($user['importo_cost_more'])) {
            $user_totale += $user['importo_cost_more']; 
        }
        if(isset($user['importo_cost_less'])) {
            $user_totale += $user['importo_cost_less']; 
        }                
        if($user_totale != $user['tot_importo']) {
            $html .= '<br />'.$this->excelImporto($user_totale).' =';
        }
        $html .= '</td>';
        if($opts['referent_modify']=='Y') 
            $html .= '<td colspan="5" class="evidence"></td>';
        $html .= '</tr>';	
        
        return $html;
    } 

    public function toExcelDeliveryBySuppliersAndCartsDrawUserTotale($sheet, $i, $user, $format, $opts) {
        
        if(empty($user))
            return $sheet;

        $totale = $this->excelImporto($user['tot_importo']);
        if(isset($user['importo_trasport']))
            $totale .= " \n".'Trasporto '.$this->excelImporto($user['importo_trasport']).' +';
        if(isset($user['importo_cost_more']))
            $totale .= " \n".'Costo agg. '.$this->excelImporto($user['importo_cost_more']).' +';
        if(isset($user['importo_cost_less']))
            $totale .= " \n".'Sconto '.$this->excelImporto((-1 * $user['importo_cost_less'])).' -';
        
        $sheet->setCellValue('A'.$i, '');
        $sheet->setCellValue('B'.$i, '');
        $sheet->setCellValue('C'.$i, '');
        $sheet->setCellValue('D'.$i, __('Total user'));
        $sheet->setCellValue('E'.$i, $user['tot_qta']);
        $sheet->setCellValue('F'.$i, $totale);

        return $sheet;
    } 
    
    public function excelImporto($value) {
    	$str = '';
    	if(!empty($value))
    		$str = number_format($value, 2, Configure::read('separatoreDecimali'), Configure::read('separatoreMigliaia'));
    	return $str;
    }    
}