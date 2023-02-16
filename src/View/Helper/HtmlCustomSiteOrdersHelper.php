<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Core\Configure;
use App\Traits;
class HtmlCustomSiteOrdersHelper extends Helper
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
    protected $_portalgas_bo_url = '';

    protected $_user = null;
    protected $_parent = null;
    protected $_order = null;
 
	public $helpers = ['Html', 'Form', 
                       'HtmlCustom', 'HtmlCustomSite',
                       'HtmlCustomSiteOrdersDesGroups',
                       'HtmlCustomSiteOrdersGasParentGroups',
                       'HtmlCustomSiteOrdersGasGroups',
                       'HtmlCustomSiteOrdersGas',
                       'HtmlCustomSiteOrdersPact',
                       'HtmlCustomSiteOrdersPactPre',
                       'HtmlCustomSiteOrdersPactPromotion'];

    public function initialize(array $config)
    {
        $config = Configure::read('Config');
        $this->_portalgas_app_root = $config['Portalgas.App.root'];
        $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
        $this->_portalgas_bo_url = $config['Portalgas.bo.url'];       
    }

    /* 
     * $user = $this->Identity->get() perche' negli altri Helper e' null!
     * $parent: per DES / GAS-GROUP ordine titolare
     * $order l'ordine per edit
     */
    public function factory($order_type_id, $user, $parent=null, $order=null, $debug=false) {

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

        // le classe che ereditano non lo prendono
        $this->_user = $user;
        $this->_parent = $parent;
        $this->_order = $order;
        
        // workaround
        $this->{$helper}->_user = $user;
        $this->{$helper}->_parent = $parent;
        $this->{$helper}->_order = $order;

        return $this->{$helper};
    } 

    public function hiddenFields() {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $this->_user->organization->id, 'required' => 'required']);
    
        return $html;
    }    

    /*
     * dettaglio ordine padre
     */
    public function infoParent() {
        return '';    
    }
    
    public function data() {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '</div>';

        if(!empty($this->_parent)) {
            $msg = "L'ordine si chiuderà il ".$this->HtmlCustom->data($this->_parent->data_fine);

            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">'; 
            $html .= $this->HtmlCustom->alert($msg);
            $html .= '</div>'; 
            $html .= '</div>';    
        }

        return $html;
    }

    /*
     * $options=['empty'] 
     * $options=['ctrlDesACL'] nella creazione di un ordine ctrl se il produttore e' DES e l'utente e' titolare
     * $options=['select2'] se attivata la ricerca nella select
     */
    public function supplierOrganizations($suppliersOrganizations, $options=[]) {

        if(isset($options['id'])) 
            $id = $options['id'];
        else 
            $id = 'supplier_organization_id';

        $opts = ['label' => __('SupplierOrganization'), 
                 'options' => $suppliersOrganizations, 
                 // @change' => 'getSuppliersOrganization', con select2 non ha + effetto, fatto il bind in supplierOrganization.js
                 'id' => $id,
                 'class' => 'form-control'];
        if(isset($options['select2'])) {
            if($options['select2']) 
                $opts['class'] = 'select2 form-control';                
        }              
        if(isset($options['empty'])) {
            if($options['empty'])
                $opts += ['empty' => Configure::read('HtmlOptionEmpty')];
        } 
        else 
            $opts += ['empty' => Configure::read('HtmlOptionEmpty')];
        
        if(isset($options['default'])) 
            $opts['default'] = $options['default'];                

        $html = '';
        
        // nei filtri di ricerca vale search_supplier_organization_id e non e' fatto il bind
        if($id=='supplier_organization_id') {
            $html .= $this->Html->script('vue/suppliersOrganization', ['block' => 'scriptPageInclude']);
            $html .= '<div class="row" id="vue-supplier-organization">';
            $html .= '<div class="col-md-8">';
            // echo $this->HtmlCustomSite->boxSupplierOrganization($suppliersOrganizations);
            $html .= $this->Form->control($id, $opts);
            $html .= '</div>';
            $html .= '<div v-if="supplier_organization.name!=null" class="col-md-4" id="vue-supplier-organization-box">';
            $html .= '  <div class="box-img" v-if="supplier_organization.supplier.img1!=\'\'"><img width="'.Configure::read('Supplier.img.preview.width').'" class="img-responsive-disabled userAvatar" v-bind:src="supplier_organization.img1" /></div>';
            $html .= '  <div class="box-name">{{supplier_organization.name}}</div>';
            $html .= '  <div class="box-owner">'.__('organization_owner_articles').': <span class="label label-info">{{supplier_organization.owner_articles | ownerArticlesLabel}}</span></div>';
            $html .= '</div>';    
        }
        else {
            $html .= '<div>';
            $html .= $this->Form->control($id, $opts);
        }

        if(isset($options['ctrlDesACL']) && $options['ctrlDesACL']) {
            $html .= '<div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Attenzione</h4>
                  </div>
                  <div class="modal-body">
                    <p v-html="$options.filters.html(modal_body)"></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Chiudi</button>
                  </div>
                </div>
              </div>
            </div>';
        }
        $html .= '</div>';  // vue-supplier-organization      

        return $html;
    } 

    public function deliveries($deliveries, $options=[]) {
        $results = [];
        $results['html'] = $this->Form->control('delivery_id', ['options' => $deliveries, 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty')]);
        return $results;
    }

    /* 
     * $deliveries 
     *      array['N'] elenco consegne attive per select
     *      array['Y] consegna da definire 
     * 
     * $results['html']
     * $results['bottom'] html inserito nel Layout in fondo, ex modal
     */
    public function gestTypeDeliveries($deliveries, $options=[]) {
        
        $results = [];

        /*
         * default
         */
        $default = 'N'; // lista consegne attive
        if(!$this->_order->isNew()) { // edit
            if($this->_order->delivery_id==key($deliveries['Y']))
                $default = 'Y';  // consegna Da definire
            else {
                $default = $this->_order->delivery_id;

                /* 
                * ctrl che tra l'elenco delle consegne ci sia la consegna gia' associata all'ordine 
                * se non c'e' (per esempio consegna chiusa e qui prendo solo DATE(Delivery.data) >= CURDATE() ) 
                * l'aggiungo 
                * */
                if(!array_key_exists($this->_order->delivery_id, $deliveries))
                    $deliveries['N'][$this->_order->delivery_id] = $this->_order->delivery->luogo.' - '.$this->_order->delivery->data->i18nFormat('eeee d MMMM Y');
            }           
        }

        // return $this->Form->control('delivery_id', ['options' => $deliveries['N'], 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty')]);
        if(empty($deliveries['N'])) {
            $item1 = [
                'value' => 'N', 
                'text' => '<div id="radio-delivery-type-N" class="radio-delivery-type" style="margin-bottom: 10px;">'.
                        __('OrderNotFoundDeliveries').
                        '</div>'];
        }
        else {
            $item1 = [
                'value' => 'N', 
                'text' => '<div id="radio-delivery-type-N" class="radio-delivery-type">'.
                        $this->Form->control('delivery_ids', ['id' => 'delivery_ids', 'options' => $deliveries['N'], 'label' => false, 'disabled' => true, 'escape' => false, 
                                                               'default' => $default, 'empty' => Configure::read('HtmlOptionEmpty')]).
                        '</div>'];            
        }
        
        $item2 = [
            'value' => key($deliveries['Y']), 
            'text' => '<div id="radio-delivery-type-Y" class="radio-delivery-type" style="margin-bottom: 10px;">'.
                    'Data e luogo della consegna ancora da definire'. // $deliveries['Y'][key($deliveries['Y'])].
                    '</div>'];

        if($this->_user->acl['isManagerDelivery']) {
            $item3 = [
                'value' => 'TO-CREATE', 
                'text' => '<div id="radio-delivery-type-TO-CREATE" class="radio-delivery-type" style="margin-bottom: 10px;">'.
                            '<a target="_blank"  title="Crea una nuova consegna" href="'.$this->HtmlCustomSite->jLink('Deliveries', 'add').'">'.        
                            'Crea una nuova consegna <i class="text-primary fa fa-lg fa-plus-circle"></i></a>'.
                        '</div>
                        <div id="radio-delivery-type-TO-CREATE-disabled" class="radio-delivery-type" style="margin-bottom: 10px;display:none;">'.
                            'Crea una nuova consegna <i class="text-primary fa fa-lg fa-plus-circle"></i></a>'.
                        '</div>'];
        }
        else {
            $item3 = [
                'value' => 'TO-CREATE', 
                'text' => '<div id="radio-delivery-type-TO-CREATE" class="radio-delivery-type" style="margin-bottom: 10px;">'.
                            '<a class="sendMail" title="'.__('Send mail to manager to delivery').'" href="">'.        
                            __('Send mail to manager to delivery').' <i class="text-primary fa fa-lg fa-envelope"></i></a>'.
                        '</div>
                        <div id="radio-delivery-type-TO-CREATE-disabled" class="radio-delivery-type" style="margin-bottom: 10px;display:none;">'.
                        __('Send mail to manager to delivery').'  <i class="text-primary fa fa-lg fa-envelope"></i></a>'.
                        '</div>'];
        }
        
        $html = '<section class="content delivery">';
        $html .= $this->title(__('Delivery'));
        $html .= $this->Form->hidden('delivery_id', ['value' => '']);
        $html .= $this->Form->radio(
            'type_delivery',
            [
                $item1,
                $item2,
                $item3
            ],
            [
            'escape' => false, 'default' => $default, 'required' => 'required', 'class' => 'radio-deliveries']
            );
        $html .= '</section>';

        /* 
         * css
         */
        $html .= '<style>
        .content.delivery .radio-delivery-type {opacity: 0.3}
        .content.delivery .radio > label {width:100%}
        .content.delivery .radio-delivery-type {margin-left: 25px}
        </style>'
        ;
        $results['html'] = $html;

        /* 
        * modal, js in ordersForm.js
        */
        $results['bottom'] = '
        <div class="modal fade" id="dialog-send_mail" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formGasMail" action="">
                <legend style="display:none;">'.__('Send Mail').'</legend>            
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">'.__('Send Mail').'</h4>
                    </div>
                    <div class="modal-body">                
                        <div class="form-group">
                            <label for="email">Mittente</label> 
                            <input type="email" class="form-control" id="email" value="'.$this->_user->get('email').'" disabled/>
                        </div>
                        <div class="form-group">
                            '.$this->Form->textarea('mail_body', ['rows' => '10', 'cols' => '100%', 'id' => 'mail_body', 'class' => 'form-control']).'
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Chiudi</button>
                        <button type="button" class="btn btn-success" id="submit-modal-mail">'.__('Send').'</button>
                    </div>
                </form>
            </div>
        </div>		
        </div>';

        return $results;
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
    
    public function monitoraggio() {

        $qta_massima_um_options = ['KG' => 'Kg (prenderà in considerazione anche i Hg, Gr)', 
                                    'LT' => 'Lt (prenderà in considerazione anche i Hl, Ml)', 
                                    'PZ' => 'Pezzi'];
        $qta_massima_um = 'KG';

        $html = '<section class="content">';
        $html .= $this->title("Monitoraggio");
        $html .= '<div class="row">
                <div class="col-md-3">
                '.$this->Form->control('qta_massima', [
                            'label' => 'Quantità massima', 
                            'type' => 'number', 
                            'min' => 0, 'value' => 0]).'
                </div>
                <div class="col-md-4">
                '.$this->Form->input('qta_massima_um', ['id' => 'qta_massima_um', 'label' => 'UM', 'options' => $qta_massima_um_options, 'default' => $qta_massima_um, 'required' => 'false']).'
                </div>
                <div class="col-md-5"> 
                    <br /><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#monitoraggio-qta_massima">
                        <i class="fa fa-2x fa-envelope" aria-hidden="true"></i> maggior informazioni</button>
                    '.$this->modal('monitoraggio-qta_massima', 'Monitoraggio quantità massima', "Quando il peso totale espresso nell'unità di misura indicata raggiungerà la quantità indicata, verrà inviata una mail ai referenti e chiuso l'ordine").'
                </div>         
            </div> <!-- row --> 
            <div class="row">
                <div class="col-md-3">
                '.$this->Form->label('importo_massimo', ['label' => 'Importo massimo']).'
                '.$this->Form->control('importo_massimo', ['label' => false, 'type' => 'number', 'min' => 0, 'value' => 0,
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
                        <i class="fa fa-2x fa-envelope" aria-hidden="true"></i> maggior informazioni</button>
                        '.$this->modal('monitoraggio-importo_massimo', 'Monitoraggio importo massimo', "Quando il totale degli acquisti raggiungerà l'importo indicato, verrà inviata una mail ai referenti e chiuso l'ordine").'
                </div>         
            </div> <!-- row -->  
            <section>'; 

        return $html;
    }
    
    public function typeGest() {

        if($this->_order->isNew()) {
            // add 
            $type_gest = '';
        }
        else  {
            // edit 
            $type_gest = $this->_order->typeGest;
        }

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
        $html .= '<section>';
        $html .= $this->title("Tipologia di gestione");

        foreach($typeGests as $typeGest) {
            $html .= '
                <div class="row" style="padding:5px 25px;border-bottom:1px solid #ddd;">
                    <div class="col-6 col-md-6">
                        <div class="radio">
                          <label><input type="radio" name="typeGest" value="'.$typeGest['value'].'" ';
            if($type_gest==$typeGest['value'])  $html .= 'checked';   
            $html .= ' />'.$typeGest['label'].'</label>
                        </div>        
                    </div>
                    <div class="col-6 col-md-6">';
            if(isset($typeGest['modal_title'])) {

                $options['size'] = 'modal-lg';

                $html .= '
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#typeGest-'.$typeGests[$i]['value'].'">
                        <i class="fa fa-2x fa-info-circle" aria-hidden="true"></i> visualizza l\'esempio
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
    public function extra() {

        if($this->_order->isNew()) {
            // add 
            $hasTrasport = 'N';
            $trasport = 0;
            $hasCostMore = 'N';
            $costMore = 0;
            $hasCostLess = 'N';
            $costLess = 0;

            $disabled = false; // il maggior campo importo non e' visualizzato
        }
        else {
            // edit
            $hasTrasport = $this->_order->hasTrasport;
            $trasport = $this->_order->trasport;
            $hasCostMore = $this->_order->hasCostMore;
            $costMore = $this->_order->costMore;
            $hasCostLess = $this->_order->hasCostLess;
            $costLess = $this->_order->costLess;          
            
            $disabled = false;
        }
   
        if(!empty($this->_parent)) {
            $hasTrasport = $this->_parent->hasTrasport;
            $trasport = $this->_parent->trasport;
            $hasCostMore = $this->_parent->hasCostMore;
            $costMore = $this->_parent->costMore;
            $hasCostLess = $this->_parent->hasCostLess;
            $costLess = $this->_parent->costLess;

            $disabled = true;
        }

        $extras = [];
        $i=0;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/ark2.png';
        $extras[$i]['label'] = __('CostTrasport');
        $extras[$i]['field_has'] = 'hasTrasport';
        $extras[$i]['field_importo'] = 'trasport';
        $extras[$i]['has'] = $hasTrasport;
        $extras[$i]['importo'] = $trasport;
        if($hasTrasport=='Y' && $trasport>0)
            $extras[$i]['importo_display'] = true;
        $i++;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kwallet2.png';
        $extras[$i]['label'] = __('CostMore');
        $extras[$i]['field_has'] = 'hasCostMore';
        $extras[$i]['field_importo'] = 'cost_more';
        $extras[$i]['has'] = $hasCostMore;
        $extras[$i]['importo'] = $costMore;
        if($hasCostLess=='Y' && $costLess>0)
            $extras[$i]['importo_display'] = true;
        $i++;
        $extras[$i]['img'] = $this->_portalgas_fe_url.'/images/cake/apps/32x32/kwallet.png';
        $extras[$i]['label'] = __('CostLess');
        $extras[$i]['field_has'] = 'hasCostLess';
        $extras[$i]['field_importo'] = 'cost_less';
        $extras[$i]['has'] = $hasCostLess;
        $extras[$i]['importo'] = $costLess;
        if($hasCostMore=='Y' && $costMore>0)
            $extras[$i]['importo_display'] = true;
        
        $html = ''; 
        $html .= '<section>';
        $html .= $this->title("Costi extra");
        $html .= '<div class="row">';
        foreach($extras as $extra) {
            $html .= '
            <div class="col-sm-4 col-12">
            <div class="input-group input-group-lg">
                <span class="input-group-btn" style="padding:25px"><img src="'.$extra['img'].'" /></span>
                '.$this->Form->label($extra['field_importo'], ['label' => $extra['label']]).'
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="'.$extra['field_has'].'" value="N" id="'.$extra['field_has'].'-n" '; 
            if($extra['has']=='N') $html .= 'checked="checked"';
            $html .= ' required="required">
                    <label class="form-check-label" for="'.$extra['field_has'].'-n">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="'.$extra['field_has'].'" value="Y" id="'.$extra['field_has'].'-y" '; 
            if($extra['has']=='Y') $html .= 'checked="checked"';
            $html .= ' required="required">
                    <label class="form-check-label" for="'.$extra['field_has'].'-y">Si</label>
                </div>';
            if(isset($extra['importo_display'])) 
                $html .= $this->Form->control($extra['field_importo'], ['label' => false, 'disabled', 'min' => 0, 'value' => $extra['importo']]);
            $html .= '
                </div><!-- /input-grgroup -->
            </div><!-- /.col-sm-4 col-12 -->';
        }
        $html .= '</div></section>';
    
        return $html;
    }
}