<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class LogComponent extends Component {

	private $controller = '';
	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->name);
		$this->action = strtolower($controller->request->action);
	}

	public function logging($uuid, $message='', $log='', $level='INFO') {
		
		$this->_logFile($uuid, $message, $log, $level);
		$this->_logDatabase($uuid, $message, $log, $level);
		$this->_logShell($uuid, $message, $log, $level);
	}

	private function _logDatabase($uuid, $message, $log, $level) {

		if(Configure::read('Logs.database')===false)
			return true;

		$data = [];
		$data['uuid'] = $uuid;
		if(is_array($message) || is_object($message))
			$data['message'] = json_encode($message);
		else
			$data['message'] = $message;
		if(is_array($log) || is_object($log))
			$data['log'] = json_encode($log);
		else
			$data['log'] = $log;
		$data['level'] = $level;

		$logTable = TableRegistry::get('Logs');

        $log = $logTable->newEntity();
        $log = $logTable->patchEntity($log, $data);
        if ($logTable->save($log)) {
            return true;
        }
       	else {
       		debug($log->getErrors());
       		return false;
       	}

		return false;
	}

	private function _logFile($uuid, $message, $log, $level) {

		if(Configure::read('Logs.file')===false)
			return true;

		if(is_array($log))
			Log::write('notice', $log);
		else
			Log::write('notice', $uuid.' '.$message.' '.$log);
	}

	private function _logShell($uuid, $message, $log, $level) {

		if(Configure::read('Logs.shell')===false)
			return true;

		debug($log);
	}
}