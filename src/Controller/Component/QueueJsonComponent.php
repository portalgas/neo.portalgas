<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\I18n\Time;
use Cake\Filesystem\File;
use \Swaggest\JsonSchema\Structure\ClassStructure;
use \Swaggest\JsonSchema\Schema;

class QueueJsonComponent extends QueueComponent {

    private $controller;
    private $action;
    private $uuid;
    private $queue_id;
    protected $_registry;
    private $results = [];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;

        $this->_registry->load('QueueLog');
        // debug($this->_registry->loaded());

        $this->controller = $registry->getController(); // strtolower($controller->name)
        $this->action = strtolower($this->controller->request->action);        
    }

	/* 
	 * $source contentuto del file Json
	 * $request ha organization_id
	 */
	public function getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request) {

        $debug = true;

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
        $this->_registry->load($this->component); // custom MappingAnotherPortal

        $organization_id = $request['organization_id'];

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
             * path
             */
            if(!empty($mapping->master_json_path)) {
                
                $value_jsons = $this->_getValueJson($source, $mapping->master_json_path);
                if($debug) debug($value_jsons);

                foreach($value_jsons as $value_json) {

                    if($debug) debug($mapping->master_json_path.' value_jsons ['.$value_json.']');

                    $datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $value, $request, $value_json);

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

                    if($mapping->has('mapping_value_type')) {
                        
                        // debug($mapping->master_json_path.' '.$mapping->mapping_value_type->match.' '.$value_json);
                        
                        $function = $mapping->mapping_value_type->match;
                        if(!$function($datas[$numResults][$slave_column])) {
                            if($mapping->mapping_value_type->is_force_value) {
                                $factory_force_value = $mapping->mapping_value_type->factory_force_value;
                                $datas[$numResults][$slave_column] = $this->{$factory_force_value}($datas[$numResults][$slave_column], $slave_column);
                            }
                        }
                        else {
                            $esito = false;
                            $code = 500;
                            $msg = 'Il tag '.$mapping->master_json_path.' dev\'essere '.$mapping->mapping_value_type->match.' mentre vale '.$datas[$numResults][$slave_column];
                            break;
                        }

                    } // end if($mapping->has('mapping_value_type')

                    $this->_registry->QueueLog->logging($uuid, $queue->id, ($numMapping+1).') Elaboro mapping - elaboro da SLAVE ['.$slave_table.'::'.$slave_column.']', 'Valore convertito ['.$datas[$numResults][$slave_column].']');

                    // debug('mapping->master_json_path '.$mapping->master_json_path.' - value_json '.$value_json);
                    $numResults++;

                } // foreach($value_jsons as $value_json)
                
            } // end if(!empty($mapping->master_json_path))
            else {
                /* 
                 * il valore non e' mappato nell'json $mapping->master_json_path
                 */
                $datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $value, $request);

                $datas[$numResults][$slave_column] = $this->_defaultValue($datas[$numResults][$slave_column], $mapping->is_required, $mapping->value_default, false);

                if($mapping->is_required && $datas[$numResults][$slave_column]==='') {
                    $esito = false;
                    $code = 500;
                    $uuid = $uuid;
                    $msg = 'Slave table '.$slave_table.' column ['.$slave_column.'] required';
                    $results = []; 

                    $this->_registry->QueueLog->logging($uuid, $queue->id, $msg, '', 'ERROR');
                }

                if($mapping->has('mapping_value_type')) {
                    
                    // debug($mapping->master_json_path.' '.$mapping->mapping_value_type->match.' '.$value_json);
                    
                    $function = $mapping->mapping_value_type->match;
                    if(!$function($datas[$numResults][$slave_column])) {
                        if($mapping->mapping_value_type->is_force_value) {
                            $factory_force_value = $mapping->mapping_value_type->factory_force_value;
                            $datas[$numResults][$slave_column] = $this->{$factory_force_value}($datas[$numResults][$slave_column], $slave_column);
                        }
                    }
                    else {
                        $esito = false;
                        $code = 500;
                        debug($mapping);
                        $msg = 'Il tag '.$mapping->master_json_path.' dev\'essere '.$mapping->mapping_value_type->match.' mentre vale '.$datas[$numResults][$slave_column];
                        break;
                    }

                } // end if($mapping->has('mapping_value_type')

                $this->_registry->QueueLog->logging($uuid, $queue->id, ($numMapping+1).') Elaboro mapping - elaboro da SLAVE ['.$slave_table.'::'.$slave_column.']', 'Valore convertito ['.$datas[$numResults][$slave_column].']');

                $numResults++;
            } // end if(!empty($mapping->master_json_path))

        } // foreach ($mappings as $mapping)

        /*
         * tutti i dati di una entity / table alla volta
         * solo il primo elemento dell'array ha tutti i campi, gli altri solo quelli che arrivano dall'json
         */
        
        foreach($datas as $numResult => $data) {
            if($numResult>0) {
                $datas[$numResult] = array_merge($datas[0], $datas[$numResult]);

                if(isset($datas[0]['id'])) // per Articles che e' il max
                    $datas[$numResult]['id'] = ($datas[0]['id']+$numResult);
            }
        }

        if($esito) 
            $result = $this->_save($uuid, $queue, $table, $datas, $organization_id);

        if(isset($result['esito']) && !$result['esito']) {
            $esito = $result['esito'];
            $code = $result['code'];
            $uuid = $result['uuid'];
            $msg = $result['msg'];
            $results = $result['results'];
        }

        $results = ['esito' => $esito, 'code' => $code, 'uuid' => $uuid, 'msg' => $msg, 'results' => $results];

        return $results; 
	}

    /*
     * conversione al nuovo valore
     */
    private function _convertingValue($mapping, $value, $request, $value_json='') {

        $mapping_type_code = $mapping->mapping_type->code;

        $organization_id = 0;
        if(!empty($request['organization_id']))
            $organization_id = $request['organization_id'];

        $data = '';
        switch ($mapping_type_code) {
            case 'FUNCTION':
                $data = $this->_registry->{$this->component}->{$value}($organization_id, $value_json);
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
                $data = $request[$value];
            break;
            case 'DEFAULT':
                $data = $value_json;
            break;
            default:
                die("mapping type [$mapping_type_code] non consentito");
            break;
        } // switch ($mapping_type_code)

        return  $data;
    }  

    /*
     * https://github.com/swaggest/php-json-schema
     *
     * json: contenuto del file json (da upload o service ecomomia solidale)
     * schema_json
     */
    // public function validateSchema($file_path_full, $file_schema_path_full, $debug=false) {
    public function validateSchema($json, $schema_json, $debug=false) {
        
        $results = [];
        $results['esito'] = true;

        if($debug) debug($json);       
        if($debug) debug($schema_json);
        
        try {
            $schema = Schema::import($schema_json);
            $schemaResults = $schema->in($json);
            $results['msg'] = $schemaResults;
            if($debug) debug($schemaResults);            
        }
        catch(\Swaggest\JsonSchema\Exception\ObjectException $e) {
           $results['esito'] = false;
           $results['msg'] = $e->getMessage();
        } 
        catch(\Swaggest\JsonSchema\Exception\TypeException $e) {
           $results['esito'] = false;
           $results['msg'] = $e->getMessage();
        } 
        catch(\Swaggest\JsonSchema\Exception\LogicException $e) {
           $results['esito'] = false;
           $results['msg'] = $e->getMessage();
        }
        catch(\Swaggest\JsonSchema\Exception\StringException $e) {
           $results['esito'] = false;
           $results['msg'] = $e->getMessage();
        } 

        return $results;
    }

    /*
     * non + utilizzato, sostituita da validateSchema
     *
     * validazione per ogni path $mapping->master_json_path
     *      $mapping->mapping_type->code == 'DEFAULT'
     *      $mapping->is_required
     *      empty($mapping->value_default)
     *      $mapping->mapping_value_type->matchù
     *
     * json: contenuto del file json (da upload o service ecomomia solidale)
     */
    public function validate($json, $queuesCode, $organization_id, $debug=false) {

        $debug = false;
        
        $esito = true;
        $code = 200;
        $msg = '';
        $results = [];

        if(empty($json) || !$json) {
            $esito = false;
            $code = 500;
            $msg = 'File json non e\' un Json valido';
        } 

        /*
         * queue
         */
        if($esito) {
            $queuesTable = TableRegistry::get('Queues');

            $queue = $queuesTable->findByCode($queuesCode);    
            
            if(!isset($queue) || !$queue->has('queue_tables')) {
                $esito = false;
                $code = 500;
                $msg = 'La coda '.$queuesCode.' non ha configurato le tabelle (queue_tables)';           
            }
        }

        if($esito) {
            $tables = $this->_getTables($queue);
            if(empty($tables)) {
                $esito = false;
                $code = 500;
                $msg = 'La coda '.$queuesCode.' non ha tabelle attive (queue_tables)';           
            }
        } // end if($esito)

        if($esito)
        foreach($tables as $table_id => $table) {

            if($esito===false)
                break;
                
                /*
                 * mappings per ogni tabella slave
                 */
                $mappings = $this->_getMappings($queue, $table_id);
                // debug($mappings);
                if(empty($mappings) || $mappings->count()==0) {
                    $esito = false;
                    $code = 500;
                    $msg = 'La coda '.$queuesCode.' non configurato le mappature (mappings)';          
                }

                foreach ($mappings as $numMapping => $mapping) {

                    if($esito===false)
                        break;

                    // if($debug) debug('master_json_path '.$mapping->master_json_path);
                    // if($debug) debug('mapping_type->code '.$mapping->mapping_type->code);

                    if(!empty($mapping->master_json_path) && $mapping->mapping_type->code == 'DEFAULT') {
                        
                        $value_jsons = $this->_getValueJson($json, $mapping->master_json_path);
        
                        foreach($value_jsons as $value_json) {
                            if($debug) debug($mapping->master_json_path.' value_jsons ['.$value_json.']');

                            if($value_json=='' && $mapping->is_required && empty($mapping->value_default)) {
                                $esito = false;
                                $code = 500;
                                $msg = 'Il tag '.$mapping->master_json_path.' e\' obbligatorio';
                                break;
                            }

                            if($mapping->has('mapping_value_type')) {
                                
                                // debug($mapping->master_json_path.' '.$mapping->mapping_value_type->match.' '.$value_json);
                                
                                $function = $mapping->mapping_value_type->match;
                                if(!$function($value_json)) {
                                    if($mapping->mapping_value_type->is_force_value) {
                                        $factory_force_value = $mapping->mapping_value_type->factory_force_value;
                                        $value_json = $this->{$factory_force_value}($value_json);
                                    }
                                }
                                else {
                                    $esito = false;
                                    $code = 500;
                                    $msg = 'Il tag '.$mapping->master_json_path.' dev\'essere '.$mapping->mapping_value_type->match.' mentre vale '.$value_json;
                                    break;
                                }

                            } // end if($mapping->has('mapping_value_type')

                        } // foreach($value_jsons as $value_json) 
                          
                    } // end if(!empty($mapping->master_json_path) && $mapping->mapping_type->code == 'DEFAULT')

                } // foreach ($mappings as $mapping)
        } // end foreach($tables as $table_id => $table)

        $results['esito'] = $esito;
        $results['code'] = $code;
        $results['msg'] = $msg;
        // debug($results);

        return $results;
    }

    private function _extract($json, $master_json_paths, $current_item_array, $debug=false) {

        $tot_item_array = count($master_json_paths);

        for($current_item_array; $current_item_array < $tot_item_array; $current_item_array++) {

            $node = $master_json_paths[$current_item_array];

            if($debug) debug(($current_item_array+1).' di '.$tot_item_array.' nodo: <b>'.$node.'</b> da estrarre dal json');

            if(is_object($json)) {
                if($debug) debug('before OBJECT estrazione rispetto al nodo '.$node);
                $json = $json->{$node};
                if($debug) debug('after OBJECT estrazione rispetto al nodo '.$node);
                // if($debug) debug($json);            
            }
            else 
            if(is_string($json)) {
                if($debug) debug('before STRING estrazione rispetto al nodo '.$node);
                //if($debug) debug($json);
                $json = $json->{$node};
                if($debug) debug('after STRING estrazione rispetto al nodo '.$node);
                //if($debug) debug($json);
            }
            else
            if(is_array($json)) {

                if($debug) debug('before ARRAY['.count($json).'] estrazione rispetto al nodo '.$node);
                //if($debug) debug($json);
                $current_item_array++;
                foreach ($json as $j) {
                    
                    if(isset($j->{$node})) {
                        
                        /*
                         * arrivato all'ultimo => valore da estrarre
                         */
                        if($current_item_array == $tot_item_array) {
                            $this->results[] = $j->{$node};
                        } // end if(($current_item_array+1) == $tot_item_array)

                       $this->_extract($j->{$node}, $master_json_paths, $current_item_array);
                       if($debug) debug('after ARRAY[n] estrazione rispetto al nodo '.$node);
                       //if($debug) debug($json);
                    } // if(isset($j->{$node}))
                } // foreach ($json as $j)
            }
            else {
                debug("type non previsto!");
                var_dump($json);
                exit;
            }

            /*
             * arrivato all'ultimo => valore da estrarre
             */
            if(($current_item_array+1) == $tot_item_array) {
                if($debug) debug($json);
                $this->results[] = $json;
            } // end if(($current_item_array+1) == $tot_item_array)
        }
    }

    /*
     * estraggo il valore dal json (string / array) scomponento $mapping->master_json_path
     *      ex blocks[0]->supplier->products[]->name
     */
    private function _getValueJson($json, $master_json_path) {
        
        $debug = true;

        $master_json_path = 'blocks->supplier->products->name';
        $master_json_path = 'subject->address->street';
        $master_json_path = 'subject->name';
        $master_json_path = 'subject->contacts->type';
        $master_json_path = 'blocks->supplier->contacts->type';     
        debug($master_json_path);
        // debug($json);

        $current_item_array = 0;
        $master_json_paths = explode('->', $master_json_path);
        // debug($master_json_paths);
        
        $this->_extract($json, $master_json_paths, $current_item_array, $debug);

        debug($this->results);
        exit;
        return $this->results;        
    }    
}