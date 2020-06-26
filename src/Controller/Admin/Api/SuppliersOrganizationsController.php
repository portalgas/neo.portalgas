<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class SuppliersOrganizationsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    public function getById() {

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        $supplier_organization_id = $this->request->getData('supplier_organization_id');
        
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $where = ['SuppliersOrganizations.organization_id' => $this->user->organization->id,
                  'SuppliersOrganizations.id' => $supplier_organization_id];

        $suppliersOrganizationResults = $suppliersOrganizationsTable->get($this->user, $where);
        if(!empty($suppliersOrganizationResults)) {
            $results['results'] = $suppliersOrganizationResults;
        }

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}