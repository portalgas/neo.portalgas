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
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        $this->viewBuilder()->setClassName('Xml'); 
    }

    public function index()
    {
        $acl_supplier_organizations = $this->Auth->getAclSupplierOrganizationsList($this->user);

        $articlesTable = TableRegistry::get('Articles');

        $supplier_organization_id = 1641;
        $where = ['Articles.supplier_organization_id' => $supplier_organization_id,
                  'Articles.stato' => 'Y'];
        $articles = $articlesTable->gets($this->user, $where);
        // debug($articles);
        
        $this->set(compact('acl_supplier_organizations', 'articles'));
    }
}