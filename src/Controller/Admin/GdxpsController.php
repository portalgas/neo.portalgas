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
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);        
    }

    public function index()
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