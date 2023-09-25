<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasGroupsHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    // eventuale msg in index
    public function msg() {
        return '';
    }

    public function hiddenFields() {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $this->_parent->organization_id, 'required' => 'required']);

        // ordine del GAS 
        if(!empty($this->_parent))
            $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $this->_parent->id, 'required' => 'required']);
        
        return $html;
    }    

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = false;
        if(!isset($options['empty'])) $options['empty'] = false; 
        if(!isset($options['select2'])) $options['select2'] = false;             
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function gasGroups($gasGroups) {
        
    }
        
    public function deliveries($deliveries, $gasGroups=[]) {

        $results = [];
        $results['html'] = $this->Form->control('gas_group_id', ['options' => $gasGroups, 'empty' => Configure::read('HtmlOptionEmpty')]);
        $results['html'] .= '<div id="gas-group-deliveries" style="display:none">';
        $results['html'] .= $this->Form->control('delivery_id', ['options' => $deliveries, 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty')]);
        $results['html'] .='</div>';
        return $results;
    }

    public function data() {

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '</div>';

        if(!empty($this->_parent)) {
            $msg = "L'ordine si chiuderà il ".$this->HtmlCustom->data($this->_parent->data_fine);

            $html .= $this->Form->control('parent_data_fine', ['type' => 'hidden', 'value' => $this->_parent->data_fine]);

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
    public function infoParent() {

        if(empty($this->_parent))
            return '';

        if($this->_parent->delivery->sys=='N')
            $delivery_label = $this->_parent->delivery->luogo.' '.$this->_parent->delivery->luogo;
        else 
            $delivery_label = $this->_parent->delivery->luogo;

        $html = '<div class="box box-solid">
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
                        <div class="col-sm-4">Da '.$this->_parent->data_inizio->i18nFormat('eeee d MMMM').' a '.$this->_parent->data_fine->i18nFormat('eeee d MMMM').'</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('StatoElaborazione').'</label>
                        <div class="col-sm-10">
                            <div style="padding-left:45px;min-height:48px;" class="action orderStato'.$this->_parent->order_state_code->code.'" title="'.$this->_parent->order_state_code->name.'">'.$this->_parent->order_state_code->descri.'</div>
                        </div>
                    </div>
                </div>

            </div>
            </div>
        </div>';

        return $html;   
    }

    public function monitoraggio() {
        return parent::monitoraggio();
    }

    public function typeGest() {
        return parent::typeGest();
    }    
}