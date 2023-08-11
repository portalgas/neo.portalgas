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
        $this->loadComponent('SuppliersOrganization');

        $this->_user = $this->Authentication->getIdentity();
        $this->_organization = $this->_user->organization;
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

        $where = ['Orders.organization_id' => $this->_organization->id,
                  'Orders.id' => $order_id];
        $ordersResults = $ordersTable->find()->where($where)->first();  

        if(!empty($ordersResults)) {
            /*
             * dati SuppliersOrganizations
             */ 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $where = ['SuppliersOrganizations.organization_id' => $this->_organization->id,
                      'SuppliersOrganizations.id' => $ordersResults->supplier_organization_id];

            $suppliersOrganizationResults = $suppliersOrganizationsTable->get($this->_user, $where);
            if(!empty($suppliersOrganizationResults)) {
                $suppliersOrganizationResults->order = $ordersResults;
                $results['results'] = $suppliersOrganizationResults;
            }
        } // if(!empty($ordersResults))

        return $this->_response($results);
    }

    public function getById() {

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        $supplier_organization_id = $this->request->getData('supplier_organization_id');
        if(empty($supplier_organization_id)) {
            $results['code'] = 500;
            $results['message'] = __('Parameters required');
            $results['errors'] = '';
            $continua = false;
            return $this->_response($results);            
        }

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $where = ['SuppliersOrganizations.organization_id' => $this->_organization->id,
                  'SuppliersOrganizations.id' => $supplier_organization_id];

        $suppliersOrganization = $suppliersOrganizationsTable->get($this->_user, $where);
        if(!empty($suppliersOrganization)) {
            $results['results'] = $suppliersOrganization;
        }

        /*
         * eventuali dati DES 
         */ 
        $is_des = false;
        if($this->_organization->paramsConfig['hasDes']) {
            $desSuppliersTable = TableRegistry::get('DesSuppliers');
            $is_des = $desSuppliersTable->getDesACL($this->_user, $suppliersOrganization);    
            // dd($is_des);
        }
        $results['results']['is_des'] = $is_des;

        return $this->_response($results);
    } 

    /*
     * dato un supplier_id, creo l'associazione con il GAS (suppliersOrganizations)
     *
     * TODO, per ora fatto ProdGaSuppliersController::import
     */
    public function import() {

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        $supplier_id = $this->request->getData('supplier_id');
        // debug($supplier_id);

        // $organization_id = this->_user->organization->id; // gas scelto
        // debug($user);

        $this->SuppliersOrganization->import($this->_user, $supplier_id, $debug);

        return $this->_response($results);        
    }
}