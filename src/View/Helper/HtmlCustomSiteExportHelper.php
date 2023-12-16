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

    public function toDeliveryBySuppliersAndCartsDrawUserTotale($user, $user_id, $format, $opts) {
        $html = '';
        $html .= '<tr>';
        $html .= '<td colspan="3"></td>';
        if($format=='HTML')
            $html .= '<td></td>';
        $html .= '<td>'.__('Total user').'</td>';
        $html .= '<td class="text-center">';
        $html .= $user[$user_id]['tot_qta'];
        $html .= '</td>';
        $html .= '<td class="text-right">';
        $html .= $this->HtmlCustom->importo($user[$user_id]['tot_importo']).'&nbsp;&nbsp;&nbsp;';
        if(isset($user[$user_id]['importo_trasport']))
            $html .= '<br />Trasporto '.$this->HtmlCustom->importo($user[$user_id]['importo_trasport']).' +';
        if(isset($user[$user_id]['importo_cost_more']))
            $html .= '<br />Costo agg. '.$this->HtmlCustom->importo($user[$user_id]['importo_cost_more']).' +';
        if(isset($user[$user_id]['importo_cost_less']))
            $html .= '<br />Sconto '.$this->HtmlCustom->importo($user[$user_id]['importo_cost_less']).' -';
        
        $user_totale = $user[$user_id]['tot_importo'];
        if(isset($user[$user_id]['importo_trasport'])) {
            $user_totale += $user[$user_id]['importo_trasport']; 
        }
        if(isset($user[$user_id]['importo_cost_more'])) {
            $user_totale += $user[$user_id]['importo_cost_more']; 
        }
        if(isset($user[$user_id]['importo_cost_less'])) {
            $user_totale -= $user[$user_id]['importo_cost_less']; 
        }                
        if($user_totale != $user[$user_id]['tot_importo']) {
            $html .= '<br />'.$this->HtmlCustom->importo($user_totale).' =';
        }
        $html .= '</td>';
        if($opts['referent_modify']=='Y') 
            $html .= '<td colspan="5" class="evidence"></td>';
        $html .= '</tr>';	
        
        return $html;
    }     
}