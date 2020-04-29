<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class QueueLogComponent extends Component {

	private $controller = '';
	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));
	}

	public function logging($uuid, $queue_id, $message='', $log='', $level='INFO') {
		
		$this->_logFile($uuid, $queue_id, $message, $log, $level);
		$this->_logDatabase($uuid, $queue_id, $message, $log, $level);
		$this->_logShell($uuid, $queue_id, $message, $log, $level);
	}

	private function _logDatabase($uuid, $queue_id, $message, $log, $level) {

		if(Configure::read('QueueLogs.database')===false)
			return true;

		$data = [];
		$data['uuid'] = $uuid;
		$data['queue_id'] = $queue_id;
		if(is_array($message) || is_object($message))
			$data['message'] = json_encode($message);
		else
			$data['message'] = $message;
		if(is_array($log) || is_object($log))
			$data['log'] = json_encode($log);
		else
			$data['log'] = $log;
		$data['level'] = $level;

		$queueLogTable = TableRegistry::get('QueueLogs');

        $queueLog = $queueLogTable->newEntity();
        $queueLog = $queueLogTable->patchEntity($queueLog, $data);
        if ($queueLogTable->save($queueLog)) {
            return true;
        }
       	else {
       		debug($queueLog->getErrors());
       		return false;
       	}

		return false;
	}

	private function _logFile($uuid, $queue_id, $message, $log, $level) {

		if(Configure::read('QueueLogs.file')===false)
			return true;

		if(is_array($log))
			Log::write('notice', $log);
		else
			Log::write('notice', $uuid.' [QueueId '.$queue_id.'] '.$message.' '.$log);
	}

	private function _logShell($uuid, $queue_id, $message, $log, $level) {

		if(Configure::read('QueueLogs.shell')===false)
			return true;

		debug($log);
	}
}