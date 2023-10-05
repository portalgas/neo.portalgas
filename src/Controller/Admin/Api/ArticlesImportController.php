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
        $config_upload['allowed_types']  = ['xlsx', 'xls'];
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
        // @unlink($upload_results['full_path']);

        $results['esito'] = true;
        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = $file_content;
        return $this->_response($results);         
    }

    public function import() {
        
        $results = [];
        $request = $this->request->getData();   
        // if($debug) debug($request);

        $select_import_fields = $request['select_import_fields'];
        if(empty($select_import_fields)) {
            dd("select_import_fields required!");
        }
    
        if(in_array('codice-id', $select_import_fields)) {
            /*
            * importazione da root dei produttori (ex OfficinaNaturae)
            *  il codice e' identificativo
            */
            $results = $this->_importToSupplier($request);
        }
        else {
            /*
            * importazione da parte dei gasisti
            */
            $results = $this->_importToGas($request);
        }

        return $this->_response($results);         
    }

    /*
    * importazione da root dei produttori (ex OfficinaNaturae)
    *  il codice e' identificativo 
    ctrl se esiste => update / se no insert
    */
    private function _importToSupplier($request) {

        $errors = [];

        $results = [];
        $results['esito'] = true;
        $results['code'] = 200;
        $results['errors'] = '';

        $supplier_organization_id = $request['supplier_organization_id'];
        $file_contents = $request['file_contents'];
        $select_import_fields = $request['select_import_fields'];

        $articlesTable = TableRegistry::get('Articles');
        $validator = $articlesTable->getValidator();
        
        /*         
         * dati produttore, ottengo l'organization_id
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $where = ['id' => $supplier_organization_id];        
        $suppliersOrganization = $suppliersOrganizationsTable->find()
                                                            ->where($where)
                                                            ->first();
        if(empty($suppliersOrganization))
            dd("produttore non trovato con id [$supplier_organization_id]"); 

        $datas = [];
        // loop rows
        foreach($file_contents as $numRow => $file_content_rows) {
            // loop cols
            foreach($file_content_rows as $numCol => $file_content) {
                $field = $select_import_fields[$numCol];
         
                switch($field) {
                    case 'codice-id':
                        // il codice arriva codice-id per identificare la gestione di root con il produttore
                        $field = 'codice';
                        $datas[$numRow][$field] = trim($file_content);
                    break;
                    case 'qta_um':
                        // nella medesima cella c'e' qta e um (1 KG)
                        list($datas[$numRow]['qta'], $datas[$numRow]['um']) = $this->_explodeQtaUm(trim($file_content));
                    break;
                    default:
                        $datas[$numRow][$field] = trim($file_content);
                    break;
                }
                // if($debug) debug($field.' - '.$file_content);                
            } // loop cols

            /*
            * decorate datas
            */
            $datas[$numRow] = array_merge($datas[$numRow], $this->_decorate($this->_organization->id, $supplier_organization_id, $datas[$numRow]));
     
            /*
            * validazione
            */   
            $validationResults = $validator->errors($datas[$numRow]);
            $row_errors = [];
            if(!empty($validationResults)) {
                $row_errors = $this->_humanErrors($validationResults);
                $errors[$numRow] = $row_errors;
            }    
        } // loop rows

        if(!empty($errors)) {
            $results['esito'] = false;
            $results['code'] = 200;
            $results['errors'] = $errors;
            return $results;             
        }

        /*
         * validazione OK => setto flag_presente_articlesorders a N
         */
        $where = ['organization_id' => $suppliersOrganization->organization_id,
        'supplier_organization_id' => $supplier_organization_id];
        $articlesTable->updateAll(['flag_presente_articlesorders' => 'N'], $where);
     
        /*
         * validazione OK => importo
        */

        foreach($datas as $numRow => $data) {

            $where = ['codice' => $data['codice'],
                        'organization_id' => $suppliersOrganization->organization_id,
                        'supplier_organization_id' => $supplier_organization_id];        
            $article = $articlesTable->find()
                                ->where($where)
                                ->first();
            if(empty($article)) {
                $article = $articlesTable->newEntity();
                $data['id'] = $this->getMax($articlesTable, 'id', ['organization_id' => $suppliersOrganization->organization_id]);
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

        return $results;
    }    

    /*
    * importazione da parte dei gasisti
    */    
    private function _importToGas($request) {

        $errors = [];

        $results = [];
        $results['esito'] = true;
        $results['code'] = 200;
        $results['errors'] = '';

        $supplier_organization_id = $request['supplier_organization_id'];
        $file_contents = $request['file_contents'];
        $select_import_fields = $request['select_import_fields'];

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
            * ctrl identificativo articolo
            */
            $id_errors = [];
            if(isset($datas[$numRow]['id'])) {

                if(is_string($datas[$numRow]['id'])) {
                    $id_errors[0]['field'] = 'id';
                    $id_errors[0]['field_human'] =  __('import-article-id');
                    $id_errors[0]['error'] = "Dev'essere un numero";
                    $errors[$numRow] = $id_errors;                    
                }
                else {
                    // update  
                    $where = ['id' => $datas[$numRow]['id'],
                            'organization_id' => $this->_organization->id,
                            'supplier_organization_id' => $supplier_organization_id];
                    $article = $articlesTable->find()
                                        ->where($where)
                                        ->first();
                    if(empty($article)) {
                        $id_errors[0]['field'] = 'id';
                        $id_errors[0]['field_human'] =  __('import-article-id');
                        $id_errors[0]['error'] = "Articolo con identificativo ".$datas[$numRow]['id']." non trovato";
                        $errors[$numRow] = $id_errors;
                    } 
                    else {
                        $datas[$numRow] = array_merge($article->toArray(), $datas[$numRow]);
                    }

                }
            } // if(isset($datas[$numRow]['id']))
                
            // dd($datas[$numRow]);

            if(empty($id_errors)) {            
                /*
                * decorate datas
                */
                $datas[$numRow] = array_merge($datas[$numRow], $this->_decorate($this->_organization->id, $supplier_organization_id, $datas[$numRow]));
                
                /*
                * validazione
                */
                $validationResults = $validator->errors($datas[$numRow]);
                $row_errors = [];
                if(!empty($validationResults)) {
                    $row_errors = $this->_humanErrors($validationResults);
                    $errors[$numRow] = $row_errors;
                }    
            } // end if(empty($id_errors)) 
        } // loop rows

        if(!empty($errors)) {
            $results['esito'] = false;
            $results['code'] = 200;
            $results['errors'] = $errors;
            return $results;             
        }

        /*
         * validazione OK => importo
         */
        foreach($datas as $numRow => $data) {

            if(isset($data['id'])) {
                // update  
                $where = ['id' => $data['id'],
                          'organization_id' => $this->_organization->id,
                          'supplier_organization_id' => $supplier_organization_id];        
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

        return $results;
    }    

    /* 
     * setto tutti i campi dell'articolo impostando se non presente quelli di default
     */
    private function _decorate($organization_id, $supplier_organization_id, $datas) {
        
        $datas['organization_id'] = $organization_id;
        $datas['supplier_organization_id'] = $supplier_organization_id;

        $datas['alert_to_qta'] = 0;
        if(isset($datas['prezzo'])) $datas['prezzo'] = $this->convertImport($datas['prezzo']);
        else $datas['prezzo'] = 0;
        if(!isset($datas['bio'])) $datas['bio'] = 'N';
        else $datas['bio'] = $this->_translateSiNo($datas['bio']);
        if(!isset($datas['pezzi_confezione'])) $datas['pezzi_confezione'] = 1;
        if(!isset($datas['um'])) $datas['um'] = 'PZ';
        if(!isset($datas['um_riferimento'])) $datas['um_riferimento'] = 'PZ';
        if(!isset($datas['qta'])) $datas['qta'] = 1.00;
        if(!isset($datas['qta_massima'])) $datas['qta_massima'] = 0;
        if(!isset($datas['qta_minima'])) $datas['qta_minima'] = 1;
        if(!isset($datas['qta_multipli'])) $datas['qta_multipli'] = 1;
        if(!isset($datas['qta_minima_order'])) $datas['qta_minima_order'] = 0;
        if(!isset($datas['qta_massima_order'])) $datas['qta_massima_order'] = 0;
        if(!isset($datas['stato'])) $datas['stato'] = 'Y';
        if(!isset($datas['flag_presente_articlesorders'])) $datas['flag_presente_articlesorders'] = 'Y'; 
        else $datas['flag_presente_articlesorders'] = $this->_translateSiNo($datas['flag_presente_articlesorders']);
        if(!isset($datas['category_article_id'])) {
            // estraggo la categoria di default
            $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
            $datas['category_article_id'] = $categoriesArticlesTable->getIsSystemId($this->_user, $organization_id); 
        }

        return $datas;
    }

    /*
     * $value = 1 KG
     * return ['1', 'KG']
     */
    private function _explodeQtaUm($value) {

        $results = [];
        $results[] = 'INVALID';
        $results[] = 'INVALID';

        if(empty($value))
            return $results;

        if(strpos($value, ' ')!==false) {
            $results = explode(' ', $value);
        }
        
        return $results;
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