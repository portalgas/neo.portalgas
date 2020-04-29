<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\I18n\Time;

class QueueDatabaseComponent extends QueueComponent {

    private $controller;
    private $action;
    private $uuid;
    private $queue_id;
    protected $_registry;

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;

        $this->_registry->load('QueueLog');
        // debug($this->_registry->loaded());

        $this->controller = $registry->getController(); // strtolower($controller->getName())
        $this->action = strtolower($this->controller->request->getParam('action'));        
	}

	public function getDatas($uuid, $queue, $mappings, $table, $table_id, $request) {
        
        /*
         * valori return
         */
        $esito = true;
        $code = '200';
        $msg = '';
        $results = []; 

        $this->uuid = $uuid;
        $this->queue_id = $queue->id;

        $this->setComponent($queue);
        $this->_registry->load($this->component); // custom MappingDwweMago

        $id = $request['id'];

        foreach ($mappings as $numMapping => $mapping) {

            if($esito===false)
                break;

            $numResults = 0;

            $master_namespace = '';
            $master_entity = '';
            $master_table = '';

            if($mapping->has('master_scope')) // quando il valore per lo slave non e' preso dal master (ex CURRENTDATE) 
                $master_namespace = $mapping->master_scope->namespace;
            if($mapping->has('master_table')) // quando il valore per lo slave non e' preso dal master (ex CURRENTDATE) 
                $master_entity = ucfirst($mapping->master_table->entity);
            
            if($mapping->has('master_table')) 
                $master_table = $mapping->master_table->name;
            $slave_table = $mapping->slave_table->name;

            $master_column = $mapping->master_column;
            $slave_column = $mapping->slave_column;
            
            $mapping_type_code = $mapping->mapping_type->code;
            $value = $mapping->value;
            $parameters = $mapping->parameters;
                
            /*
             * master
             *  puo' non essere valorizzato quando il valore per lo slave non e' preso dal master (ex CURRENTDATE) 
             */
            $masterEntitys = [];
            if(!empty($master_namespace) && !empty($master_entity)) {
                
                // $this->loadModel(sprintf('App\Model\Table\%s\%sTable', $master_namespace, $master_entity));
                $this->controller->loadModel(sprintf('App\Model\Table\%s\%sTable', $master_namespace, $master_entity));
                /*
                $queueTable = TableRegistry::get('Queues');
                $datasource_master = $queueTable->getDataSourceMaster($queue);

                $masterTable = TableRegistry::get($master_table);
                $masterTable->setConnection(ConnectionManager::get($datasource_master));
                */
                $where = [$mapping->master_table->where_key => $id];
                $masterEntitys = $this->controller->{$master_entity}
                            ->find()
                            ->where($where)
                            ->contain($this->_getModelContain($master_entity));
                
                // debug('Tot found rows '.$masterEntitys->count());
                // debug($masterEntitys);

                if(empty($masterEntitys) || $masterEntitys->count()==0) {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = $master_entity.' con '.$mapping->master_table->where_key.'='.$id.' non trovato!';
                    $results = $where; 

                    $this->_registry->QueueLog->logging($uuid, $queue->id, $msg, $where, 'ERROR');

                    break;
                }
            } // if(!empty($master_namespace) && !empty($master_entity))

            if(empty($masterEntitys)) {

                $datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $value, $master_column);
                
                $datas[$numResults][$slave_column] = $this->_defaultValue($datas[$numResults][$slave_column], $mapping->is_required, $mapping->value_default);

                if($mapping->is_required && $datas[$numResults][$slave_column]==='') {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = 'Slave table '.$slave_table.' column ['.$slave_column.'] required';
                    $results = []; 

                    $this->_registry->QueueLog->logging($uuid, $queue->id, $msg, '', 'ERROR');

                    break;                        
                }

                $this->_registry->QueueLog->logging($uuid, $queue->id, ($numMapping+1).') Elaboro mapping - elaboro da SLAVE ['.$slave_table.'::'.$slave_column.']', 'Valore convertito ['.$datas[$numResults][$slave_column].']');

                $numResults++;
            }
            else 
            foreach($masterEntitys as $masterEntity) {

                $datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $value, $master_column, $masterEntity);

                $datas[$numResults][$slave_column] = $this->_defaultValue($datas[$numResults][$slave_column], $mapping->is_required, $mapping->value_default);

                if($mapping->is_required && $datas[$numResults][$slave_column]==='') {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = 'Slave table '.$slave_table.' column ['.$slave_column.'] required';
                    $results = []; 

                    $this->_registry->QueueLog->logging($uuid, $queue->id, $msg, '', 'ERROR');

                    break;                        
                }

                $this->_registry->QueueLog->logging($uuid, $queue->id, ($numMapping+1).') Elaboro mapping - da MASTER ['.$master_table.'::'.$master_column.'] SLAVE ['.$slave_table.'::'.$slave_column.']', 'Valore convertito ['.$datas[$numResults][$slave_column].']');

                $numResults++;
                    
            } // end foreach($masterEntitys as $masterEntity)                            
        } // foreach ($mappings as $mapping)

        /*
         * tutti i idati di un entity / table
         */
        // debug($datas);

        if($esito) 
            $esito = $this->_save($uuid, $queue, $table, $datas);
            
        if(isset($result['esito']) && !$result['esito']) {
            $esito = $result['esito'];
            $code = $result['code'];
            $uuid = $result['uuid'];
            $msg = $result['msg'];
            $results = $result['results'];
        }

        return ['esito' => $esito, 'code' => $code, 'uuid' => $uuid, 'msg' => $msg, 'results' => $results];
	}

    /*
     * conversione al nuovo valore
     */
    private function _convertingValue($mapping, $value, $master_column, $masterEntity=[]) {

        $mapping_type_code = $mapping->mapping_type->code;

        $data = '';
        switch ($mapping_type_code) {
            case 'FUNCTION':
                if(!empty($masterEntity) && !empty($masterEntity->{$master_column}))
                    $data = $this->_registry->{$this->component}->{$value}($masterEntity->{$master_column});
                else
                    $data = $this->_registry->{$this->component}->{$value}();
            break;
            case 'CURRENTDATE':
                $data = new Time(date('Y-m-d'));
            break;
            case 'CURRENTDATETIME':
                $data = new Time(date('Y-m-d H:i:s'));
            break;
            case 'INNER_TABLE_PARENT':
                $queue_table_id = $mapping->queue_table->table_id;

                // debug($this->last_insert_ids);
                // debug($queue_table_id);

                if(isset($this->last_insert_ids[$queue_table_id]))
                    $data = $this->last_insert_ids[$queue_table_id];
                else {
                    $this->_registry->QueueLog->logging($this->uuid, $this->queue_id, 'INNER_TABLE_PARENT non esiste la chiave '.$queue_table_id.'  nell array ', $this->last_insert_ids, 'ERROR');
                }
            break;
            case 'PARAMETER-EXT':
                $data = $this->controller->request->getData($value);
            break;
            case 'DEFAULT':
                if(!empty($masterEntity)) {
                    if(strpos($master_column, '->')===false) {
                       
                        if (is_object($masterEntity->{$master_column})) {
                            if($masterEntity->{$master_column} instanceof \Cake\I18n\FrozenTime) {
                                $data = date_format($masterEntity->{$master_column},"Y-m-d");
                                $data = $masterEntity->{$master_column};
                            }
                            else
                                $data = $masterEntity->{$master_column};
                        } 
                        else    
                            $data = $masterEntity->{$master_column};
                    }
                    else {
                        // ex capicommesse->Firma 
                        list($one, $two) = explode('->', $master_column);
                        if($masterEntity->has($one))
                            $data = $masterEntity->{$one}->{$two};
                    }                        
                }
                else
                    $data = $value;
            break;
            default:
                die("mapping type [$mapping_type_code] non consentito");
            break;
        } // switch ($mapping_type_code)

        return  $data;
    }  
    
    private function _getModelContain($master_entity) {
        $results = [];
        switch (strtolower($master_entity)) {
            case 'pibofintdoc':
                $results = ['TipiDocumenti', 'Capicommesse'];
                // $this->loadModel('App\Model\Table\Dwwe\TipiDocumentiTable');
                // $this->loadModel('App\Model\Table\Dwwe\CapicommesseTable');
                $this->controller->loadModel('App\Model\Table\Dwwe\TipiDocumentiTable');
                $this->controller->loadModel('App\Model\Table\Dwwe\CapicommesseTable');
                break;
            case 'pibofdetdoc':
                break;
        }
        return $results;
    }
}