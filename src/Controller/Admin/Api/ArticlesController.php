<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleDecorator;
use App\Traits;

class ArticlesController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SuppliersOrganization');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function gets() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $jsonData = $this->request->input('json_decode');
        $where = [];
        $where += ['Articles.organization_id' => $this->_organization->id]; 

        if(isset($jsonData->search_flag_presente_articlesorders)) {
            $search_flag_presente_articlesorders = $jsonData->search_flag_presente_articlesorders;
            ($search_flag_presente_articlesorders) ? $search_flag_presente_articlesorders = 'Y': $search_flag_presente_articlesorders = 'N';
            $where += ['Articles.flag_presente_articlesorders' => $search_flag_presente_articlesorders];
        } 
        if(!empty($jsonData->search_name)) {
            $search_name = $jsonData->search_name;
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if(!empty($jsonData->search_codice)) {
            $search_codice = $jsonData->search_codice;
            $where += ['Articles.codice' => '%'.$search_codice.'%'];
        } 
        if(!empty($jsonData->search_categories_articles)) {
            $search_categories_articles = $jsonData->search_categories_articles;
            $where += ['Articles.category_article_id' => $search_categories_articles];
        }         
        if(!empty($jsonData->search_supplier_organization_id)) {
            $search_supplier_organization_id = $jsonData->search_supplier_organization_id;
            $where += ['Articles.supplier_organization_id' => $search_supplier_organization_id];
        } 
        else {
            // non ho scelto il produttore, filtro per ACL
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
            $where += ['Articles.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];  
        }                

        $articles = $this->Articles->find()
                    ->contain(['SuppliersOrganizations', 'CategoriesArticles'])
                    ->where($where)
                    ->order(['Articles.name'])
                    ->limit(100)
                    ->all();

        $article = new ApiArticleDecorator($this->_user, $articles);
        $results['results'] = $article->results;
        
        return $this->_response($results); 
    }
    
    public function getAutocomplete() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $jsonData = $this->request->input('json_decode');
        $where = [];
        $where += ['Articles.organization_id' => $this->_organization->id]; 

        $field = $jsonData->field; // name / codice

        if($field=='name') {
            $search_name = $jsonData->search_name;
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if($field=='codice') {
            $search_codice = $jsonData->search_codice;
            $where += ['Articles.codice LIKE ' => '%'.$search_codice.'%'];
        }         
        if(!empty($jsonData->search_supplier_organization_id)) {
            $search_supplier_organization_id = $jsonData->search_supplier_organization_id;
            $where += ['Articles.supplier_organization_id' => $search_supplier_organization_id];
        } 
        else {
            // non ho scelto il produttore, filtro per ACL
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
            $where += ['Articles.supplier_organization_id IN ' => array_keys($suppliersOrganizations)];  
        }                

        if($field=='name') 
            $selects = ['Articles.name'];
        else
        if($field=='codice')  
            $selects = ['Articles.codice'];

        $articles = $this->Articles->find()
                    ->select($selects)
                    ->where($where)
                    ->order(['Articles.name'])
                    ->limit(100)
                    ->all();

        $article_results = [];
        if($articles->count()>0) {
            foreach($articles as $article) {
                if($field=='name') 
                    $article_results[] = $article->name;
                else
                if($field=='codice')                 
                    $article_results[] = $article->codice;
            }
        }

        $results['results'] = $article_results;
      
        return $this->_response($results); 
    }    
}