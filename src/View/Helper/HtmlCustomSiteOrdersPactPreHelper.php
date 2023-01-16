<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersPactPreHelper extends HtmlCustomSiteOrdersPactHelper
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

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['type' => 'radio' , 'options' => $deliveries]);
    }

    public function supplierOrganizations($suppliersOrganizations, $ctrlDesACL=false) {
        return parent::supplierOrganizations($suppliersOrganizations, $ctrlDesACL);
    }
    
    public function monitoraggio($results) {
        return parent::monitoraggio($results);
    }

    public function typeGest($results) {
        return parent::typeGest($results);
    }      
}