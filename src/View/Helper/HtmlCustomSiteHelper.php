<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteHelper extends FormHelper
{
	private $debug = false;
	public  $helpers = ['Url', 'Html', 'Form', 'HtmlCustom'];

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
                $results = ("mapping type [".$mapping->mapping_type->code."] non consentito");
            break;
        }

        return $results;
    }       

    public function boxOrder($results, $options=[]) {

        if(empty($results))
            return '';

        if($results->delivery->sys=='N')
            $delivery_label = $results->delivery->luogo.' '.$results->delivery->luogo;
        else 
            $delivery_label = $results->delivery->luogo;

        $url = '';
        if(!empty($results->suppliers_organization->supplier->img1)) {
            $config = Configure::read('Config');
            $img_path = sprintf(Configure::read('Supplier.img.path.full'), $results->suppliers_organization->supplier->img1);
            $portalgas_app_root = $config['Portalgas.App.root'];
            $path = $portalgas_app_root.$img_path;
    
            if(file_exists($path)) {
                $portalgas_fe_url = $config['Portalgas.fe.url'];
                $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $results->suppliers_organization->supplier->img1);
            }   
        }

        $html = '<section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">'.__('Order-'.$results->order_type_id).'</h3>
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
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('SupplierOrganization').'</label>
                        <div class="col-sm-4">
                            <a href="https://neo.portalgas.it/site/produttore/'.$results->suppliers_organization->supplier->slug.'" target="_blank" title="vai al sito del produttore">'.$results->suppliers_organization->name.'</a></div>
                        
                        <label class="col-sm-2 control-label" style="padding-top:0px"></label>
                        <div class="col-sm-4">';
            if(!empty($url)) 
                $html .= '<img src="'.$url.'" alt="'.$results->suppliers_organization->name.'" title="'.$results->suppliers_organization->name.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" />';

                $html .= '<div class="box-owner">'.__('organization_owner_articles').': <span class="label label-info">'.__('ArticlesOwner'.$results->suppliers_organization->owner_articles).'</span>';
                        
                $html .= '</div>
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

    public function boxSupplierOrganization($results, $options=[]) {

        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Supplier.img.path.full'), $results->supplier->img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $html = '';
        $html .= '<div class="box-supplier-organization">';
        // $html .= $results->id;
        if(!empty($results->supplier->img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $results->supplier->img1);

            $html .= '<span class="box-img"><img src="'.$url.'" alt="'.$results->supplier->name.'" title="'.$results->supplier->name.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" /></span> ';
        }
        $html .= '<span class="box-name">';
        $html .= '<a href="https://neo.portalgas.it/site/produttore/'.$results->suppliers_organization->supplier->slug.'" target="_blank" title="vai al sito del produttore">';
        $html .= $results->name;
        $html .= '</a>'; 
        $html .= '</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxArticleImg($results, $options=[]) {

        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Article.img.path.full'), $results->article->organization_id, $results->article->img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $html = '';
        $html .= '<div class="box-article-img">';
        // $html .= $results->id;
        if(!empty($results->article->img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $url = sprintf($portalgas_fe_url.Configure::read('Article.img.path.full'), $results->article->organization_id, $results->article->img1);

            $html .= '<span class="box-img"><img src="'.$url.'" alt="'.$results->article->name.'" title="'.$results->article->name.'" width="'.Configure::read('Article.img.preview.width').'" class="img-article" /></span> ';
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
    public function boxVerticalSupplierOrganizationreferents($results, $options=[]) {

        if(isset($options['pdf_img_path']))
            $img_path = $options['pdf_img_path'];
        else
            $img_path = '/img';

        $html = '';
        $html .= '<dl class="row">';
        foreach ($results as $referent) {

            $html .= '<dt class="col-sm-3">';
            if($referent['type']!='referente')
                $html .= '('.$referent['type'].') ';
            $html .= $referent['name'].' ';
            $html .= '</dt>';

            $html .= '<dd class="col-sm-9">';
            if(!empty($referent['email']))
                $html .= '&nbsp;'.$this->HtmlCustom->mail($referent['email']);   

            if(isset($referent['phone_satispay'])) {
                $html .= '&nbsp;'.$referent['phone_satispay'];
                $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />';
            }
            else 
            if(isset($referent['phone'])) 
                $html .= '&nbsp;'.$referent['phone'];
           
            $html .= '</dd>';
        }
        $html .= '</dl>';

        return $html;
    }

    public function boxOrizontalSupplierOrganizationreferents($results, $options=[]) {

        if(isset($options['pdf_img_path']))
            $img_path = $options['pdf_img_path'];
        else
            $img_path = '/img';

        $html = '';
        $html .= '<ul class="list-inline">';
        foreach ($results as $numResult => $referent) {

            $html .= '<li class="list-inline-item">';
            if($referent['type']!='referente')
                $html .= '('.$referent['type'].') ';
            $html .= $referent['name'].' ';

            if(!empty($referent['email']))
                $html .= '&nbsp;'.$this->HtmlCustom->mail($referent['email']);   

            if(isset($referent['phone_satispay'])) {
                $html .= '<span>';
                $html .= '&nbsp;'.$referent['phone_satispay'];
                $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />';
                $html .= '</span>';
            }
            else 
            if(isset($referent['phone'])) 
                $html .= '&nbsp;'.$referent['phone'];
           
            $html .= '</li>';

            if(isset($options['br'])) {
                if(($numResult%2)!=0)
                    $html .= '<br />';                
            }
        }
        $html .= '</ul>';

        return $html;
    }

    public function boxTitle($results, $breadcrumbs=[]) {

        if(!isset($results['title']))
            $results['title'] = ''; 

        $html = '';
        $html .= '<section class="content-header">';
        $html .= '<h1>';
        $html .= __($results['title']);
        if(isset($results['subtitle']) && !empty($results['subtitle'])) {
            $html .= '<small>';
            $html .= __($results['subtitle']);
            $html .= '</small>';
        }            
        $html .= '</h1>';

        if(!empty($breadcrumbs)) {
            $html .= '<ol class="breadcrumb">';
            foreach($breadcrumbs as $breadcrumb) {
                if(is_string($breadcrumb)) {
                    switch($breadcrumb) {
                        case 'home':
                            $html .= '<li><a href="'.$this->Url->build('/').'"><i class="fa fa-home"></i> '.__('Home').'</a></li>';
                        break;
                        case 'list':
                            $html .= '<li><a href="'.$this->Url->build(['action' => 'index']).'"><i class="fa fa-list"></i> '.__('List').'</a></li>';
                        break;
                    }
                }
            }
            $html .= '</ol>';
        }

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

    public function drawSupplierImage($supplier, $options=[]) {

        $html = '';
        if(!empty($supplier->img1)) {

            if(isset($options['max-width']))
                $max_width = $options['max-width'];
            else 
                $max_width = '100px';

            $config = Configure::read('Config');
            $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
            $this->_portalgas_app_root = $config['Portalgas.App.root'];
            $img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $supplier->img1);
            $img_path_supplier = $this->_portalgas_app_root . $img_path_supplier;
    
            $url = '';
            if(file_exists($img_path_supplier)) {
                $url = sprintf($this->_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $supplier->img1);
                $html .= '<img style="'.$max_width.'" src="'.$url.'" title="'.$supplier->name.'" alt="'.$supplier->name.'" />';
            }
        }
        return $html;
    }
}