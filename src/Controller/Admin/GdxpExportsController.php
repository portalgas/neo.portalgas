<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class GdxpExportsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        $this->viewBuilder()->setClassName('Xml'); 
    }

    public function index($supplier_organization_id) {

        $articlesTable = TableRegistry::get('Articles');
        $articlesTable->addBehavior('GdxpArticles');

        $where = ['Articles.organization_id' => $this->user->organization_id,
            'Articles.supplier_organization_id' => $supplier_organization_id,
                  'Articles.stato' => 'Y'
            ];
        // debug($where);    
        $order = ['Articles.name asc'];

        $articles = $articlesTable->find('all', [
                        'conditions' => $where,
                        'order' => $order,
                        'limit' => 2,
                        'contain' => ['CategoriesArticles']
                        /*
                        'contain' => ['SuppliersOrganizations' => ['Suppliers', 'OwnerOrganizations', 'OwnerSupplierOrganizations']]
                        */
                        ]);
        // debug($articles);

        $this->set(compact('articles'));
        $this->set('_serialize', ['articles']);

        // Set Force Download
       // return $this->response->withDownload('report-' . date('YmdHis') . '.' . $format);
    }    
}