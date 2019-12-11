<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class AppHelper extends Helper {
 
    public function assetUrl($path, $options = array()) {
        if (!empty($this->request->params['ext']) && $this->request->params['ext'] === 'pdf') {
            $options['fullBase'] = true;
        }
        
        return parent::assetUrl($path, $options);
    }
}