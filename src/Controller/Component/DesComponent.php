<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class DesComponent extends Component {

	private $controller = '';
	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));
	}

	public function logging($uuid, $message='', $log='', $level='INFO') {
		
		$this->_logFile($uuid, $message, $log, $level);
		$this->_logDatabase($uuid, $message, $log, $level);
		$this->_logShell($uuid, $message, $log, $level);
	}
}