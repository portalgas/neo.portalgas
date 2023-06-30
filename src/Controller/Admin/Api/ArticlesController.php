<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiArticleDecorator;
use App\Decorator\ApiSupplierDecorator;
use App\Traits;

class ArticlesController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
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
        if(!empty($jsonData->search_name)) {
            $search_name = $jsonData->search_name;
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if(!empty($jsonData->search_codice)) {
            $search_codice = $jsonData->search_codice;
            $where += ['Articles.codice' => '%'.$search_codice.'%'];
        } 
        if(!empty($jsonData->search_supplier_organization_id)) {
            $search_supplier_organization_id = $jsonData->search_supplier_organization_id;
            $where += ['Articles.supplier_organization_id' => $search_supplier_organization_id];
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
}