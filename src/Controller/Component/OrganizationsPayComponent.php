<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class OrganizationsPayComponent extends Component {

	private $controller = '';
	private $action = '';

	private $portalgas_app_root ='';
	private $portalgas_fe_url ='';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));

        $config = Configure::read('Config');
        $this->portalgas_app_root = $config['Portalgas.App.root'];
        $this->portalgas_fe_url = $config['Portalgas.fe.url'];

	}

	/*
	 * get physical path 
	 */
	public function getDocPath($user, $organizationsPay, $debug=false) {

		$results = '';
		
		$path_file = $this->portalgas_app_root.Configure::read('App.doc.upload.organizations.pays').DS.$organizationsPay->year.DS.$organizationsPay->organization_id.'.pdf'; 

		if(file_exists($path_file)) {
			$results = $path_file;
		}

		if($debug) debug('path_file '.$path_file.' - results '.$results);
		return $results;	
	}

	/*
	 * get web url  
	 */		
	public function getDocUrl($user, $organizationsPay, $debug=false) {

		$results = $this->portalgas_fe_url.Configure::read('App.web.doc.upload.organizations.pays').'/'.$organizationsPay->year.'/'.$organizationsPay->organization_id.'.pdf';
		
		return $results;
	}
}