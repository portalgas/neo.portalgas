<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;
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

        // cancello file
        @unlink($upload_results['full_path']);

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
        $results['errors'] = '';
                  
        $request = $this->request->getData();   
        // if($debug) debug($request);

        $supplier_organization_id = $request['supplier_organization_id'];
        $select_import_fields = $request['select_import_fields'];
        $is_first_row_header = $request['is_first_row_header'];
        $file_contents = $request['file_contents'];

        $articlesTable = TableRegistry::get('Articles');
        $validator = $articlesTable->getValidator();

        $datas = [];
        // loop rows
        foreach($file_contents as $numRow => $file_content_rows) {
            // loop cols
            
            foreach($file_content_rows as $numCol => $file_content) {
                $field = $select_import_fields[$numCol];
                
                $datas[$numRow][$field] = trim($file_content);
                // if($debug) debug($field.' - '.$file_content);                
            } // loop cols

            /*
            * decorate datas
            */
            $datas[$numRow]['organization_id'] = $this->_organization->id;
            $datas[$numRow]['supplier_organization_id'] = $supplier_organization_id;
            $datas[$numRow]['alert_to_qta'] = 0;
            if(isset($datas[$numRow]['prezzo'])) $datas[$numRow]['prezzo'] = $this->convertImport($datas[$numRow]['prezzo']);
            if(!isset($datas[$numRow]['bio'])) $datas[$numRow]['bio'] = 'N';
            else $datas[$numRow]['bio'] = $this->_translateSiNo($datas[$numRow]['bio']);
            if(!isset($datas[$numRow]['pezzi_confezione'])) $datas[$numRow]['pezzi_confezione'] = 1;
            if(!isset($datas[$numRow]['um'])) $datas[$numRow]['um'] = 'PZ';
            if(!isset($datas[$numRow]['um_riferimento'])) $datas[$numRow]['um_riferimento'] = 'PZ';
            if(!isset($datas[$numRow]['qta'])) $datas[$numRow]['qta'] = 1.00;
            if(!isset($datas[$numRow]['qta_massima'])) $datas[$numRow]['qta_massima'] = 0;
            if(!isset($datas[$numRow]['qta_minima'])) $datas[$numRow]['qta_minima'] = 1;
            if(!isset($datas[$numRow]['qta_multipli'])) $datas[$numRow]['qta_multipli'] = 1;
            if(!isset($datas[$numRow]['qta_minima_order'])) $datas[$numRow]['qta_minima_order'] = 0;
            if(!isset($datas[$numRow]['qta_massima_order'])) $datas[$numRow]['qta_massima_order'] = 0;
            if(!isset($datas[$numRow]['stato'])) $datas[$numRow]['stato'] = 'Y';
            if(!isset($datas[$numRow]['flag_presente_articlesorders'])) $datas[$numRow]['flag_presente_articlesorders'] = 'Y'; 
            else $datas[$numRow]['flag_presente_articlesorders'] = $this->_translateSiNo($datas[$numRow]['flag_presente_articlesorders']);
            if(!isset($datas[$numRow]['category_article_id'])) {
                // estraggo la categoria di default
                $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
                $datas[$numRow]['category_article_id'] = $categoriesArticlesTable->getIsSystemId($this->_user, $this->_organization->id); 
            }
            // dd($datas);

            /*
            * validazione
            */
            $validationResults = $validator->errors($datas[$numRow]);
            $row_errors = [];
            if(!empty($validationResults)) {
                $row_errors = $this->_humanErrors($validationResults);
                $errors[$numRow] = $row_errors;
            }    

            if(empty($row_errors)) {
                /* 
                 * ctrl identificativo articolo
                 */
                if(isset($datas[$numRow]['id'])) {
                    // update  
                    $where = ['id' => $datas[$numRow]['id'],
                            'organization_id' => $this->_organization->id];
                    $article = $articlesTable->find()
                                        ->where($where)
                                        ->first();
                    if(empty($article)) {
                        $id_errors = [];
                        $id_errors[0]['field'] = 'id';
                        $id_errors[0]['field_human'] =  __('import-article-id');
                        $id_errors[0]['error'] = "Articolo con identificativo ".$datas[$numRow]['id']." non trovato";                  
                        $errors[$numRow] = $id_errors;
                    } 
                } // if(isset($datas[$numRow]['id']))
            } // end if(empty($errors)) 
        } // loop rows

        if(!empty($errors)) {
            $results['esito'] = false;
            $results['code'] = 200;
            $results['errors'] = $errors;
            return $this->_response($results);             
        }

        /*
         * validazione OK => importo
         */
        foreach($datas as $numRow => $data) {

            if(isset($data['id'])) {
                // update  
                $where = ['id' => $data['id'],
                          'organization_id' => $this->_organization->id];        
                $article = $articlesTable->find()
                                    ->where($where)
                                    ->first();
                if(empty($article)) {
                    // non dovrebbe capitare, controllato precedentemente                  
                    continue;
                }    
            }
            else {
                // insert
                $article = $articlesTable->newEntity();
                $data['id'] = $this->getMax($articlesTable, 'id', ['organization_id' => $this->_organization->id]);
                $data['id']++;
            }
            $article = $articlesTable->patchEntity($article, $data);
            // dd($article);
            if (!$articlesTable->save($article)) {
                Log::error($article->getErrors());
                // dd($article->getErrors());
                continue;
            }
        } // end loop datas

        $results['esito'] = true;
        $results['code'] = 200;
        $results['errors'] = $errors;
        return $this->_response($results);          
    }    

    private function _humanErrors($validationResults) {
        
        $i=0;
        $results = [];
        foreach($validationResults as $field => $validationResult) {
            // dd($validationResult);
            $results[$i]['field'] = $field;
            $results[$i]['field_human'] = __('import-article-'.$field);
            foreach($validationResult as $validation) {
                $results[$i]['error'] = $validation;
            }
            $i++;
        }
        return $results;
    }

    private function _translateSiNo($value) {
        switch(strtolower($value)) {
            case 'si':
                return 'Y';
            break;
            case 'no':
                return 'N';
            break;
            default:
                return 'N';
            break;
        }
    }
}