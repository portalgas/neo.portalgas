<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersPactHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
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