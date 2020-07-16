<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;

class DocumentComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    /*
     * get back url after upload
     */
    public function getRedirectUrl($document_reference_model=null, $document_reference_id=0, $debug=false) {

    	if($debug) debug($document_reference_model);

        if(!empty($document_reference_model)) {

        	$controller = $document_reference_model->url_back_controller;
        	$action = $document_reference_model->url_back_action;
        	$params = '';

        	if($debug) debug(strtolower($document_reference_model->code));
        	switch (strtolower($document_reference_model->code)) {
        		case '':
        			$params = $document_reference_id;
        		break;
        	}
            $url = ['controller' => $controller, 'action' => $action, $params];
        }
        else
            $url = ['action' => 'index'];

		if($debug) debug($url);

        return $url;
    }   
}