<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticlesImportExportDecorator;

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
        if(!isset($upload_results['file_name']) || empty($upload_results['file_name'])) {
            $results['esito'] = false;
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Errore di sistema!";
            $results['results'] = [];
            return $this->_response($results);            
        }

        $file_content = $this->ArticlesImportExport->read($upload_results['full_path']);
        
        $articles = new ArticlesImportExportDecorator($file_content);
        $file_content = $articles->results;

        $results['esito'] = true;
        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = $file_content;
        return $this->_response($results);         
    }

    public function import() {
        
        $debug = true;
        $errors = [];

        $results = [];
        $results['esito'] = true;
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
                  
        $request = $this->request->getData();   
        // if($debug) debug($request);

        $supplier_organization_id = $request['supplier_organization_id'];
        $select_import_fields = $request['select_import_fields'];
        $is_first_row_header = $request['is_first_row_header'];
        $file_contents = $request['file_contents'];

        $articlesTable = TableRegistry::get('Articles');

        $i=0;
        // loop rows
        foreach($file_contents as $numRow => $file_content_rows) {
            // loop cols
            $datas = [];
            foreach($file_content_rows as $numCol => $file_content) {
                $field = $select_import_fields[$numCol];
                
                $datas[$field] = trim($file_content);
                // if($debug) debug($field.' - '.$file_content);
            }
            
            $datas['organization_id'] = $this->_organization->id;
            $datas['supplier_organization_id'] = $supplier_organization_id;

            if(isset($datas['id'])) {
                // update  

                $id = (int)$datas['id'];
                if($id===0) {
                    $errors[$i] = [];
                    $errors[$i]['numRow'] = $numRow;
                    $errors[$i]['msg'] = "Identificativo articolo ".$datas['id']." non valido";
                    $i++;
                    continue;
                }

                $where = ['id' => $datas['id'],
                          'organization_id' => $this->_organization->id];        
                $article = $articlesTable->find()
                                    ->where($where)
                                    ->first();
                if(empty($article)) {
                    $errors[$i] = [];
                    $errors[$i]['numRow'] = $numRow;
                    $errors[$i]['msg'] = "Articolo con identificativo ".$datas['id']." non trovato";
                    $i++;
                    continue;
                }    
            }
            else {
                // insert
                $article = $articlesTable->newEntity();
            }
            $article = $articlesTable->patchEntity($article, $datas);
            // dd($article);
            if (!$articlesTable->save($article)) {
                $errors[$i] = [];
                $errors[$i]['numRow'] = $numRow;
                $errors[$i]['msg'] = $article->getErrors();
                $i++;
                continue;    
            }

            if($debug) debug($errors);
        } // end loop rows

        $results['esito'] = true;
        $results['code'] = 200;
        $results['errors'] = $errors;
        return $this->_response($results);          
    }    
}