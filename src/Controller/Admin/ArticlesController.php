<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Traits;
use Cake\Log\Log;
use Cake\Cache\Cache;
use Cake\Http\CallbackStream;

class ArticlesController extends AppController
{
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('ArticlesImportExport');
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
         * se arrivo da import
         * */
        $request = $this->request->getQuery();
        (!empty($request['search_supplier_organization_id'])) ? $search_supplier_organization_id = $request['search_supplier_organization_id']: $search_supplier_organization_id = '';
        $this->set(compact('search_supplier_organization_id'));

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

        if(empty($search_supplier_organization_id)) {
            (count($suppliersOrganizations)==1) ? $search_supplier_organization_id = key($suppliersOrganizations): $search_supplier_organization_id = '';
            $this->set(compact('search_supplier_organization_id'));
        }

        /*
         * elenco categorie del GAS
         * gestito /admin/api/categories-articles/gets
         */
        $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
        $js_categories_articles = $categoriesArticlesTable->jsListGets($this->_user, $this->_organization->id);
        $js_categories_articles = json_encode($js_categories_articles);
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

        $source_fields = $this->ArticlesImportExport->getExportSourceFields($this->_user);
        $export_fields = $this->ArticlesImportExport->getExportFields($this->_user);
        $default_fields = $this->ArticlesImportExport->getExportDefaultFields($this->_user);
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
             * estraggo gli articoli in base al produttore (own chi gestisce il listino)
             * */
            $articles = $this->Articles->getsToArticleSupplierOrganization($this->_user, $this->_organization->id, $supplier_organization_id);
            if($articles->count()==0) {
                $this->Flash->error("Il produttore non ha articoli associati!");
                return $this->redirect(['action' => 'export']);
            }

            $writer = $this->ArticlesImportExport->export($this->_user, $this->request->getData(), $articles);
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

    public function import()
    {
        $debug = false;

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $this->set(compact('suppliersOrganizations'));

        $import_fields = [];
        $import_fields[''] = 'A quale campo corrisponde?';
        $import_fields['IGNORE'] = 'Ignore questa colonna';
        $import_fields += $this->ArticlesImportExport->getImportFields($this->_user);
        $this->set(compact('import_fields'));
    }

    public function importSupplier()
    {
        $debug = false;

        /*
         * SELECT k_suppliers_organizations.id, k_suppliers_organizations.*
         * FROM k_suppliers_organizations, k_organizations
         * WHERE k_suppliers_organizations.organization_id = k_organizations.id
         *  and k_organizations.name like '%Offici%';
         */
        $suppliersOrganizations = [3178 => 'Officina Naturae', 3389 => 'La Saponaria'];
        $this->set(compact('suppliersOrganizations'));

        $import_fields = [];
        $import_fields[''] = 'A quale campo corrisponde?';
        $import_fields['IGNORE'] = 'Ignore questa colonna';
        $import_fields += $this->ArticlesImportExport->getImportSupplierFields($this->_user);
        $this->set(compact('import_fields'));
    }

    /*
     * $article_id=null se add
     * $supplier_organization_id!=null se scelto un produttore
     */
    public function view($article_organization_id, $article_id=null, $supplier_organization_id=null)
    {
        $this->set(compact('article_organization_id', 'article_id', 'supplier_organization_id'));

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $this->set(compact('suppliersOrganizations'));

        $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
        $categoriesArticles = $categoriesArticlesTable->getsList($this->_user, $this->_organization->id);
        $this->set(compact('categoriesArticles'));

        if(Cache::read('articlesTypes')===false) {
            $articlesTypesTable = TableRegistry::get('ArticlesTypes');
            $articlesTypes = $articlesTypesTable->getsList($this->_user, $this->_organization->id);
            Cache::write('articlesTypes',$articlesTypes);
        }
        else
            $articlesTypes = Cache::read('articlesTypes');
        $this->set(compact('articlesTypes'));

        $this->set('ums', $this->Articles->enum('um'));
    }
}
