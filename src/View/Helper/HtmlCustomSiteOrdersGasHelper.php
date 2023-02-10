<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    public function setUser($user) {
        parent::setUser($user);
    }
        
    public function hiddenFields($organization_id, $parent) {
        return parent::hiddenFields($organization_id, $parent);
    }   

    /*
     * dettaglio ordine padre
     */
    public function infoParent($results) {
        return '';    
    }

    public function data($parent) {
        return parent::data($parent);
    }

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = true;
        if(!isset($options['empty'])) $options['empty'] = true; 
        if(!isset($options['select2'])) $options['select2'] = true;       
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    /* 
     * $deliveries 
     *      array['N'] elenco consegne attive per select
     *      array['Y] consegna da definire 
     */
    public function deliveries($deliveries) {
        
        // return $this->Form->control('delivery_id', ['options' => $deliveries['N']]);
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
                        $this->Form->control('delivery_ids', ['id' => 'delivery_ids', 'options' => $deliveries['N'], 'label' => false, 'disabled' => true]).
                        '</div>'];            
        }
        
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
        $html .= $this->Form->radio(
            'delivery_id',
            [
            $item1,
            ['value' => key($deliveries['Y']), 
                'text' => '<div id="radio-delivery-type-Y" class="radio-delivery-type" style="margin-bottom: 10px;">'.
                            'Data e luogo della consegna ancora da definire'. // $deliveries['Y'][key($deliveries['Y'])].
                            '</div>'],
            $item3
            ],
            [
            'escape' => false, 'default' => 'N', 'required' => 'required', 'class' => 'radio-deliveries']
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

        /* 
         * modal, js in ordersForm.js
         */
        $html .= '
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
    </div>	    
        ';
        return $html;
    }

    public function note() {
        return parent::note();     
    } 
    
    public function mailOpenTesto() {
        return parent::mailOpenTesto();     
    }
    
    public function monitoraggio($results) {
        return parent::monitoraggio($results);
    }

    public function typeGest($results) {
        return parent::typeGest($results);
    }    
}