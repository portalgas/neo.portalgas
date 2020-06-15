<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersHelper extends FormHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function factory($scope, $debug=false) {

        $helper = '';

        switch (strtoupper($scope)) {
            case 'DES-TITOLARE':
            case 'DES':
                $helper = 'HtmlCustomSiteOrdersDes';
                break;
            case 'GAS':
                $helper = 'HtmlCustomSiteOrdersGas';
                break;
            case 'PROMOTION':
                $helper = 'HtmlCustomSiteOrdersPromotion';
                break;
            case 'PACT-PRE':
                $helper = 'HtmlCustomSiteOrdersPactPre';
                break;
            case 'PACT':
                $helper = 'HtmlCustomSiteOrdersPact';
                break;
            
            default:
                die('HtmlCustomSiteHelper scope ['.$scope.'] non previsto');
                break;
        }

        if($debug) debug($helper);
        
        return $helper;
    }     
}