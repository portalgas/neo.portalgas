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

    public function translateMappingTypeCode($mapping, $options=[]) {

    	$results = '';
        switch($mapping->mapping_type->code) {
            case 'CURRENTDATE':
              $results = date('Y-m-d');
            break;
            case 'CURRENTDATETIME':
              $results = date('Y-m-d H:i:s');
            break;
            case 'FUNCTION':
                if(!empty($mapping->master_json_xpath))
                    $results .= 'Da <b>json</b> '.$mapping->master_json_xpath.' => ';
                else            
                if(!empty($mapping->master_xml_xpath))
                    $results .= 'Da <b>xml</b> '.$mapping->master_xml_xpath.' => ';
                else         
                if(!empty($mapping->master_csv_num_col))
                    $results .= 'Da <b>col</b> '.$mapping->master_csv_num_col.' => ';

                $results .= '<b>Function</b> '.$mapping->value;
            break;
            case 'DEFAULT':
                if(!empty($mapping->master_json_xpath))
                    $results = '<b>json</b> '.$mapping->master_json_xpath;
                else
                if(!empty($mapping->master_xml_xpath))
                    $results = '<b>xml</b> '.$mapping->master_xml_xpath;
                else
                if(!empty($mapping->master_csv_num_col))
                    $results = '<b>col</b> '.$mapping->master_csv_num_col;
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

    public function boxOrder($results, $options=[]) {

        $html = '';
        $html .= '<div class="box-order">';

        if($results->delivery->sys=='N')
            $delivery_label = $results->delivery->luogo.' '.$results->delivery->luogo;
        else 
            $delivery_label = $results->delivery->luogo;

        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';  
        $html .= '<select name="delivery_id" id="delivery_id" class="form-control">';
        $html .= '<option value="'.$results->delivery_id.'">'.$delivery_label.'</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<select name="order_id" id="order_id" class="form-control">';
        $html .= '<option value="'.$results->id.'">Dal '.$results->data_inizio.' al '.$results->data_fine;
        $html .= '</option>';
        $html .= '</select>';
        $html .= '</div>';

        //$html .= '<div class="col-md-6">';
        //$html .= __('StateOrder');
        // echo $this->App->utilsCommons->getOrderTime($results['Order']);
        //$html .= '</div>';
        $html .= '<div class="col-md-12">';
        // $html .= __('StatoElaborazione');
        $html .= $results->order_state_code->name.': '.$results->order_state_code->descri;
        // $html .= $this->App->drawOrdersStateDiv($results);
        $html .= '</div>';
        $html .= '</div>'; // row 

        $html .= "</div>"; // box-order

        return $html;
    }

    public function boxSupplierOrganization($results, $options=[]) {

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];
        $url = $portalgas_fe_url.Configure::read('Supplier.img.path.full');

        $html = '';
        $html .= '<div class="box-supplier-organization">';
        // $html .= $results->id;
        if(!empty($results->supplier->img1)) {
            $img1_path = sprintf($url, $results->supplier->img1);
            $html .= '<span class="box-img"><img src="'.$img1_path.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" /></span> ';
        }
        $html .= '<span class="box-name">'.$results->name.'</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxArticleImg($results, $options=[]) {

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];
        $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

        $html = '';
        $html .= '<div class="box-article-img">';
        // $html .= $results->id;
        if(!empty($results->article->img1)) {
            $img1_path = sprintf($url, $results->article->organization_id, $results->article->img1);
            $html .= '<span class="box-img"><img src="'.$img1_path.'" width="'.Configure::read('Article.img.preview.width').'" class="img-article" /></span> ';
        }
        $html .= '<span class="box-name">'.$results->name.'</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxArticleImgOnly($results, $options=[]) {

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];
        $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

        $html = '';
        $html .= '<div class="box-article-img">';
        // $html .= $results->id;
        if(!empty($results->article->img1)) {
            $img1_path = sprintf($url, $results->article->organization_id, $results->article->img1);
            $html .= '<span class="box-img"><img src="'.$img1_path.'" class="img-article" /></span> ';
        }
        $html .= "</div>";

        return $html;
    }

    /*
     * REFERENTI 
     */
    public function boxSupplierOrganizationreferents($results, $options=[]) {

        if(isset($options['pdf_img_path']))
            $img_path = $options['pdf_img_path'];
        else
            $img_path = '/img';

        $html = '';
        $html .= '<ul class="list-referents">';
        foreach ($results as $referent) {
            
            $html .= '<li>';
            if($referent->type!='REFERENTE')
                $html .= '('.strtolower($referent->type).') ';
            $html .= $referent->user->name.' ';
            if(!empty($referent->user->email))
                $html .= $this->HtmlCustom->mail($referent->user->email);   
            // debug($referent->user->user_profiles);
            foreach ($referent->user->user_profiles as $user_profile) {
                if($user_profile->profile_key=='profile.phone' && $user_profile->profile_value!='')
                    $html .= ' - '.$user_profile->profile_value.' - '; 
                if($user_profile->profile_key=='profile.satispay' && $user_profile->profile_value=='Y')
                    $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />'; 
                if($user_profile->profile_key=='profile.satispay_phone' && $user_profile->profile_value=='Y')
                    $html .= ' - '.$user_profile->profile_value.' - '; 
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;

    }

    public function boxTitle($results, $options=[]) {

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

    public function orderPriceTypes($price_type_enums, $options=[]) {

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