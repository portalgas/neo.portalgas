<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

class QueueComponent extends Component {

    private $debug = false; // se true non scrive sul database
    
    protected $last_insert_ids = [];
    protected $_registry;
    
	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;

        $this->_registry->load('QueueLog');
        $this->_registry->load('MappingDwweMago');

        // debug($this->_registry->loaded());

        $controller = $registry->getController(); // strtolower($controller->name)
		$action = strtolower($controller->request->action);
	}

    /*
     * POST /api/queue
     */
    public function queue($request, $debug=false) {    
        
        try {
            $uuid = $this->_getUuid();

            $esito = true;
            $code = '';
            $msg = '';
            $results = [];  

            $queuesCode = $request['code'];
            /*
             * queues.queue_mapping_type.code = XML / CSV => nome file
             * queues.queue_mapping_type.code = DB => id tabella
             */
            $id = $request['id'];
            
            $organization_id = 0;
            if(!empty($organization_id))
                $organization_id = $request['organization_id'];

            /*
             * queue
             */
            $queuesTable = TableRegistry::get('Queues');
            $queue = $queuesTable->findByCode($queuesCode);    
            $this->_registry->QueueLog->logging($uuid, $queue->id, 'Request', $request);
            if(!$queue->has('queue_tables')) {
                $esito = false;
                $code = 500;
                $uuid = $uuid;
                $msg = 'La coda '.$queuesCode.' non ha configurato le tabelle (queue_tables)';
                $results = [];             
            }

            /* 
             * se XML contentuo file, se non empty
             */
            $source = $this->_getSources($id, $queue);
            if($source===false) {
                $esito = false;
                $code = 500;
                $uuid = $uuid;
                $msg = 'File '.$id.' non aperto';
                $results = []; 
            }

            if($esito) {

                $tables = $this->_getTables($queue);
                if(empty($tables)) {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = 'La coda '.$queuesCode.' non ha tabelle attive (queue_tables)';
                    $results = [];             
                }

                foreach($tables as $table_id => $table) {

                    if($esito===false)
                        break;

                    $this->_registry->QueueLog->logging($uuid, $queue->id, 'Elaboro tabella '.$table->name.' (id '.$table_id.')');
                    
                    /*
                     * mappings per ogni tabella slave
                     */
                    $mappings = $this->_getMappings($queue, $table_id);
                    // debug($mappings);
                    if(empty($mappings) || $mappings->count()==0) {
                        $esito = false;
                        $code = 500;
                        $uuid = $uuid;
                        $msg = 'La coda '.$queuesCode.' non configurato le mappature (mappings)';
                        $results = [];             
                    }
                   
                    $this->_registry->QueueLog->logging($uuid, $queue->id, 'Per la tabella '.$table->name.' trovati '.$mappings->count().' mapping da elaborare');

                    $datas = [];
                    if($esito) {

                        /*
                         * source dati
                         */
                        switch (strtoupper($queue->queue_mapping_type->code)) {
                            case 'DB':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $request);
                            break;
                            case 'XML':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request);
                            break;
                            case 'CSV':
                            break;
                            default:
                                debug($queue->queue_mapping_type->code);
                                die();    
                            break;
                        }   
                    }

                    if(isset($datas['esito']) && !$datas['esito']) {

                        $esito = $datas['esito'];
                        $code = $datas['code'];
                        $uuid = $datas['uuid'];
                        $msg = $datas['msg'];
                        $results = $datas['results'];

                        break;
                    }

                } // end foreach($tables as $table)
            } // if($esito)
        } catch (Exception $e) {

            if(isset($uuid))
                $uuid = $uuid;
            if(isset($queue->id))
                $queue_id = $queue->id;

            $this->_registry->QueueLog->logging($uuid, $queue_id, 'Error', $e->getMessage(), 'ERROR');

            $esito = false;
            $code = 500;
            $uuid = $uuid;
            $results = $e->getMessage();            
        }

        return ['esito' => $esito, 'code' => $code, 'uuid' => $uuid, 'msg' => $msg, 'results' => $results];
    }

	private function _getSources($id, $queue) {

		$results = null;
	    switch (strtoupper($queue->queue_mapping_type->code)) {
	        case 'DB':
	            
	        break;
	        case 'XML':
	            $results = @simplexml_load_file($id);
	            // debug($xmldata);
	        break;
	        case 'CSV':
	        break;
	        default:
	            debug($queue->queue_mapping_type->code);
	            die();    
	        break;
	    } 

	    return $results;
	}

	/*
	 * il ciclo per il mapping parte da queue_tables (sort)
	 */
	protected function _getTables($queue) {

        $tables = [];
        foreach($queue->queue_tables as $queue_table) {
            if($queue_table->table->is_active) {
                // rif table slave
                $tables[$queue_table->table->id] = $queue_table->table;
            }
        }

        return $tables;
	}

	protected function _getMappings($queue, $table_id) {

        $where = [];

        $mappingsTable = TableRegistry::get('Mappings');

        $where = ['Mappings.queue_id' => $queue->id,
                'Mappings.master_scope_id' => $queue->master_scope_id,
                'Mappings.slave_scope_id' => $queue->slave_scope_id,
                'Mappings.slave_table_id' => $table_id,
                'Mappings.is_active' => 1];

        $mappings = $mappingsTable->find()
                            ->where($where)
                            ->order(['Mappings.sort' => 'asc'])
                            ->contain(['QueueTables', 'MappingTypes', 'MappingValueTypes', 'MasterScopes', 'SlaveScopes', 
                                'MasterTables', // solo per $queue->queue_mapping_type->code = DB
                                'SlaveTables'])
                            ->all();
        return $mappings; 		
	}

    /*
     * componente che si occupa del mapping per FUNC_
     */
    protected function _getComponent($queue) {
       
        $component = '';
        if(!empty($queue->component)) {
            $component = $queue->component; 
        }

        // array_push($this->components, $component);

        return $component;
    }

    protected function _save($uuid, $queue, $table, $datas) {

        $slave_namespace = $queue->slave_scope->namespace;
        $slave_entity = ucfirst($table->entity);

        /*
         * slave
         */
        //$this->loadModel(sprintf('App\Model\Table\%s\%sTable', $slave_namespace, $slave_entity));
        // $this->_registry->QueueLog->logging($this->uuid, $queue->id, 'INSERT table slave ['.$slave_entity.'] in '.$slave_namespace);
		$queueTable = TableRegistry::get('Queues');            
        $datasource_slave = $queueTable->getDataSourceSlave($queue);

        $tableRegistry = TableRegistry::get($slave_entity);
        $tableRegistry->setConnection(ConnectionManager::get($datasource_slave));
        $conn = $tableRegistry->getConnection($datasource_slave);
        $conn->begin();

        foreach($datas as $data) {
           
            // // $this->_registry->QueueLog->logging($this->uuid, $queue->id, 'data', $data);
            
            // $slaveEntity = $this->{$slave_entity}->newEntity();
            $slaveEntity = $tableRegistry->newEntity();

            // $slaveEntity = $this->{$slave_entity}->patchEntity($slaveEntity, $data);
            $slaveEntity = $tableRegistry->patchEntity($slaveEntity, $data);
             
            // $this->_registry->QueueLog->logging($uuid, $queue->id, 'slaveEntity', $slaveEntity);

            // if ($this->{$slave_entity}->save($slaveEntity)) {
            if(!$this->debug) {
                    $resultSave = $tableRegistry->save($slaveEntity);
                    if ($resultSave) {

                        /*
                         * per la gestione INNER_TABLE_PARENT (Eredita da tabella)
                         */
                        $primary_key = $tableRegistry->getPrimaryKey();
                        // debug($primary_key);
                        if(!is_array($primary_key)) {
                            // debug($resultSave->{$primary_key});
                            $this->last_insert_ids[$table->id] =  $resultSave->{$primary_key};
                        }
                        // debug($this->last_insert_ids);

                        $esito = true;
                        $code = 200;
                        $uuid = $uuid;
                        $msg = '';
                        $results = [];
                    }
                    else {
                        
                        // $this->_registry->QueueLog->logging($uuid, $queue->id, 'save', $slaveEntity->getErrors(), 'ERROR');

                        $esito = false;
                        $code = 500;
                        $uuid = $uuid;
                        $msg = '';
                        $results['entity'] = $data;
                        $results['error'] = $slaveEntity->getErrors();
                        break;
                    }
            } // if(!$this->debug)                                 
        } // end foreach($datas as $data)  

        if($esito)
            $conn->commit(); 
        else {
            // $this->_registry->QueueLog->logging($uuid, $queue->id, 'Error', 'rollback', 'ERROR');
            $conn->rollback();
        }

        return ['esito' => $esito, 'code' => $code, 'uuid' => $uuid, 'msg' => $msg, 'results' => $results];
    }

    /*
     * se required => default value
     */
    protected function _defaultValue($data, $is_required, $value_default) {

        if($is_required && $data=='' && !empty($value_default)) {
            $data = $value_default;                  
        }

        return $data;
    }	

    private function _getUuid() {
        return uniqid();
    }    
}