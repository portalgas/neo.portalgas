<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;

class GdxpsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(!$this->Auth->isSuperReferente($this->user) || !$this->Auth->isReferentGeneric($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);        
    }

    /*
     * list suppliers gdxp
     */
    public function index()
    {
        $gdxp_suppliers_index_url = Configure::read('Gdxp.suppliers.index.url');
        $gdxp_articles_index_url = Configure::read('Gdxp.articles.index.url');

        $this->set(compact('gdxp_suppliers_index_url', 'gdxp_articles_index_url'));
    }

    public function export()
    {
        $articles = [];
        $supplier_organization_id = 0;
        $acl_supplier_organizations = $this->Auth->getAclSupplierOrganizationsList($this->user);

        if ($this->request->is('post')) {

            $supplier_organization_id = $this->request->getData('supplier_organization_id');

            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $supplier_organization = $suppliersOrganizationsTable->get($supplier_organization_id, ['contain' => ['Suppliers']]);
            $this->set(compact('supplier_organization'));

            // debug('supplier_organization_id '.$supplier_organization_id);
            $articlesTable = TableRegistry::get('Articles');

            $where = ['Articles.supplier_organization_id' => $supplier_organization_id,
                      'Articles.stato' => 'Y'];
            $articles = $articlesTable->gets($this->user, $where);
            // debug($articles);
        } // end if ($this->request->is('post')) {

        $this->set(compact('acl_supplier_organizations', 'supplier_organization_id', 'articles'));
    }
}