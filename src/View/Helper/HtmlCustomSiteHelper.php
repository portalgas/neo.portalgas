<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteHelper extends FormHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function translateMappingTypeCode($mapping) {

    	$results = '';
        switch($mapping->mapping_type->code) {
            case 'CURRENTDATE':
              $results = date('Y-m-d');
            break;
            case 'CURRENTDATETIME':
              $results = date('Y-m-d H:i:s');
            break;
            case 'FUNCTION':
                $results = '<b>Function</b> '.$mapping->value;
            break;
            case 'DEFAULT':
                if(!empty($mapping->master_json_xpath))
                    $results = $mapping->master_json_xpath;
                else
                if(!empty($mapping->master_xml_xpath))
                    $results = $mapping->master_xml_xpath;
                else
                    $results = $mapping->value;
            break;
            case 'PARAMETER-EXT':
                $results = '<b>Param</b> '.$mapping->value;
            break;
            case 'INNER_TABLE_PARENT':
                if($mapping->has('queue_table'))
                   $results = $mapping->queue_table->table->name.' ('.$mapping->queue_table->table->id.')';
                else
                   $results = '<span class="label label-danger">dato inconsitente</span>'; 
            break;
            default:
                $results = ("mapping type [$mapping_type_code] non consentito");
            break;
        }

        return $results;
    }       

    public function boxSupplierOrganization($results) {

        $html = '';
        $html .= '<div class="box-supplier-organization">';
        // $html .= $results->id;
        if(!empty($results->supplier->img1)) {
            $img1_path = sprintf(Configure::read('Supplier.img.path.full'), $results->supplier->img1);
            $html .= '<span class="box-img"><img src="'.$img1_path.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" /></span> ';
        }
        $html .= '<span class="box-name">'.$results->name.'</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxTitle($results) {

        if(!isset($results['title']))
            $results['title'] = ''; 

        $html = '';
        $html .= '<section class="content-header">';
        $html .= '<h1>';
        $html .= $results['title'];
        if(isset($results['subtitle']) && !empty($results['subtitle'])) {
            $html .= '<small>';
            $html .= $results['subtitle'];
            $html .= '</small>';
        }            
        $html .= '</h1>';
        $html .= "</section>";

        return $html;
    }

    public function orderPriceTypes($price_type_enums) {

        $html = '';
        $html .= '<div class="panel panel-primary">';
        $html .= '<div class="panel-heading"><h3 class="panel-title">'.__('priceTypes').'</h3></div>';
        
        $html .= '<div class="panel-body box_order_price_types" id="vue-order-price-types" style="display:none;">'; 

        $html .= '<div v-if="spinner_run_type_prices === true" class="run run-type-prices"><div class="spinner"></div></div>'; 
        $html .= '<div v-if="spinner_run_type_prices === false">'; 

        /*
         * fields new row
         */ 
        $html .= '<div class="row" id="frm">';
        $html .= '<div class="col-xs-2">';
        $html .= '<label for="name">'.__('Name').'</label>';
        $html .= '  <input v-model="row.name" placeholder="name" type="text" class="form-control" />';
        $html .= '  <span class="text-danger" v-if="validationErrors.name" v-text="validationErrors.name"></span>';
        $html .= '</div>';
        $html .= '<div class="col-xs-3">';
        $html .= '<label for="name">'.__('Descri').'</label>';
        $html .= '  <textarea v-model="row.descri" class="form-control"></textarea>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<label for="name">'.__('Price Type').'</label>';
        $html .= $this->Form->radio('type', $price_type_enums, ['v-model' => 'row.type']);
        $html .= '  <span class="text-danger" v-if="validationErrors.type" v-text="validationErrors.type"></span>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<label for="date">'.__('Value').'</label>';
        $html .= '<input v-model="row.value" placeholder="value" type="text" class="form-control" />';
        $html .= '  <span class="text-danger" v-if="validationErrors.value" v-text="validationErrors.value"></span>';
        $html .= '</div>';

        $html .= '<div class="col-xs-1">';
        $html .= '<label for="date">'.__('Sort').'</label>';
        $html .= '<input v-model="row.sort" type="number" class="form-control"/>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<button button type="button" class="btn btn-primary" @click="addTableRow">'.__('Add row').'</button>';
        $html .= '</div>';
        $html .= '</div>'; // row

        /*
         * table
         */ 
        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>'.__('Name').'</th>';
        $html .= '<th>'.__('Descri').'</th>';
        $html .= '<th>'.__('Price Type').'</th>';
        $html .= '<th>'.__('Value').'</th>';
        $html .= '<th>'.__('Sort').'</th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        /*
         * riga aggiunta con Vue
         * ogni volta che si aggiunge una riga devo fare il bind per valorizzare i campo hidden con i checkbox scelti
         */
        $html .= '<tr v-for="(row, index) in rows">
                  <td>{{row.name}}</td>
                  <td>{{row.descri}}</td>
                  <td>{{row.type | priceTypeLabel}}</td>
                  <td>{{row.value}}</td>
                  <td>{{row.sort}}</td>
                  <td>
                    <a href="" class="text-center btn btn-danger" v-on:click="removeTableRow(index)"><i class="fa fa-trash"></i></a>
                    <input name="priceTypes.name[]" type="hidden" v-bind:value="row.name" />
                    <input name="priceTypes.descri[]" type="hidden" v-bind:value="row.descri" />
                    <input name="priceTypes.type[]" type="hidden" v-bind:value="row.type" />
                    <input name="priceTypes.value[]" type="hidden" v-bind:value="row.value" />
                    <input name="priceTypes.sort[]" type="hidden" v-bind:value="row.sort" />
                  </td>
                </tr>'; 

        $html .= '</tbody>';
        $html .= '</table></div>';

        $html .= '</div>'; // results
        $html .= '</div>'; // box_order_price_types
        $html .= '</div>'; // panel panel-primary

        return $html;
    }    
}