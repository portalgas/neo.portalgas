<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasParentGroupsHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
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

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        $options['ctrlDesACL'] = false;
        $options['empty'] = true; 
        $options['select2'] = true;         
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['options' => $deliveries]);
    }

    public function data($parent) {
        return parent::data($parent);
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