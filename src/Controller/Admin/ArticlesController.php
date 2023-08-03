<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Traits;
use Cake\Log\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;

class ArticlesController extends AppController
{
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('SuppliersOrganization');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);
        
        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isSuperReferente'] && !$this->_user->acl['isReferentGeneric']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
  
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function indexQuick()
    {
        $where = [];

        /* 
         * in api/ArticlesController
         * 
         * filters
        $request = $this->request->getQuery();
        $search_name = '';
        $search_code = '';
        $search_supplier_organization_id = '';
          
        if(!empty($request['search_name'])) {
            $search_name = $request['search_name'];
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if(!empty($request['search_code'])) {
            $search_code = $request['search_code'];
            $where += ['Articles.code' => '%'.$search_code.'%'];
        } 
        if(!empty($request['search_supplier_organization_id'])) {
            $search_supplier_organization_id = $request['search_supplier_organization_id'];
            $where += ['Articles.supplier_organization_id' => $search_supplier_organization_id];
        }                 
        $this->set(compact('search_code', 'search_name', 'search_supplier_organization_id'));
     
        $articles = $this->Articles->find()
                    ->contain(['SuppliersOrganizations', 'CategoriesArticles'])
                    ->where($where)
                    ->order(['Articles.name'])
                    ->limit(100)
                    ->all();

        $article = new ApiArticleDecorator($this->_user, $articles);
        $articles = $article->results;
        $this->set(compact('articles'));
        */

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $this->set(compact('suppliersOrganizations'));

        (count($suppliersOrganizations)==1) ? $search_supplier_organization_id = key($suppliersOrganizations): $search_supplier_organization_id = '';
        $this->set(compact('search_supplier_organization_id'));

        /*
         * elenco categorie del GAS
         */ 
        $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
        $categories_articles = $categoriesArticlesTable->find('treeList', [
                            'spacer' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                            'conditions' => ['Organization_id' => $this->_organization->id]]);
        $js_categories_articles = json_encode($categories_articles->toArray());
        $this->set(compact('js_categories_articles'));

        $si_no = ['Y' => 'Si', 'N' => 'No'];
        $this->set(compact('si_no'));

        /*
         * ordinamento, di default 'Articles.name ASC'
         * definito in article.js
         */
        $search_orders = [];
        $search_orders['Articles.codice ASC'] = 'Codice (A-Z)';
        $search_orders['Articles.codice DESC'] = 'Codice (Z-A)';
        $search_orders['Articles.name ASC'] = 'Nome (A-Z)';
        $search_orders['Articles.name DESC'] = 'Nome (Z-A)';
        $search_orders['CategoriesArticles.name ASC'] = 'Categoria (A-Z)';
        $search_orders['CategoriesArticles.name DESC'] = 'Categoria (Z-A)';
        $search_orders['Articles.prezzo ASC'] = 'Prezzo (1-9)';
        $search_orders['Articles.prezzo DESC'] = 'Prezzo (9-1)';
        $this->set(compact('search_orders'));

        // $this->set('ums', $this->Articles->enum('um'));
    }

    public function export()
    {  
        $debug = false;

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $this->set(compact('suppliersOrganizations'));  
     
        /* 
         * campi opzionali
         */
        $source_fields = [
            'codice' => ['label' => __('Code'), 'nota' => '001'],
            'nota' => ['label' => __('Note'), 'nota' => "descrizione dell'articolo"],
            'ingredienti' => ['label' => 'Ingredienti', 'nota' => "solo ingredienti naturali"],
            'um_riferimento' => ['label' => __('um_riferimento'), 'nota' => 'Kg'],
            'qta_minima' => ['label' => __('qta_minima'), 'nota' => '1'],
            'qta_massima' => ['label' => __('qta_massima'), 'nota' => '10'],
            'qta_minima_order' => ['label' => __('qta_minima_order'), 'nota' => '0'],
            'qta_massima_order' => ['label' => __('qta_massima_order'), 'nota' => '0'],
            'qta_multipli' => ['label' => __('qta_multipli'), 'nota' => '1']
        ];

        /* 
         * campi esportati
         */
        $export_fields = [
            'name' => ['label' => __('Name'), 'nota' => 'Toma valle di Lanzo'],
            'prezzo' => ['label' => __('Price'), 'nota' => '12,50'],
            'qta' => ['label' => __('qta'), 'nota' => '500'],
            'um' => ['label' => __('UM'), 'nota' => 'Gr'],
            'pezzi_confezione' => ['label' => __('pezzi_confezione'), 'nota' => '1'],
            'bio' => ['label' => __('Bio'), 'nota' => 'Si'],
            'flag_presente_articlesorders' => ['label' => __('flag_presente_articlesorders'), 'nota' => 'Si']
        ];

        /* 
         * campi di default
         */                
        $default_fields = ['id' => ['label' => 'Identificativo articolo', 'nota' => 'Necessario se si vuole aggiornare l\'articolo']];

        $this->set(compact('source_fields', 'export_fields', 'default_fields'));    

        if ($this->request->is('post')) {

            $datas = $this->request->getData();
            if($debug) debug($datas);
            // Log::debug($datas);
            $supplier_organization_id = $datas['supplier_organization_id'];
            $request_export_fields = $datas['export_fields'];
            if(empty($supplier_organization_id) || empty($request_export_fields)) {
                $this->Flash->error(__('Parameters required'));
                return $this->redirect(['action' => 'export']);           
            } 
        
            /*
             * dati produttore
             */
            $supplier_organization = $suppliersOrganizationsTable->get($this->_user, ['SuppliersOrganizations.id' => $supplier_organization_id]);
            if($debug) debug($supplier_organization);
            // Log::debug($supplier_organization);

            /* 
             * campi da estrarre
             */
            $request_default_fields = [];
            foreach($default_fields as $key => $default_field) {
                $request_default_fields[] = $key;
            }
            
            $arr_export_fields = [];
            $arr_export_fields = $request_default_fields;
            if(strpos($request_export_fields, ';')===false)
                $arr_export_fields[] = $request_export_fields;
            else
                $arr_export_fields = array_merge($arr_export_fields, explode(';', $request_export_fields));
            if($debug) debug($arr_export_fields);
            
            $alphabet = range('A', 'Z');
            // if($debug) debug($alphabet);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            /* 
             * estraggo gli articoli in base al produttore (own chi gestisce il listino)
             * */
            $articles = $this->Articles->getsToArticleSupplierOrganization($this->_user, $this->_organization->id, $supplier_organization_id);
            
            if($articles->count()==0) {
                $this->Flash->error("Il produttore non ha articoli associati!");
                return $this->redirect(['action' => 'export']);  
            } 

            /* 
             * header
             */
            foreach($arr_export_fields as $numResult => $arr_export_field) {
                $numCol = $alphabet[$numResult].'1';
                $sheet->setCellValue($numCol, __($arr_export_field));
            }

            foreach($articles as $numResult => $article) {

                $numRow = ($numResult + 2);

                foreach($arr_export_fields as $numResult2 => $arr_export_field) {
                    $numCol = $alphabet[$numResult2].$numRow;
                    $value = $article->{$arr_export_field};
                    switch($value) {
                        case 'Y':
                            $value = 'Si';
                        break;
                        case 'N':
                            $value = 'No';
                        break;
                    }
                    if($debug) debug($numCol.' '.$arr_export_field.' '.$value);
                    $sheet->setCellValue($numCol, $value);
                } // foreach($arr_export_fields as $numResult2 => $arr_export_field) 
            } // foreach($articles as $numResult => $article)

            $writer = new Xlsx($spreadsheet);
            $stream = new CallbackStream(function () use ($writer) {
                $writer->save('php://output');
            });

            $filename = $this->setFileName('Articoli di '.$supplier_organization->name); // .'.xlsx';
            if($debug) debug($filename);
            $response = $this->response; 
            return $response->withType('xlsx')
                ->withHeader('Content-Disposition', "attachment;filename=\"{$filename}.xlsx\"")
                ->withBody($stream);
        } // post
    }
}
