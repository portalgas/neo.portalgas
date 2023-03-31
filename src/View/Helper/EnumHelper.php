<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class EnumHelper extends Helper
{
	private $debug = false;

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function draw($value, $enums) {

    	$html = '';	

        foreach($enums as $key => $enum) {
            if($key==$value)
                $html = $enum;
        }
   
		return $html;
    }           
}