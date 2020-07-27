<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Filesystem\File;

class ImportFilesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Upload');
        $this->loadComponent('Auths');        
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(!$this->Authentication->getIdentity()->acl['isSuperReferente'] || !$this->Authentication->getIdentity()->acl['isReferentGeneric']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }

    /*
     * https://github.com/madbob/GDXP/blob/master/DOC.md
     *
     * q = per cosa ricercare (id / vat)
     * w = parola da ricercare
     */
    public function jsonToService()
    {
        $debug = false;
        $continua = true;
        
        $this->loadComponent('QueueJson');

        $queuesCode = Configure::read('Gdxp.queue.code');
        $organization_id = $this->Authentication->getIdentity()->organization->id;

        $q = $this->request->getQuery('q'); // q = per cosa ricercare (id / vat)
        $w = $this->request->getQuery('w'); // w = parola da ricercare
        $service = $this->request->getQuery('service');
        if(empty($service)) 
            $service='ECONOMIASOLIDALE';

        if($debug) debug($w);

        $url = '';
        switch ($service) {
            case 'ECONOMIASOLIDALE':
                $url = Configure::read('Gdxp.articles.index.url').'?'.$q.'='.$w;
                break; 
            default:
                $continua = false;
                die('valore service ['.$service.'] non previsto');
                break;
        }
        if($debug) debug($url);
        $this->set(compact('url'));

        if($continua) {
            $http = new Client();
            $response = $http->get($url);
            //$file_content = $response->body();
            $file_content = $response->getStringBody(); // getJson()/getXml() 

            if(empty($file_content)) {
                $continua = false;
                $this->Flash->error('dati da '.$url.' non pervenuti');  
            }
        }

        if($continua) {
            $json = json_decode($file_content);
            // if($debug) debug($json);
            if(empty($json)) {
                $continua = false;
                $this->Flash->error('dati da '.$url.' non in formato json corretto');  
            }            
        }

        if($continua) {
            $schema_json = $this->_getGdxpSchemaJson();
            // if($debug) debug($schema_json);

            $results = $this->QueueJson->validateSchema($json, $schema_json);
            if($debug) debug($results);
            if(isset($results['esito']) && !$results['esito']) {
                $continua = false;
                $this->Flash->error($results['msg']);  
            }

        } // end if($continua)

        /*
         * import dati queue
         */
        if($continua) {

            $request['code'] = $queuesCode;                
            $request['organization_id'] = $organization_id;
            $request['id'] = $json; 

            $results = $this->QueueJson->queue($request, $debug);
            // debug($results);
            if(isset($results['esito']) && !$results['esito']) {
                $continua = false;
                // debug($results); 
                if(!empty($results['msg']))
                    $msg = $results['msg'];
                else
                    $msg = __('Gdxp-ServiceJsonValidateKo');
                $this->Flash->error($msg);

                $this->set('errors', $results['results']);
            } // if(isset($results['esito']) && !$results['esito']) 

        } // end if($continua)

        if($continua) {

            /*
             * ricerco il produttore appena salvato
             */
            $suppliersTable = TableRegistry::get('Suppliers');

            $supplier = $suppliersTable->findByPiva($w)
                    ->contain(['SuppliersOrganizations' => function($q)  use ($organization_id) {
                                            return $q
                                                ->where(['organization_id' => $organization_id]);
                                    }])
                    ->first();
            // debug($supplier);
            $this->set(compact('supplier'));
                    
            $this->Flash->success(__('Gdxp-ServiceJsonValidateOk'));
        }
    }

	/*
	 * https://github.com/madbob/GDXP/blob/master/DOC.md
	 */
    public function json()
    {
        $debug = false;
        $continua = true;

        $this->loadComponent('QueueJson');

        $queuesCode = Configure::read('Gdxp.queue.code');
        $organization_id = $this->Authentication->getIdentity()->organization_id;

        if ($this->request->is('post')) {

            if($debug) debug($this->request->getData());

            /*
             * uplaod del file
             */
            $config = [] ;
            $config['upload_path']    = WWW_ROOT;          
            $config['allowed_types']  = ['txt', 'json'];            
            $config['max_size']       = 0;   
            $config['overwrite']      = true;
            $config['encrypt_name']  = false;
            $config['remove_spaces'] = true;         
            $this->Upload->init($config);  
            $results = $this->Upload->upload();
            if ($results===false){
                $continua = false;
                $error = $this->Upload->errors();
                if($debug) debug($error);
                $this->Flash->error($error[0]);
            } 

            $file_path_full = $config['upload_path'].$this->Upload->output('file_name');
            if($debug) debug('file_path_full '.$file_path_full);

            /*
             * validazione https://github.com/swaggest/php-json-schema
             */
            if($continua) {

                $file = new File($file_path_full);   
                $file_content = $file->read(true, 'r');
                $json = json_decode($file_content);
                // debug($json);

                $schema_json = $this->_getGdxpSchemaJson();

                $results = $this->QueueJson->validateSchema($json, $schema_json);
                if($debug) debug($results);
                if(isset($results['esito']) && !$results['esito']) {
                    $continua = false;
                    $this->Flash->error($results['msg']);  
                }
            } // end if($continua)

            /*
             * import dati queue
             */
            if($continua) {

                $request['code'] = $queuesCode;                
                $request['organization_id'] = $organization_id;
                $request['id'] = $json;

                $results = $this->QueueJson->queue($request, $debug);
                // debug($results);
                if(isset($results['esito']) && !$results['esito']) {
                    $continua = false;
                    // debug($results);
                    if(!empty($results['msg']))
                        $msg = $results['msg'];
                    else
                        $msg = __('UploadValidateKo');
                    $this->Flash->error($msg);  

                    $this->set('errors', $results['results']);
                } // if(isset($results['esito']) && !$results['esito']) 

            } // end if($continua)

            if($continua) {
                $this->Flash->success(__('UploadValidateOk'));
            }

        } // end post              
    }

    public function xml()
    {
        $this->loadComponent('QueueXml');

        $queuesCode = Configure::read('Gdxp.queue.code');
        $organization_id = $this->Authentication->getIdentity()->organization_id;

        $debug = false;
        $continua = true;

        if ($this->request->is('post')) {

            if($debug) debug($this->request->getData());

            /*
             * uplaod del file
             */
            $config = [] ;
            $config['upload_path']    = WWW_ROOT;          
            $config['allowed_types']  = 'xml';            
            $config['max_size']       = 0;   
            $config['overwrite']      = true;
            $config['encrypt_name']  = false;
            $config['remove_spaces'] = true;         
            $this->Upload->init($config);  

            // $results = $this->Upload->readXml(); // return object(SimpleXMLElement)
            $results = $this->Upload->upload();
            if ($results===false){
                $continua = false;
                $error = $this->Upload->errors();
                debug($error);
                $this->Flash->error($error[0]);
            } 

            $file_path_full = $config['upload_path'].$this->Upload->output('file_name');
            // debug($file_path_full);

            /*
             * validazione
             */
            if($continua) {

                $results = $this->QueueXml->validate($file_path_full, $queuesCode, $organization_id, $debug);
                if(isset($results['esito']) && !$results['esito']) {
                    $continua = false;
                    $this->Flash->error($results['msg']);  
                }
            } // end if($continua)

            /*
             * import dati queue
             */
            if($continua) {

                $request['code'] = $queuesCode;                
                $request['organization_id'] = $organization_id;
                $request['id'] = $file_path_full; 

                $results = $this->QueueXml->queue($request, $debug);
                // debug($results);
                if(isset($results['esito']) && !$results['esito']) {
                    $continua = false;
                    // debug($results);
                    if(!empty($results['msg']))
                        $msg = $results['msg'];
                    else
                        $msg = __('UploadValidateKo');
                    $this->Flash->error($msg);  

                    $this->set('errors', $results['results']);
                }

            } // end if($continua)

            if($continua) {
                $this->Flash->success(__('UploadValidateOk'));
            }

        } // end post              
    }

    public function csv()
    {
        $debug = true;
        $continua = true;

        if ($this->request->is('post')) {

            debug($this->request->getData());

            $config = [] ;
            // $config['upload_path']    = WWW_ROOT;          
            $config['allowed_types']  = 'csv';            
            $config['max_size']       = 0;   
            $config['overwrite']      = true;
            $config['encrypt_name']  = false;
            $config['remove_spaces'] = true;         
            $this->Upload->init($config);  

            $results = $this->Upload->readCsv();
            if ($results===false){
                $error = $this->Upload->errors();
                debug($error); 
            } else {

                debug($results);
                // while (($data = fgetcsv($handle, 1000, $deliminatore)) !== false)


                // debug($this->Upload->output('file_name'));
                // debug($this->Upload->output());
            }
        } // end post  
    }

    private function _getGdxpSchemaJson() {

        $file_schema_path_full = Configure::read('Gdxp.schema_path');

        $file = new File($file_schema_path_full);   
        $file_content = $file->read(true, 'r');
        $schema_json = json_decode($file_content);
        // debug($schema_json);

        return $schema_json;
    }    
}