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

    public function supplierOrganizations($suppliersOrganizations) {

        $html = '';
        $html .= $this->Html->script('vue/suppliersOrganization', ['block' => 'scriptPageInclude']);
        
        $html .= '<div class="row" id="vue-supplier-organization">';
        $html .= '<div class="col-md-8">';
        // echo $this->HtmlCustomSite->boxSupplierOrganization($suppliersOrganizations);
        $html .= $this->Form->control('supplier_organization_id', ['label' => __('Supplier Organization'), 'options' => $suppliersOrganizations, '@change' => 'getSuppliersOrganization']);
        $html .= '</div>';
        $html .= '<div class="col-md-4" style="display: none;" id="vue-supplier-organization-img">';
        $html .= '<div class="box-img" v-if="supplier_organization.supplier.img1!=\'\'"><img width="'.Configure::read('Supplier.img.preview.width').'" class="img-responsive-disabled userAvatar" v-bind:src="supplier_organization.img1" /></div>';
        $html .= '<div class="box-name">{{supplier_organization.name}}</div>';
        $html .= '<div class="box-owner">'.__('organization_owner_articles').': {{supplier_organization.owner_articles | ownerArticlesLabel}}</div>';
        $html .= '</div>';
        $html .= '</div>';        

        return $html;
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
        $html .= $this->Form->control('mail_open_testo', ['type' => 'textarea']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-2">'; 
        $html .= '<a title="clicca per ingrandire l\'immagine" class="img-helps img-fluid rounded float-right" href="" data-toggle="modal" data-target="#modalHelps" ';
        $html .= 'data-attr-title="Il testo sarà aggiunto alla mail di notifica di apertura dell\'ordine e sarà visibile anche sul sito"';
        $html .= '><img class="img-responsive" src="/img/helps/orders-mail-open-testo.png" /></a>';
        $html .= '</div>';         
        $html .= '</div>';   

        return $html;     
    }   
    
    /*
     * trasport / cost_more / cost_less
     */    
    public function extra($order, $parent) {

        if(!empty($order)) {
            // add 
            $hasTrasport = 'N';
            $trasport = 0;
            $hasCostMore = 'N';
            $costMore = 0;
            $hasCostLess = 'N';
            $costLess = 0;

            $disabled = false;
        }
        else {
            // edit
            $hasTrasport = $order->hasTrasport;
            $trasport = $order->trasport;
            $hasCostMore = $order->hasCostMore;
            $costMore = $order->costMore;
            $hasCostLess = $order->hasCostLess;
            $costLess = $order->costLess;          
            
            $disabled = false;
        }

        if(!empty($parent)) {
            $hasTrasport = $parent->hasTrasport;
            $trasport = $parent->trasport;
            $hasCostMore = $parent->hasCostMore;
            $costMore = $parent->costMore;
            $hasCostLess = $parent->hasCostLess;
            $costLess = $parent->costLess;

            $disabled = true;
        }

        $html = '';
        $html .= '<div class="row">';

        $html .= '<div class="col-md-1">';
        $html .= $this->Form->label('Trasport', ['label' => __('CostTrasport')]);
        $html .= '</div>';  
        $html .= '<div class="col-md-1">'; 
        $html .= $this->Form->radio('hasTrasport', ['Y' => 'Si', 'N' => 'No'], ['default' => $hasTrasport, 'disabled' => $disabled]);
        $html .= '</div>'; 
        $html .= '<div class="col-md-1">'; 
        if($hasTrasport=='Y' && $trasport>0)
            $html .= $this->Form->control('trasport', ['label' => false, 'disabled']);
        $html .= '</div>'; 

        $html .= '<div class="col-md-1">';
        $html .= $this->Form->label('CostMore', ['label' => __('CostMore')]);
        $html .= '</div>';  
        $html .= '<div class="col-md-1">'; 
        $html .= $this->Form->radio('hasCostMore', ['Y' => 'Si', 'N' => 'No'], ['default' => $hasCostMore, 'disabled' => $disabled]);
        $html .= '</div>'; 
        $html .= '<div class="col-md-1">'; 
        if($hasCostMore=='Y' && $costMore>0)
            $html .= $this->Form->control('cost_more', ['label' => false, 'disabled']);
        $html .= '</div>'; 

        $html .= '<div class="col-md-1">';
        $html .= $this->Form->label('CostLess', ['label' => __('CostLess')]);
        $html .= '</div>';  
        $html .= '<div class="col-md-1">'; 
        $html .= $this->Form->radio('hasCostLess', ['Y' => 'Si', 'N' => 'No'], ['default' => $hasCostLess, 'disabled' => $disabled]);
        $html .= '</div>'; 
        $html .= '<div class="col-md-1">'; 
        if($hasCostLess=='Y' && $costLess>0)
            $html .= $this->Form->control('cost_less', ['label' => false, 'disabled']);
        $html .= '</div>'; 

        $html .= '</div>'; 

        return $html;        

    }
}