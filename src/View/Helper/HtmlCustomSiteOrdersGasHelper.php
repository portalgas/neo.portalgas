<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersGasHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    // eventuale msg in index
    public function msg() {
        return '';
    }

    public function hiddenFields() {
        return parent::hiddenFields();
    }

    /*
     * dettaglio ordine padre
     */
    public function infoParent() {
        return '';
    }

    public function data() {
        return parent::data();
    }

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = true;
        if(!isset($options['empty'])) $options['empty'] = true;
        if(!isset($options['select2'])) $options['select2'] = true;
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries, $options=[]) {
        return parent::gestTypeDeliveries($deliveries, $options);
    }

    public function deliveryOlds($order_type_id, $order, $parent, $delivery_olds) {
        return parent::deliveryOlds($order_type_id, $order, $parent, $delivery_olds);
    }

    public function note() {
        return parent::note();
    }

    public function mailOpenTesto() {
        return parent::mailOpenTesto();
    }

    public function monitoraggio() {
        return parent::monitoraggio();
    }

    public function typeGest() {
        return parent::typeGest();
    }
}
