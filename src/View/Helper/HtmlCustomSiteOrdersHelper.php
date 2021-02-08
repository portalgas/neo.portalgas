<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersHelper extends FormHelper
{
    private static $GAS = 1;
    private static $DES_TITOLARE = 2;
    private static $DES = 3;
    private static $PROMOTION = 4;
    private static $PACT_PRE = 5;
    private static $PACT = 6;
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function factory($order_type_id, $debug=false) {

        $helper = '';

        switch (strtoupper($order_type_id)) {
            case self::$GAS:
                $helper = 'HtmlCustomSiteOrdersGas';
                break;
            case self::$DES:
            case self::$DES_TITOLARE:
                $helper = 'HtmlCustomSiteOrdersDes';
                break;
            case self::$PROMOTION:
                $helper = 'HtmlCustomSiteOrdersPromotion';
                break;
            case self::$PACT_PRE:
                $helper = 'HtmlCustomSiteOrdersPactPre';
                break;
            case self::$PACT:
                $helper = 'HtmlCustomSiteOrdersPact';
                break;
            default:
                die('HtmlCustomSiteHelper order_type_id ['.$order_type_id.'] non previsto');
                break;
        }

        if($debug) debug($helper);
        
        return $helper;
    } 

    public function note() {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">'; 
        $html .= $this->Form->control('nota');
        $html .= '</div>'; 
        $html .= '</div>';   

        return $html;     
    } 
    
    public function mailOpenTesto() {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">'; 
        $html .= $this->Form->control('mail_open_testo');
        $html .= '</div>'; 
        $html .= '</div>';   

        return $html;     
    }    
}