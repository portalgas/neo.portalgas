<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasParentGroupsHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

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

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = false;
        if(!isset($options['empty'])) $options['empty'] = true; 
        if(!isset($options['select2'])) $options['select2'] = true;  
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries, $options=[]) {
        return parent::gestTypeDeliveries($deliveries, $options);        
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