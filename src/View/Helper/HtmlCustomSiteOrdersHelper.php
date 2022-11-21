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
    private static $GAS_GROUPS = 10;
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
            case self::$GAS_GROUPS:
                $helper = 'HtmlCustomSiteOrdersGasGroups';
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
        $html .= '<div class="col-md-10">'; 
        $html .= $this->Form->control('nota', ['type' => 'text', 'class' => 'form-control ctrl-length', 'maxlength' => Configure::read('OrderNotaMaxLen')]);
        $html .= '</div>'; 
        $html .= '<div class="col-md-2">'; 
        $html .= '<a title="clicca per ingrandire l\'immagine" class="img-helps img-fluid rounded float-right" href="" data-toggle="modal" data-target="#modalHelps" ';
        $html .= 'data-attr-title="Dove comparirà la nota che inserisci"';
        $html .= '><img class="img-responsive" src="/img/helps/orders-nota.png" /></a>';
        $html .= '</div>';   
        $html .= '</div>';   

        return $html;     
    } 
    
    public function mailOpenTesto() {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-10">'; 
        $html .= $this->Form->control('mail_open_testo');
        $html .= '</div>'; 
        $html .= '<div class="col-md-2">'; 
        $html .= '<a title="clicca per ingrandire l\'immagine" class="img-helps img-fluid rounded float-right" href="" data-toggle="modal" data-target="#modalHelps" ';
        $html .= 'data-attr-title="Il testo sarà aggiunto alla mail di notifica di apertura dell\'ordine e sarà visibile anche sul sito"';
        $html .= '><img class="img-responsive" src="/img/helps/orders-mail-open-testo.png" /></a>';
        $html .= '</div>';         
        $html .= '</div>';   

        return $html;     
    }    
}