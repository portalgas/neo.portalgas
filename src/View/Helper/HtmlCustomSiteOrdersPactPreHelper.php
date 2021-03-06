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
        // debug($config);
    }

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['type' => 'radio' , 'options' => $deliveries]);
    }
}