<?php
namespace App\Controller\Admin;

use App\Controller\Admin\ArticlesImportSuperController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Filesystem\File;
use League\Csv\Reader;
use League\Csv\Statement;

class ArticlesImportOfficinanaturaController extends ArticlesImportSuperController
{
    /*
     * produttore Officina Natura
     */
	private $_default_organization_id = 152;
	private $_default_supplier_organization_id = 3178; // Supplier.id 187
    private $_offset = 0; // parte dalla riga 1 a leggere il file
	private $_limit = 10000;
	private $_header = 0; // a quale riga c'e' l'intestazione

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Upload', [
            'allowed_types' => 'csv',           
            'max_size' => 0,   
            'overwrite' => true,
            'encrypt_name' => false,
            'remove_spaces' => true]);     
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);       
    }

    public function index()
    {
        $debug = true;
        $continua = true;

        if ($this->request->is('post')) {

            $datas = $this->request->getData();
            debug($this->request->getData());
 
            $file = $this->Upload->uploadToTmp(); 
            if(!$file) {
                debug($this->Upload->errors());
                $continua = false;
            }

            /*
             * lettura csv 
             */
            if($continua) {
                $csv = Reader::createFromPath($datas['file']['tmp_name'], 'r');
                $csv->setDelimiter(';');
                debug("File contiene ".count($csv)." righe");
    
                $stmt = (new Statement())->offset($this->_offset)->limit($this->_limit);

                $i = 0;
                $datas = [];
                $rows = $stmt->process($csv);
                foreach ($rows as $numRow => $row) {

                    /*
                     * escludo le intestazioni
                     */
                    if($row[0]!='' && strpos($row[0], 'www.officinadellacqua.com')===false) {
                        
                        $datas[$i]['codice'] = $this->_sanitizeCodice($this->_sanitizeString($row[0]));
                        if($datas[$i]['codice']=='5580---') {
                            echo iconv("UTF-8", "ASCII//IGNORE", $row[1]);
                            dd(iconv("UTF-8", "ASCII//IGNORE", $row[1])); 
                        }

                        $name = $this->_sanitizeString($row[1]);
                        if(!empty($name))
                            $datas[$i]['name'] = $name;
                        $datas[$i]['prezzo'] = $this->_sanitizeImporto($row[3]);
                        $confs = $this->_sanitizeConf($row[2]);
                        $datas[$i]['um'] = $confs['um'];
                        $datas[$i]['um_riferimento'] = $confs['um_riferimento'];
                        $datas[$i]['qta'] = $confs['qta'];

                        
                        if($datas[$i]['codice']=='5580---') {
                            debug($numRow.') '.$datas[$i]['codice'].' - '.$datas[$i]['name'].' - '.$datas[$i]['conf'].' - '.$datas[$i]['prezzo']);
                            dd($row);
                        }
                        
                        debug($numRow.') '.$datas[$i]['codice'].' - '.$datas[$i]['name'].' - '.$datas[$i]['qta'].' - '.$datas[$i]['prezzo']);

                        $i++;
                    }
                } // loop csv

                debug("Estratte ".$i." righe");

                if(empty($datas))
                    $continua = false;

            } // end if($continua) 

            /*
             * inserimento database
             */
            if($continua) {
                            
                $articlesTable = TableRegistry::get('Articles');
                $articlesTable->addBehavior('Articles'); // id = getMaxIdOrganizationId

                $where = ['organization_id' => $this->_default_organization_id,
                          'supplier_organization_id' => $this->_default_supplier_organization_id];

               /*
                * setto flag_presente_articlesorders a N
                */
                $articlesTable->updateAll(['flag_presente_articlesorders' => 'N'], $where);

                /* 
                 * ciclo per database 
                 */
                foreach($datas as $data) {
                    
                    $action = '';

                    /* 
                    * ctrl se esiste gia' 
                    */                    
                    $where2 = [];
                    $where2 = array_merge($where, ['codice' => $data['codice']]);
                    $article = $articlesTable->find()
                                            ->where($where2)
                                            ->first();       
                    if(empty($article)) {
                        $action = 'INSERT';
                        
                        $article = $articlesTable->newEntity();
                        $article->isNew(true);
                        
                        // $data['id'] = $articlesTable->getMaxIdOrganizationId($this->_user, $this->_organization->id);
                        $data['category_article_id'] = 0;
                        $data['pezzi_confezione'] = 1;
                        $data['qta_multipli'] = 1;
                        $data['qta_minima'] = 1;
                        $data['qta_massima'] = 0;
                        $data['qta_massima_order'] = 0;
                        $data['alert_to_qta'] = 0;
                        $data['bio'] = 'N';
                        $data['stato'] = 'Y';
                    }
                    else {
                        $article->isNew(false);
                        $action = 'UPDATE';                        
                    } 

                    $data['flag_presente_articlesorders'] = 'Y';
                    $data['organization_id'] = $this->_default_organization_id;
                    $data['supplier_organization_id'] = $this->_default_supplier_organization_id;

                   /*
                    * workaround
                    */
                    $article->organization_id = $this->_default_organization_id;
                    $article = $articlesTable->patchEntity($article, $data);
                    // debug($article);
                    if (!$articlesTable->save($article)) {
                        debug($article);
                        dd($article->getErrors());
                    }

                    // if($article->isNew())
                    if($action=='INSERT')
                        debug('INSERT '.$article->codice.' '.$article->name);
                    else
                        debug('UPDATE '.$article->codice.' '.$article->name);
                                            
                } // end foreach($datas as $data) 
            } // end if($continua) 

        } // end post  
    } 

    private function _sanitizeCodice($value) {
        $value = str_replace(',', '.', $value);
        return $value;
    }

    private function _sanitizeConf($value) {
        $value = trim($value);

        $datas['qta'] = '';
        $datas['um'] = '';
        $datas['um_riferimento'] = '';

        if(empty($value)) {
            $datas['qta'] = 1;
            $datas['um'] = 'PZ';
            $datas['um_riferimento'] = 'PZ';
            return $datas;
        }

        $value = strtoupper($value);
        if(strpos($value, 'INVECE')!==false || 
            strpos($value, 'VOLUME')!==false ||  
            strpos($value, 'SCONTO')!==false) {
            $datas['qta'] = 1;
            $datas['um'] = 'PZ';
            $datas['um_riferimento'] = 'PZ';
            return $datas;
        }

        /*
         * 1 ml 
         */
        if(strpos($value, ' ')!==false) {
            list($datas['qta'], $datas['um']) = explode(' ', $value);
        }
        else {
            $datas['qta'] = $value;
            /* 
             * 200ml 
             */
            if(strpos($datas['qta'], 'ml')!==false || strpos($datas['qta'], 'ML')!==false) {
                $datas['qta'] = substr($datas['qta'], 0, strlen($datas['qta'])-2);
                $datas['um'] = 'ML';
            }
            else 
            if(strpos($datas['qta'], 'gr')!==false || strpos($datas['qta'], 'GR')!==false) {
                $datas['qta'] = substr($datas['qta'], 0, strlen($datas['qta'])-2);
                $datas['um'] = 'GR';
            }
            else 
            if(strpos($datas['qta'], 'kg')!==false || strpos($datas['qta'], 'KG')!==false) {
                $datas['qta'] = substr($datas['qta'], 0, strlen($datas['qta'])-2);
                $datas['um'] = 'KG';
            }
            else 
            if(strpos($datas['qta'], 'pz')!==false || strpos($datas['qta'], 'PZ')!==false) {
                $datas['qta'] = substr($datas['qta'], 0, strlen($datas['qta'])-2);
                $datas['um'] = 'PZ';
            }
            else 
            if(strpos($datas['qta'], ',')!==false) { // 10,11
                $datas['qta'] = 1;
                $datas['um'] = 'PZ';
            }
            else   
                $datas['um'] = 'PZ';
        }

        $um = strtoupper($datas['um']);
        switch($um) {
            case 'LITRO':
            case 'LITRI':
                $um = 'LT';
            break;
            default: 
                $um = 'PZ';
            break;
        }
        
        $datas['qta'] = str_replace(',', '.', $datas['qta']);
        $datas['um'] = $um;
        $datas['um_riferimento'] = $um;
        
        return $datas;
    }   
}