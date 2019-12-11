<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ImportFilesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Upload');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    public function xml()
    {
        $this->loadComponent('QueueXml');

        $queuesCode = 'GDXP-PORTALGAS';
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
                $this->Flash->error($error);
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
}