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
            $html .= '<span class="box-img"><img src="'.$img1_path.'" width="'.Configure::read('Supplier.img.preview.width').'" /></span> ';
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
}