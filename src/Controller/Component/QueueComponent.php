<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Type;                         
use Cake\Filesystem\File;

class QueueComponent extends Component {

    private $debug = false; // se true non scrive sul database
    
    protected $last_insert_ids = [];
    protected $_registry;
    protected $component;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;

        $this->_registry->load('QueueLog');
                                                  

        // debug($this->_registry->loaded());

        $controller = $registry->getController(); // strtolower($controller->getName())
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
             * queues.queue_mapping_type.code = XML / JSON / CSV => nome file
             * queues.queue_mapping_type.code = DB => id tabella
             * queues.queue_mapping_type.code = REMOTE-XLSX => file remoto
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
            $this->_registry->QueueLog->logging($uuid, $queue, 'Request', $request);
            if(!$queue->has('queue_tables') || empty($queue->queue_tables)) {
                esito = false;
                $code = 500;
                $uuid = $uuid;
                $msg = 'La coda '.$queuesCode.' non ha configurato le tabelle (queue_tables)';
                $results = [];             
            }

            /* 
             * se XML, Json, remote file contentuo file, se non empty
             */
            if($esito) {
                $source = $this->_getSources($id, $queue);
                if($source===false) {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = 'File ['.$id.'] non aperto';
                    $results = []; 
                }
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

                    $this->_registry->QueueLog->logging($uuid, $queue, 'Elaboro tabella '.$table->name.' (id '.$table_id.')');
                    
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
                   
                    $this->_registry->QueueLog->logging($uuid, $queue, 'Per la tabella '.$table->name.' trovati '.$mappings->count().' mapping da elaborare');

                    $datas = [];
                    if($esito) {

                        /*
                         * source dati
                         */
                        switch (strtoupper($queue->queue_mapping_type->code)) {
                            case 'DB':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $request);
                            break;
                            case 'JSON':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request);
                            break;
                            case 'XML':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request);
                            break;
                            case 'CSV':
                            break;
                            case 'REMOTE-XLSX':
                            case 'REMOTE-XLS':
                                $datas = $this->getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request);                            
                            break;
                            default:
                                debug('getDatas non previsto '.$queue->queue_mapping_type->code);
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
            case 'JSON':
                $results = $id;
            break;
            case 'XML':
                $results = @simplexml_load_file($id);
                // debug($xmldata);
            break;
            case 'CSV':
            break;
            case 'REMOTE-XLSX':
                /*
                 * $id = path locale del file
                 * ex /var/www/connect/backup_files/krca/test.xlsx 
                 */
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($id);
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $results[] = $row->getCells();
                        // debug($$results);
                    }
                }
                $reader->close();
            break;
            case 'REMOTE-XLS':
                /*
                 * $id = path locale del file
                 * ex /var/www/connect/backup_files/krca/%s/test.xls 
                 */ 
                /**  Identify the type of $inputFileName  **/
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($id);
                /**  Create a new Reader of the type that has been identified  **/
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                /**  Load $inputFileName to a Spreadsheet Object  **/
                $spreadsheet = $reader->load($id);
                /**  Convert Spreadsheet Object to an Array for ease of use  **/
                $results = $spreadsheet->getActiveSheet()->toArray();
                unset($results[0]); // delete header
                // debug($results);
                /*
                foreach( $results as $result) { 
                    foreach( $result as $cell) {
                        debug($cell);
                    }
                }
                */
            break;             
            default:
                debug('_getSources non previsto '.$queue->queue_mapping_type->code);
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
                $tables[$queue_table->table->id]['before_save'] = $queue_table->before_save;
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
    protected function setComponent($queue) {
       
        $component = '';
        if(!empty($queue->component)) {
            $component = $queue->component; 
        }

        $this->component = $component;
    }

    protected function _save($uuid, $queue, $table, $datas, $organization_id=0, $debug = false) {

        $debug = false;

        $esito = true;
        $action = true;
        $code = 200;
        $uuid = $uuid;
        $msg = '';
        $results = [];

        $slave_namespace = $queue->slave_scope->namespace;
        $slave_entity = ucfirst($table->entity);


        /*
         * beforeSave
         */
        if(!empty($table->before_save)) {
            $results = $this->_registry->{$this->component}->{$table->before_save}($datas, $organization_id);
            // debug($results);
            $this->_registry->QueueLog->logging($uuid, $queue, $results['msg'], $results['results'], 'INFO');

            $esito = $results['esito'];
            $action = $results['action'];

            /*
             * salvo l'id del record dovresse servire per la tabella successiva 
             * per la gestione INNER_TABLE_PARENT (Eredita da tabella)               
             */
            if(isset($results['results']) && isset($results['results']->id))
                $this->last_insert_ids[$table->id] = $results['results']->id;

            if(!$action) {
                $this->_registry->QueueLog->logging($uuid, $queue, 'function before_save '.$table->before_save.' return false => NON insert ', $results);                
            }
            else
                $this->_registry->QueueLog->logging($uuid, $queue, 'function before_save '.$table->before_save.' return true => insert ', $results);            
        }

        if($esito && $action) {

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
                
                 if($debug) debug($data);

                // $this->_registry->QueueLog->logging($this->uuid, $queue->id, 'data', $data);

                /*
                 * insert o update, se viene passato la chiave (ex sku) ctrl se esiste gia' 
                 */
                $insert = true;
                if(!empty($table->update_key)) {
                    if(!isset($data[$table->update_key])) {
                        $this->_registry->QueueLog->logging($uuid, $queue, 'Campo ['.$table->update_key.'] per valutare se INSERT / UPDATE non esiste', 'ERROR');

                        $insert = true;
                    }
                    else
                    if(empty($data[$table->update_key]))
                        $insert = true;
                    else {
                        // debug($data[$table->update_key]);
                        $where = [$table->update_key => $data[$table->update_key]];
                        if($debug) debug($where);
                        $slaveEntity = $tableRegistry->find()->where($where)->first();
                        if($debug) debug($slaveEntity);

                        if(empty($slaveEntity))
                            $insert = true;
                        else
                            $insert = false;
                    } 
                } // if(!empty($table->update_key))

                // $slaveEntity = $this->{$slave_entity}->newEntity();
                if($insert)
                    $slaveEntity = $tableRegistry->newEntity();
               
                if(isset($data['id']))
                    $slaveEntity->id = $data['id'];
                if(isset($data['organization_id']))
                    $slaveEntity->organization_id = $data['organization_id'];
                
                // $slaveEntity = $this->{$slave_entity}->patchEntity($slaveEntity, $data);
                $slaveEntity = $tableRegistry->patchEntity($slaveEntity, $data);
                 
                                                                                                       

                                                                    
                                       
                $this->_registry->QueueLog->logging($uuid, $queue, 'slaveEntity '.$slave_entity, $slaveEntity);

                // if ($this->{$slave_entity}->save($slaveEntity)) {
                if($debug) debug($slaveEntity); 
                if(!$this->debug) {

                        $conn->begin();
                        
                        $resultSave = $tableRegistry->save($slaveEntity);
                        if ($resultSave) {

                            $conn->commit(); 

                            $esito = true;

                            /*
                             * salvo l'id del record dovresse servire per la tabella successiva 
                             * per la gestione INNER_TABLE_PARENT (Eredita da tabella)
                             */
                            $primary_key = $tableRegistry->getPrimaryKey();
                            // debug($primary_key);
                            if(!is_array($primary_key)) {
                                // debug($resultSave->{$primary_key});
                                $this->last_insert_ids[$table->id] =  $resultSave->{$primary_key};
                            }
                            // debug($this->last_insert_ids);

                            /*
                             * afterSave
                             */
                            if(!empty($table->after_save)) {
                                $results = $this->_registry->{$this->component}->{$table->after_save}($data, $organization_id);

                                $esito = $results['esito'];
                                if(!$esito) {
                                    $code = $results['code'];
                                    $uuid = $results['uuid'];
                                    $msg = $results['msg'];
                                    $results = $results['results'];

                                }

                            } // end if(!empty($table->after_save))

                            if($esito) {
                                $esito = true;
                                $code = 200;
                                $uuid = $uuid;
                                $msg = '';
                                $results = [];                                
                            } 
                        }
                        else {

                            $conn->rollback();

                            $this->_registry->QueueLog->logging($uuid, $queue, 'Save', $slaveEntity->getErrors(), 'ERROR');

                            $this->_registry->QueueLog->logging($uuid, $queue, 'Error', 'rollback', 'ERROR');
                            
                            $esito = false;
                            $code = 500;
                            $uuid = $uuid;
                            $msg = '';
                            $results['entity'] = $data;
                            $results['error'] = $slaveEntity->getErrors();
                            break;
                        }
                } // if(!$this->debug)   
                else {
                    $esito = true;
                    $code = 200;
                    $uuid = $uuid;
                    $msg = 'Modalita DEBUG';
                    $results = $slaveEntity;                    
                }                              
            } // end foreach($datas as $data)  

        } // end if($esito)
        else {
            $esito = $results['esito'];
            $code = $results['code'];
            $uuid = $uuid;
            $msg = $results['msg'];
            $results = [];
        }

        $results = ['esito' => $esito, 'code' => $code, 'uuid' => $uuid, 'msg' => $msg, 'results' => $results];

        return $results;
    }

    /*
     * se required => default value
     */
    protected function _defaultValue($data, $is_required, $value_default, $debug=false) {

        $data_old = $data;

        if($is_required && $data==='' && $value_default!='') {
            $data = $value_default;
            settype($data, gettype($data_old));                  
        }

        if($debug) {
            debug('BEFORE data '.$data_old.' ['.gettype($data_old).'] is_required '.$is_required.' value_default '.$value_default.' AFTER data '.$data.' ['.gettype($data).']');
        }

        return $data;
    }   

    private function _getUuid() {
        return date('YmdHi').'-'.uniqid();
    }  

    /*
     * definiti in mapping_value_types.factory_force_value
     */
    protected function castingInt($value, $slave_column='') {
        //if($slave_column=='qta_multipli') { echo '<pre>BEFORE castingInt'; var_dump($value); echo '<pre>'; }
        $value = (int)$value;
        //if($slave_column=='qta_multipli') { echo '<pre>AFTER castingInt'; var_dump($value); echo '<pre>'; }
        return $value;
    }  
}