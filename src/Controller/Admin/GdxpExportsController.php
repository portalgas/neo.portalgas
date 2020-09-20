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
        // $this->viewBuilder()->setOption('serialize', true);
    }

    public function index($supplier_organization_id) {

        $gdxp = $this->_getHeader(); 

        $subject = ['subject' => $this->_getOrganization($this->Authentication->getIdentity()->organization)];

        $blocks =[]; 
        $supplier = $this->_getSupplier($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $supplier_organization_id);
        if(!empty($supplier)) {
            $blocks[0]['supplier'] = $supplier;
            $blocks[0]['supplier']['products'] = $this->_getArticles($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $supplier_organization_id); 

            $suplier_name = $blocks[0]['supplier']['name'];
        } 
        else {
            $suplier_name = 'Produttore non trovato';
        } // end if(!empty($supplier))

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set($subject);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);
        
        /*
         * commentare per visualizzarlo a video
         * Set Force Download https://book.cakephp.org/3/en/views/json-and-xml-views.html#example-usage
         */
        $suplier_name = str_replace(' ', '-', $suplier_name);
        $file_name = Configure::read('Gdxp.file.prefix').$suplier_name.'-'.date('YmdHis').'.json';

        // Prior to 3.4.0
        return $this->response->download($file_name);
        
        // return $this->response->withDownload($file_name);       
    } 

    private function _getHeader() {

        return [
            'protocolVersion' => Configure::read('Gdxp.protocolVersion'),
            'creationDate' => date('Y-m-d'),
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
        $results['contacts'] = [];

        $contacts = [];
        $contacts['type'] = 'emailAddress';
        $contacts['value'] = $organization->mail;
        $results['contacts'][0] = $contacts;

        return $results;
    }   

    /*
     * Suppliers
     */
    private function _getSupplier($user, $organization_id, $supplier_organization_id) {

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');        
        $results = [];
        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
            'SuppliersOrganizations.id' => $supplier_organization_id,
            ];
        // debug($where);    

        $suppliersOrganizationsResults = $suppliersOrganizationsTable->find('all', [
                        'conditions' => $where,
                        'fields' => ['supplier_id']
                        /*
                        'contain' => ['OwnerOrganizations', 'OwnerSupplierOrganizations']
                        */
                        ])
                        ->first();
        // debug($suppliersOrganizationsResults);   
        if(!empty($suppliersOrganizationsResults)) {

            $supplier_id = $suppliersOrganizationsResults->supplier_id; 
            // debug($suppliersOrganizationsResults->supplier_id); 

            $suppliersTable = TableRegistry::get('Suppliers');
            $suppliersTable->addBehavior('GdxpSuppliers');

            $where = ['Suppliers.id' => $supplier_id,
                ];
            // debug($where);    

            $results = $suppliersTable->find('all', ['conditions' => $where])->first();
            // debug($results);
        } // end if(!empty($suppliersOrganizationsResults))            

        return $results;
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