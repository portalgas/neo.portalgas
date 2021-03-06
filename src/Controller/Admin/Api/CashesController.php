<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CashesController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cash');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * aggiornamento produttori per gestione chi e' escluso dal prepagato
     */
    public function cashExcludedUpdate() {

        $organization_id = $this->Authentication->getIdentity()->organization->id;

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isManager'] || $this->Authentication->getIdentity()->organization->paramsConfig['hasCashFilterSupplier']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $results = [];
    
        $conditions = [];
        $supplier_organization_id = $this->request->getData('id');

        $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');

        $where = ['SupplierOrganizationCashExcludeds.supplier_organization_id' => $supplier_organization_id,
                  'SupplierOrganizationCashExcludeds.organization_id' => $organization_id];
        $supplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->find()
                                                ->where($where)
                                                ->first();
		if(empty($supplierOrganizationCashExcluded)) {
			/*
			 * insert
			 */
        	$data = [];
        	$data['organization_id'] = $organization_id;
        	$data['supplier_organization_id'] = $supplier_organization_id;

	        $supplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->newEntity();
	        $supplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->patchEntity($supplierOrganizationCashExcluded, $data);
            if (!$supplierOrganizationCashExcludedsTable->save($supplierOrganizationCashExcluded)) {
                $results['code'] = 500;
		        $results['message'] = '';
		        $results['errors'] = $supplierOrganizationCashExcluded->getErrors();
            }
            else {
		        $results['code'] = 200;
		        $results['message'] = 'OK';
		        $results['errors'] = '';
            }
		}      
		else {
			/*
			 * delete
			 */
        	if (!$supplierOrganizationCashExcludedsTable->delete($supplierOrganizationCashExcluded))  {
                $results['code'] = 500;
		        $results['message'] = '';
		        $results['errors'] = $supplierOrganizationCashExcluded->getErrors();
            }
            else {
		        $results['code'] = 200;
		        $results['message'] = 'OK';
		        $results['errors'] = '';
            }
        }

        return $this->_response($results);  
    } 

    /* 
     * elenco di tutti i movimenti di cassa di uno uses
     * /admin/api/cashes/cash-history-by-user
     */
    public function cashHistoryByUser() {

        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
   
        $results = $this->Cash->getHistoryByUser($user, $user->organization->id, $user->id, [], $debug);
        
        return $this->_response($results);
    }     
}