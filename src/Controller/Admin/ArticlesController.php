<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Traits;

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
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $this->set(compact('suppliersOrganizations'));

        (count($suppliersOrganizations)==1) ? $search_supplier_organization_id = key($suppliersOrganizations): $search_supplier_organization_id = '';
        $this->set(compact('search_supplier_organization_id'));        
    }
}
