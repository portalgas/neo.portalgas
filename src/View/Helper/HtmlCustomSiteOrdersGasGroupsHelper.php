<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasGroupsHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function hiddenFields($organization_id, $parent) {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $organization_id, 'required' => 'required']);

        // prod_gas_promotion_id
        $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $parent->id, 'required' => 'required']);
        
        return $html;
    }    

    public function supplierOrganizations($suppliersOrganizations) {
        return $this->Form->control('supplier_organization_id', ['options' => $suppliersOrganizations, '@change' => 'getSuppliersOrganization']);
    }

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['options' => $deliveries]);
    }

    public function note() {
        return parent::note();     
    } 
    
    public function mailOpenTesto() {
        return parent::note();     
    }      
}