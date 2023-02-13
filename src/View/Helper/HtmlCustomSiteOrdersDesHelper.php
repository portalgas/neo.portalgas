<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersDesHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
    public $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    public function hiddenFields($organization_id, $parent) {
        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $organization_id, 'required' => 'required']);

        // ordine del DES 
        if(!empty($parent))
            $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $parent->id, 'required' => 'required']);
        
        return $html;
    }   

    /*
     * dettaglio ordine padre
     */
    public function infoParent($des_order) {
        if(empty($des_order))
            return '';

        $delivery_label = $des_order->luogo;

        $html = '<div class="box box-solid">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Ordine principale</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn btn-danger btn-sm btn-box-tool-disabled" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div> <!-- /.box-header -->
            <div class="box-body no-padding-disabled">

                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('Delivery').'</label>
                        <div class="col-sm-4">'.$delivery_label.'</div>
                    </div>
                    <div class="form-group">
                    ';

                    foreach($des_order->de->des_organizations as $des_organization) {                    
                        $url = sprintf($this->_portalgas_fe_url.Configure::read('Organization.img.path.full'), $des_organization->organization->img1);
                        $img = '<span class="box-img"><img src="'.$url.'" alt="'.$des_organization->organization->name.'" title="'.$des_organization->organization->name.'" width="'.Configure::read('Organization.img.preview.width').'" class="img-supplier" /></span> ';
                        $html .= '<label class="col-sm-2 control-label" style="padding-top:0px">'.__('G.A.S.').'</label>
                                    <div class="col-sm-10">'.$img.' '.$des_organization->organization->name;
                        if(count($des_order->des_orders_organizations)>0) {  
                            $found = false;
                            foreach($des_order->des_orders_organizations as $des_orders_organization) {
                                if($des_orders_organization->organization_id==$des_organization->organization->id) {
                                    $found = true; 
                                    $html .= ' <span class="label label-success">ordine già creato</span>';
                                }
                                if(!$found)
                                    $html .= ' <span class="label label-warning">ordine non ancora creato</span>';
                            }
                        }
                                
                        $html .= '</div>';
                    }


             
    $html .= '</div>
                </div>

            </div>
        </div>
        </div>';

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
            $msg = "L'ordine si chiuderà il ".$this->HtmlCustom->data($parent->data_fine_max);
            
            $html .= $this->Form->control('data_fine_max', ['type' => 'hidden', 'value' => $parent->data_fine_max]);

            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">'; 
            $html .= $this->HtmlCustom->alert($msg);
            $html .= '</div>'; 
            $html .= '</div>';    
        }

        return $html;
    }

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = false;
        if(!isset($options['empty'])) $options['empty'] = false; 
        if(!isset($options['select2'])) $options['select2'] = false;           
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries, $options=[]) {
        return parent::deliveries($deliveries, $options);
    }

    public function note() {
        return parent::note();     
    } 
    
    public function mailOpenTesto() {
        return parent::mailOpenTesto();     
    }
    
    public function monitoraggio($results) {
        return parent::monitoraggio($results);
    }

    public function typeGest($results) {
        return parent::typeGest($results);
    }    
}