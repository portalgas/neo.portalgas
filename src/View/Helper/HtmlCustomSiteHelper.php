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

   // public function orderPriceTypes($priceTypes, $priceTypeEnums) {
 public function orderPriceTypes($price_type_enums) {

        $html = '';
        $html .= '<div class="panel panel-primary"><div class="panel-heading">';    
        $html .= '<h3 class="panel-title">'.__('priceTypes').'</h3></div></div>';
        $html .= '<div class="box_order_price_types" id="vue-order-price-types" style="display:none;">';     
        $html .= '<div class="row">';
        $html .= '<div class="col-xs-3">';
        $html .= '<label for="name">'.__('Name').'</label>';
        $html .= '  <input v-model="row.name" placeholder="name" type="text" class="form-control" id="name">';
        $html .= '</div>';

        $html .= '<div class="col-xs-3">';
        $html .= '<label for="name">'.__('Price Type').'</label>';
        $html .= $this->Form->radio('type', $price_type_enums, ['v-model' => 'row.type']);
        $html .= '</div>';

        $html .= '<div class="col-xs-3">';
        $html .= '<label for="date">'.__('Value').'</label>';
        $html .= '<input v-model="row.value" placeholder="value" type="text" class="form-control" />';
        $html .= '</div>';

        $html .= '<div class="col-xs-3">';
        $html .= '<button v-if="isFormValid" button type="button" class="btn btn-primary" @click="addTableRow">Add a row</button>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="width:30%">'.__('Name').'</th>';
        $html .= '<th style="width:30%">'.__('Price Type').'</th>';
        $html .= '<th style="width:30%">'.__('Value').'</th>';
        $html .= '<th style="width:10%"></th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        if(!empty($priceTypes))
        foreach($priceTypes as $numResult => $priceType) {

            $collaborator_id = $offer_detail_collaborator->collaborator_id;
            $ids .= $collaborator_id.',';

            $html .= '<tr>';
            $html .= '<td>'.$offer_detail_collaborator->collaborator->name.'</td>';
            $html .= '<td>';
            $html .= $this->Form->control('price_type_id['.$collaborator_id.']', 
                ['name' => 'offer_detail_collaborators.price_type_id['.$collaborator_id.']',
                'data-attr-id' => $collaborator_id,
                '@change' => 'tooglePriceCollaborator($event);',
                'options' => $priceTypes, 
                'default' => $offer_detail_collaborator->price_type_id, 
                'label' => false, 
                'hiddenField'=>false]);
            $html .= '</td>';
            $html .= '<td>';

            $options = ['name' => 'offer_detail_collaborators.price['.$collaborator_id.']',
                'id' => 'price-'.$collaborator_id, 
                'value' => $offer_detail_collaborator->price_collaborator, 
                'label' => false,
                'type' => 'text', 
                'class' => 'form-control importo'];
            if($offer_detail_collaborator->price_type_id!=Configure::read('PriceTypesCalc'))
                $options += ['style' => 'display: none;'];
            $html .= $this->Form->control('price['.$collaborator_id.']', $options);
            $html .= '</td>';
            $html .= '<td>';
            $html .= $this->Form->control('checkbox_collaborator_id['.$collaborator_id.']', 
                                             ['type' => 'checkbox', 
                                            'name' => 'checkbox_offer_detail_collaborator.id['.$collaborator_id.']', 
                                            'class' => 'checkbox_offer_detail_collaborator', 
                                            'style' => 'margin-left: 0px !important;',
                                            'value' => $collaborator_id, 
                                            'label' => false, 
                                            'checked' => 'checked']);
            $html .= '</td>';
            $html .= '</tr>';
               
        } // end foreach($offer_detail_collaborators as $numResult => $offer_detail_collaborator) 

        /*
         * riga aggiunta con Vue
         * ogni volta che si aggiunge una riga devo fare il bind per valorizzare i campo hidden con i checkbox scelti
         */
        $html .= '<tr v-for="(row, index) in rows">
                  <td>{{row.name}}</td>
                  <td>{{row.type}}</td>
                  <td>{{row.value}}</td>
                  <td>
                    <button type="button" class="text-center button btn-xs btn-danger" v-on:click="removeTableRow(index)">-</button>
                  </td>
                </tr>'; 

        $html .= '</tbody>';
        $html .= '</table></div>';

        $html .= '</div>';

        return $html;
    }    
}