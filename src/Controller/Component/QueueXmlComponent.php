<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\I18n\Time;

class QueueXmlComponent extends QueueComponent {

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

        $this->controller = $registry->getController(); // strtolower($controller->name)
        $this->action = strtolower($this->controller->request->action);        
    }

	/* 
	 * $source contentuto del file XML
	 * $request ha organization_id
	 */
	public function getDatas($uuid, $queue, $mappings, $table, $table_id, $source, $request) {

        /*
         * valori return
         */
        $esito = true;
        $code = '200';
        $msg = '';
        $results = []; 

        $this->uuid = $uuid;
        $this->queue_id = $queue->id;

        $component = $this->_getComponent($queue);
        $this->_registry->load($component); // custom MappingAnotherPortal

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
             * xpath
             */
            if(!empty($mapping->master_xml_xpath)) {
                
                $value_xml_xpath = $source->xpath($mapping->master_xml_xpath);
                
                if(!empty($value_xml_xpath) && count($value_xml_xpath)===1) {
                	/*
                	 * e' una stringa
                	 */
                    $value_xmls = (string)$value_xml_xpath[0];
					$datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $component, $value, $organization_id, $value_xmls);

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

					// debug('mapping->master_xml_xpath '.$mapping->master_xml_xpath.' - value_xmls '.$value_xmls);
					$numResults++;
                }
                else {
                	/*
                	 * e' una array (ex articoli)
                	 */                	
                    $value_xmls = $value_xml_xpath;
                    foreach($value_xmls as $value_xml) {
                        $value_xml = (string)$value_xml[0];
                        $datas[$numResults][$slave_column] = $this->_convertingValue($mapping, $component, $value, $organization_id, $value_xml);

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

                        // debug('mapping->master_xml_xpath '.$mapping->master_xml_xpath.' - value_xmls '.$value_xmls);

                        $numResults++;
                    }                    
                    // debug('mapping->master_xml_xpath '.$mapping->master_xml_xpath.' - value_xmls ARRAY');
                }
            } // end if(!empty($mapping->master_xml_xpath))
        } // foreach ($mappings as $mapping)

        /*
         * tutti i idati di un entity / table
         */
        // debug($datas);

        if($esito) 
            $result = $this->_save($uuid, $queue, $table, $datas);

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
    private function _convertingValue($mapping, $component, $value, $organization_id, $value_xml) {

        $mapping_type_code = $mapping->mapping_type->code;

        $data = '';
        switch ($mapping_type_code) {
            case 'FUNCTION':
                $data = $this->_registry->{$component}->{$value}($organization_id, $value_xml);
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
                $data = $this->request->getData($value);
            break;
            case 'DEFAULT':
                $data = $value_xml;
            break;
            default:
                die("mapping type [$mapping_type_code] non consentito");
            break;
        } // switch ($mapping_type_code)

        return  $data;
    }  

    /*
     * xml_data object(SimpleXMLElement) o file_path_full
     *
     * validazione per ogni xpath $mapping->master_xml_xpath
     *      $mapping->mapping_type->code == 'DEFAULT'
     *      $mapping->is_required
     *      empty($mapping->value_default)
     *      $mapping->mapping_value_type->match
     */
    public function validate($xmlData, $queuesCode, $organization_id, $debug=false) {

        $esito = true;
        $code = 200;
        $msg = '';
        $results = [];

        /*
         * estaggo SimpleXMLElement da $xml_data  
         */
        if(is_string($xmlData)) 
            $xml_data = @simplexml_load_file($xmlData);
        else
            $xml_data = $xmlData;

        if(!$xml_data) {
            $esito = false;
            $code = 500;
            $msg = 'File '.$xmlData.' non e\' un XML valido';
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

                    $numResults = 0;

                    /*
                     * xpath
                     */
                    if($debug) debug('master_xml_xpath '.$mapping->master_xml_xpath);
                    if($debug) debug('mapping_type->code '.$mapping->mapping_type->code);

                    if(!empty($mapping->master_xml_xpath) && $mapping->mapping_type->code == 'DEFAULT') {
                        
                        $value_xml_xpath = $xml_data->xpath($mapping->master_xml_xpath);

                        if(!empty($value_xml_xpath) && count($value_xml_xpath)===1) {
                            /*
                             * e' una stringa
                             */
                            $value_xml = (string)$value_xml_xpath[0];
                            if($debug) debug('value_xmls ['.$value_xml.']');

                            if($value_xml=='' && $mapping->is_required && empty($mapping->value_default)) {
                                $esito = false;
                                $code = 500;
                                $msg = 'Il tag '.$mapping->master_xml_xpath.' e\' obbligatorio';
                                break;
                            }

                            if($mapping->has('mapping_value_type')) {
                                // $mapping->mapping_value_type->match
                            } // end if($mapping->has('mapping_value_type')
                             
                            $numResults++;
                        }
                        else {
                            /*
                             * e' una array (ex articoli)
                             */                 
                            $value_xmls = $value_xml_xpath;
                            foreach($value_xmls as $value_xml) {

                                $value_xml = (string)$value_xml[0];
                                if($debug) debug('value_xmls ['.$value_xml.']');

                                if($value_xml=='' && $mapping->is_required && empty($mapping->value_default)) {
                                    $esito = false;
                                    $code = 500;
                                    $msg = 'Il tag '.$mapping->master_xml_xpath.' e\' obbligatorio';
                                    break;
                                }

                                if($mapping->has('mapping_value_type')) {
                                    // $mapping->mapping_value_type->match
                                } // end if($mapping->has('mapping_value_type')

                                $numResults++;
                            }
                        }
                    } // end if(!empty($mapping->master_xml_xpath))
                } // foreach ($mappings as $mapping)
        } // end foreach($tables as $table_id => $table)

        $results['esito'] = $esito;
        $results['code'] = $code;
        $results['msg'] = $msg;

        return $results;
    }
}