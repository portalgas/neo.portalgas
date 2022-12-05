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

    public function supplierOrganizations($suppliersOrganizations) {
        return parent::supplierOrganizations($suppliersOrganizations);
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
}