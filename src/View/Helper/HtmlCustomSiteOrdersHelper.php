<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;
use App\Traits;
class HtmlCustomSiteOrdersHelper extends FormHelper
{
    use Traits\HelperTrait;

    private static $GAS = 1;
    private static $DES_TITOLARE = 2;
    private static $DES = 3;
    private static $PROMOTION = 4;
    private static $PACT_PRE = 5;
    private static $PACT = 6;
    private static $GAS_GROUPS = 10;
    private static $GAS_PARENT_GROUPS = 11;

    protected $_portalgas_app_root = '';
    protected $_portalgas_fe_url = '';
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        $config = Configure::read('Config');
        $this->_portalgas_app_root = $config['Portalgas.App.root'];
        $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
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
            case self::$GAS_PARENT_GROUPS:
                $helper = 'HtmlCustomSiteOrdersGasParentGroups';
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

    public function hiddenFields($organization_id, $parent) {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $organization_id, 'required' => 'required']);
    
        return $html;
    }    

    /*
     * dettaglio ordine padre
     */
    public function infoParent($results) {
        return '';    
    }
    
    public function data($parent) {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '</div>';

        if(!empty($parent)) {
            $msg = "L'ordine si chiuderà il ".$this->HtmlCustom->data($parent->data_fine);

            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">'; 
            $html .= $this->HtmlCustom->alert($msg);
            $html .= '</div>'; 
            $html .= '</div>';    
        }

        return $html;
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
    
    public function monitoraggio($results) {

        $qta_massima_um_options = ['KG' => 'Kg (prenderà in considerazione anche i Hg, Gr)', 
                                    'LT' => 'Lt (prenderà in considerazione anche i Hl, Ml)', 
                                    'PZ' => 'Pezzi'];
        $qta_massima_um = 'KG';

        $html = '
            <section><div class="box-header with-border"><h3 class="box-title">Monitoraggio</h3></div>
            <div class="row">
                <div class="col-md-3">
                '.$this->Form->control('qta_massima', ['label' => 'Quantità massima', 'type' => 'number', 'min' => 0]).'
                </div>
                <div class="col-md-4">
                '.$this->Form->input('qta_massima_um', ['id' => 'qta_massima_um', 'label' => 'UM', 'options' => $qta_massima_um_options, 'default' => $qta_massima_um, 'required' => 'false']).'
                </div>
                <div class="col-md-5"> 
                    <br /><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#monitoraggio-qta_massima">
                        <i class="fa fa-2x fa-envelope" aria-hidden="true"></i></button>
                    '.$this->modal('monitoraggio-qta_massima', 'Monitoraggio quantità massima', "Quando il peso totale espresso nell'unità di misura indicata raggiungerà la quantità indicata, verrà inviata una mail ai referenti e chiuso l'ordine").'
                </div>         
            </div> <!-- row --> 
            <div class="row">
                <div class="col-md-3">
                '.$this->Form->label('importo_massimo', ['label' => 'Importo massimo']).'
                '.$this->Form->control('importo_massimo', ['label' => false, 'type' => 'number', 'min' => 0,
                                                            'templates' => [
                                                                'inputContainer' => '<div class="input-group">{{content}}<div class="input-group-btn">
                                                                <button class="btn btn-default" type="button">
                                                                <i class="fa fa-eur"></i>
                                                                </button>
                                                            </div>
                                                            </div>']]).'
                </div>
                <div class="col-md-4">
                </div>                
                <div class="col-md-5"> 
                    <br /><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#monitoraggio-importo_massimo">
                        <i class="fa fa-2x fa-envelope" aria-hidden="true"></i></button>
                        '.$this->modal('monitoraggio-importo_massimo', 'Monitoraggio importo massimo', "Quando il totale degli acquisti raggiungerà l'importo indicato, verrà inviata una mail ai referenti e chiuso l'ordine").'
                </div>         
            </div> <!-- row -->  
            <section>'; 

        return $html;
    }
    
    public function typeGest($results) {

        $typeGests = [];
        $i=0;
        $typeGests[$i]['img'] = '';
        $typeGests[$i]['label'] = 'Nessuno di questi';
        $typeGests[$i]['value'] = '';
        $i++;
        $typeGests[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kexi.png';
        $typeGests[$i]['label'] = "Gestisci gli acquisti aggregati per l'importo degli utenti";
        $typeGests[$i]['value'] = 'AGGREGATE';
        $typeGests[$i]['modal_title'] = "Esempio: Gestisci gli acquisti aggregati per l'importo degli utenti";
        $typeGests[$i]['modal_body'] = '<div class="table-responsive"><table class="table"><tbody><tr><th>Gasista</th><th>ha ordinato</th><th>con l\'importo</th><th>Gestito la somma degli importi</th></tr><tr><td rowspan="2">Rossi Mario</td><td>2 orate</td><td>10,00&nbsp;€</td><td rowspan="2">10,00&nbsp;€ + 5,00&nbsp;€ = <b>15,00</b>&nbsp;€</td></tr><tr><td>1 branzino</td><td>5,00&nbsp;€</td></tr></tbody></table></div>';
        $i++;
        $typeGests[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kexi_split.png';
        $typeGests[$i]['label'] = "Gestisci gli acquisti dividendo le quantità di ogni acquisto";
        $typeGests[$i]['value'] = 'SPLIT';
        $typeGests[$i]['modal_title'] = "Esempio: Gestisci gli acquisti dividendo le quantità di ogni acquisto";
        $typeGests[$i]['modal_body'] = '<div class="table-responsive"><table class="table"><tbody><tr><th>Gasista</th><th>ha ordinato</th><th>con l\'importo</th><th>Gestito ogni singola quantità</th></tr><tr><td rowspan="2">Rossi Mario</td><td rowspan="2"><b>2</b> orate</td><td rowspan="2">10,00&nbsp;€</td><td><b>1</b> orate&nbsp;&nbsp;....&nbsp;€</td></tr><tr><td><b>1</b> orata&nbsp;&nbsp;....&nbsp;€</td></tr></tbody></table></div>';

        $html = ''; 
        $html .= '<section><div class="box-header with-border"><h3 class="box-title">Tipologia di gestione</h3></div>';

        foreach($typeGests as $typeGest) {
            $html .= '
                <div class="row" style="padding:5px 25px;border-bottom:1px solid #ddd;">
                    <div class="col-6 col-md-6">
                        <div class="radio">
                          <label><input type="radio" name="typeGest" value="'.$typeGest['value'].'" checked />'.$typeGest['label'].'</label>
                        </div>        
                    </div>
                    <div class="col-6 col-md-6">';
            if(isset($typeGest['modal_title'])) {

                $options['size'] = 'modal-lg';

                $html .= '
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#typeGest-'.$typeGests[$i]['value'].'">
                        <i class="fa fa-2x fa-info-circle" aria-hidden="true"></i> Clicca per visualizzare l\'esempio
                    </button>
                    '.$this->modal('typeGest-'.$typeGest['value'], $typeGest['modal_title'], $typeGest['modal_body'], $options);
            }
            $html .= '</div> <!-- col-6 col-md-6 -->
                </div> <!-- row --> ';
        }
        $html .= '</section>';

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

        $extras = [];
        $i=0;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/ark2.png';
        $extras[$i]['label'] = __('CostTrasport');
        $extras[$i]['field_has'] = 'hasTrasport';
        $extras[$i]['field_importo'] = 'trasport';
        if($hasTrasport=='Y' && $trasport>0)
            $extras[$i]['importo_display'] = true;
        $i++;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kwallet2.png';
        $extras[$i]['label'] = __('CostMore');
        $extras[$i]['field_has'] = 'hasCostMore';
        $extras[$i]['field_importo'] = 'cost_more';
        if($hasCostLess=='Y' && $costLess>0)
            $extras[$i]['importo_display'] = true;
        $i++;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kwallet.png';
        $extras[$i]['label'] = __('CostLess');
        $extras[$i]['field_has'] = 'hasCostLess';
        $extras[$i]['field_importo'] = 'cost_less';
        if($hasCostMore=='Y' && $costMore>0)
            $extras[$i]['importo_display'] = true;
        
        $html = ''; 
        $html .= '<section><div class="box-header with-border"><h3 class="box-title">Costi extra</h3></div>';
        $html .= '<div class="row">';
        foreach($extras as $extra) {
            $html .= '
            <div class="col-sm-4 col-12">
            <div class="input-group input-group-lg">
                <span class="input-group-btn" style="padding:25px"><img src="'.$extra['img'].'" /></span>
                '.$this->Form->label($extra['field_importo'], ['label' => $extra['label']]).'
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="'.$extra['field_has'].'" value="N" id="'.$extra['field_has'].'-n" checked="checked" required="required">
                    <label class="form-check-label" for="'.$extra['field_has'].'-n">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="'.$extra['field_has'].'" value="N" id="'.$extra['field_has'].'-y" required="required">
                    <label class="form-check-label" for="'.$extra['field_has'].'-y">Si</label>
                </div>';
            if(!isset($extra['importo_display'])) 
                $html .= $this->Form->control($extra['field_importo'], ['label' => false, 'disabled', 'min' => 0]);
            $html .= '
                </div><!-- /input-grgroup -->
            </div><!-- /.col-sm-4 col-12 -->';
        }
        $html .= '</div></section>';
    
        return $html;
    }
}