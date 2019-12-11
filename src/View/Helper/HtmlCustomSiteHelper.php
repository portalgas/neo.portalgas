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
}