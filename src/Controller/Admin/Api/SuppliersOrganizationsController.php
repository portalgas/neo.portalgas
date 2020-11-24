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
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
    
    /*
     * chi gestisce il listino articoli
     * owner_articles da Orders (deriva da SuppliersOrganizations)
     */
    public function getByOrderId() {
        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        /*
         * dati Orders
         */ 
        $order_id = $this->request->getData('order_id');
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $this->Authentication->getIdentity()->organization->id,
                  'Orders.id' => $order_id];
        $ordersResults = $ordersTable->find()->where($where)->first();  

        if(!empty($ordersResults)) {
            /*
             * dati SuppliersOrganizations
             */ 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $where = ['SuppliersOrganizations.organization_id' => $this->Authentication->getIdentity()->organization->id,
                      'SuppliersOrganizations.id' => $ordersResults->supplier_organization_id];

            $suppliersOrganizationResults = $suppliersOrganizationsTable->get($this->Authentication->getIdentity(), $where);
            if(!empty($suppliersOrganizationResults)) {
                $suppliersOrganizationResults->order = $ordersResults;
                $results['results'] = $suppliersOrganizationResults;
            }
        } // if(!empty($ordersResults))

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
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

        $where = ['SuppliersOrganizations.organization_id' => $this->Authentication->getIdentity()->organization->id,
                  'SuppliersOrganizations.id' => $supplier_organization_id];

        $suppliersOrganizationResults = $suppliersOrganizationsTable->get($this->Authentication->getIdentity(), $where);
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