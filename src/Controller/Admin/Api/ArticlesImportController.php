<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class ArticlesImportController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('ArticlesImportExport');
        $this->loadComponent('Upload');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
    
    public function upload() {
        
        $debug = false;

        $results = [];
        $results['esito'] = true;
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
                  
        $request = $this->request->getData();   
        if($debug) debug($request);

        /*
        * upload del file
        */
        $config_upload = [] ;
        $config_upload['upload_path']    = TMP.'import';          
        $config_upload['allowed_types']  = ['xlsx'];
        $config_upload['max_size']       = 0;   
        $config_upload['overwrite']      = true;
        $config_upload['encrypt_name']  = true;
        $config_upload['remove_spaces'] = true;         
        $this->Upload->init($config_upload);  
        $upload_results = $this->Upload->upload('file');
        if ($upload_results===false){
            $errors = $this->Upload->errors(); 
            $results['esito'] = false;
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $errors;
            $results['results'] = [];
            if($debug) debug($errors);
            return $this->_response($results);   
        } 
        if($debug) debug($this->Upload->output());
        $upload_results = $this->Upload->output();
        $file_name = $upload_results['file_name'];
        if(!isset($upload_results['file_name']) || empty($upload_results['file_name'])) {
            $results['esito'] = false;
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Errore di sistema!";
            $results['results'] = [];
            return $this->_response($results);            
        }

        $file_content = $this->ArticlesImportExport->read($upload_results['full_path']);

        $results['esito'] = true;
        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = $file_content;
        return $this->_response($results);         
    }
}