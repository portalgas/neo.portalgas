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
        
        // $this->viewBuilder()->setClassName('Xml'); 
        $this->viewBuilder()->setClassName('Json'); 
    }

    public function index($supplier_organization_id) {

        $gdxp = $this->_getHeader(); 

        $subject = $this->_getOrganization($this->user->organization);

        $blocks =[]; 
        $blocks[0]['supplier'] = $this->_getSupplier($this->user, $this->user->organization_id, $supplier_organization_id); 
        $blocks[0]['supplier']['products'] = $this->_getArticles($this->user, $this->user->organization_id, $supplier_organization_id); 

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);

        // Set Force Download
        $suplier_name = $blocks[0]['supplier']['name'];
        $suplier_name = str_replace(' ', '-', $suplier_name);
        $file_name = Configure::read('Gdxp.file.prefix').$suplier_name.'-'.date('YmdHis').'.json';

        return $this->response->download($file_name);        
    } 

    private function _getHeader() {

        return [
            'protocolVersion' => Configure::read('Gdxp.protocolVersion'),
            'creationDate' => date('YmdHis'),
            'applicationSignature' => Configure::read('Gdxp.applicationSignature')
        ];
    } 

    private function _getOrganization($organization) {

        $results = [];
        $results['name'] = $organization->name;
        $results['taxCode'] = $organization->cf;
        $results['vatNumber'] = $organization->piva;
        $results['address']['street'] = $organization->indirizzo;
        $results['address']['locality'] = $organization->localita;
        $results['address']['zipCode'] = $organization->cap;
        $results['contacts']['type'] = 'emailAddress';
        $results['contacts']['value'] = $organization->mail;

        return $results;
    }   

    /*
     * Suppliers
     */
    private function _getSupplier($user, $organization_id, $supplier_organization_id) {

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');        
        
        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
            'SuppliersOrganizations.id' => $supplier_organization_id,
            ];
        // debug($where);    

        $results = $suppliersOrganizationsTable->find('all', [
                        'conditions' => $where,
                        'fields' => ['supplier_id']
                        /*
                        'contain' => ['OwnerOrganizations', 'OwnerSupplierOrganizations']
                        */
                        ])
                        ->first();
        $supplier_id = $results->supplier_id; 
        // debug($results->supplier_id); 

        $suppliersTable = TableRegistry::get('Suppliers');
        $suppliersTable->addBehavior('GdxpSuppliers');

        $where = ['Suppliers.id' => $supplier_id,
            ];
        // debug($where);    

        $supplierResults = $suppliersTable->find('all', ['conditions' => $where])->first();
        // debug($supplierResults);

        return $supplierResults;
    }

    /*
     * Articles
     */
    private function _getArticles($user, $organization_id, $supplier_organization_id) { 

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $suppliersOrganizationsTable->Articles->addBehavior('GdxpArticles');

        $where = ['Articles.organization_id' => $organization_id,
                  'Articles.supplier_organization_id' => $supplier_organization_id,
                  'Articles.stato' => 'Y'
            ];
        // debug($where);    

        $articleResults = $suppliersOrganizationsTable->Articles->find('all', [
                        'conditions' => $where,
                        'order' => ['Articles.name asc'],
                        // 'limit' => 2,
                        'contain' => ['CategoriesArticles']
                        ]);
        // debug($articleResults);

        return $articleResults;
    }   
}