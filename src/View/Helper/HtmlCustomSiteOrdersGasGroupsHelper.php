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
        parent::initialize($config);
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

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        $options['ctrlDesACL'] = false;
        $options['empty'] = false; 
        $options['select2'] = false;            
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

        if($results->delivery->sys=='N')
            $delivery_label = $results->delivery->luogo.' '.$results->delivery->luogo;
        else 
            $delivery_label = $results->delivery->luogo;

        $html = '<section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Ordine principale</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div> <!-- /.box-header -->
            <div class="box-body no-padding-disabled">

                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('Delivery').'</label>
                        <div class="col-sm-4">'.$delivery_label.'</div>
                        
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('Order').'</label>
                        <div class="col-sm-4">Dal '.$results->data_inizio.' al '.$results->data_fine.'</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('StatoElaborazione').'</label>
                        <div class="col-sm-10">'.$results->order_state_code->name.': '.$results->order_state_code->descri.'</div>
                    </div>
                </div>

            </div>
        </div>
        </section>';

        return $html;   
    }

    public function monitoraggio($results) {
        return parent::monitoraggio($results);
    }

    public function typeGest($results) {
        return parent::typeGest($results);
    }    
}