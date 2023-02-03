<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

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
        if($this->_user->acl['isManagerDelivery']) {

        }
        else {

        }

        $html = '<section class="content delivery">';
        $html .= $this->title(__('Delivery'));
        $html .= $this->Form->radio(
            'delivery',
            [
                ['value' => 'N', 'text' => $this->Form->control('delivery_id', ['options' => $deliveries['N'], 'label' => false])],
                ['value' => key($deliveries['Y']), 'text' => $this->Form->control('delivery_Y', ['value' => $deliveries['Y'][key($deliveries['Y'])], 'label' => false, 'disabled' => true])],
                ['value' => 'MAIL', 'text' => '<div class="form-group input" title="Crea una nuova consegna">Crea una nuova consegna <i class="text-primary fa fa-lg fa-plus-circle"></i></div>'],
            ],
            [
            'escape' => false, 'default' => 'N', 'required' => 'required', 'class' => 'radio-deliveries']
            );
        $html .= '</section>';
        $html .= '<style>
        .content.delivery .radio > label {width:100%}
        .content.delivery .radio > label > .form-group.input {margin-left: 25px}
        </style>'
        ;

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