<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Decorator\ApiArticleDecorator;

class ArticlesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('SuppliersOrganization');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);
        
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

        $si_no = ['Y' => 'Si', 'N' => 'No'];
        $this->set(compact('si_no'));

        // $this->set('ums', $this->Articles->enum('um'));
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $article = $this->Articles->get($id, [
            'contain' => ['Organizations', 'SuppliersOrganizations', 'CategoriesArticles'],
        ]);

        $this->set('article', $article);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('The {0} has been saved.', 'Article'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Article'));
        }
        $organizations = $this->Articles->Organizations->find('list', ['limit' => 200]);
        $suppliersOrganizations = $this->Articles->SuppliersOrganizations->find('list', ['limit' => 200]);
        $categoriesArticles = $this->Articles->CategoriesArticles->find('list', ['limit' => 200]);
        $this->set(compact('article', 'organizations', 'suppliersOrganizations', 'categoriesArticles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $article = $this->Articles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('The {0} has been saved.', 'Article'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Article'));
        }
        $organizations = $this->Articles->Organizations->find('list', ['limit' => 200]);
        $suppliersOrganizations = $this->Articles->SuppliersOrganizations->find('list', ['limit' => 200]);
        $categoriesArticles = $this->Articles->CategoriesArticles->find('list', ['limit' => 200]);
        $this->set(compact('article', 'organizations', 'suppliersOrganizations', 'categoriesArticles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Article'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Article'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
