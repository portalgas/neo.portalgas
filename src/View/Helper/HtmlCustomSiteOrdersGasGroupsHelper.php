<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasGroupsHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function hiddenFields($organization_id, $parent) {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $organization_id, 'required' => 'required']);

        // ordine del GAS 
        if(!empty($parent))
            $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $parent->id, 'required' => 'required']);
        
        return $html;
    }    

    public function supplierOrganizations($suppliersOrganizations) {
        return parent::supplierOrganizations($suppliersOrganizations);
    }

    public function gasGroups($gasGroups) {
        
    }
        
    public function deliveries($deliveries, $gasGroups) {
        $html = '';
        $html .= $this->Form->control('gas_group_id', ['options' => $gasGroups]);
        $html .= $this->Form->control('delivery_id', ['options' => $deliveries]);
        return $html;
    }

    public function data($parent) {

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '</div>';

        if(!empty($parent)) {
            $msg = "L'ordine si chiuderà il ".$this->HtmlCustom->data($parent->data_fine);

            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">'; 
            $html .= $this->HtmlCustom->alert($msg);
            $html .= '</div>'; 
            $html .= '</div>';    
        }

        return $html;
    }

    public function note() {
        return parent::note();     
    } 
    
    public function mailOpenTesto() {
        return parent::mailOpenTesto();     
    }  
    
    /*
     * dettaglio ordine padre
     */
    public function infoParent($results) {

        if(empty($results)) 
            return '';    
    }
}