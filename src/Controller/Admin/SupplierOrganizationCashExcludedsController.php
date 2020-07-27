<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SupplierOrganizationCashExcludeds Controller
 *
 * @property \App\Model\Table\SupplierOrganizationCashExcludedsTable $SupplierOrganizationCashExcludeds
 *
 * @method \App\Model\Entity\SupplierOrganizationCashExcluded[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SupplierOrganizationCashExcludedsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Authentication->getIdentity()->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SuppliersOrganizations'],
        ];
        $supplierOrganizationCashExcludeds = $this->paginate($this->SupplierOrganizationCashExcludeds);

        $this->set(compact('kSupplierOrganizationCashExcludeds'));
    }

    /**
     * View method
     *
     * @param string|null $id K Supplier Organization Cash Excluded id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->get($id, [
            'contain' => ['Organizations', 'SuppliersOrganizations'],
        ]);

        $this->set('kSupplierOrganizationCashExcluded', $supplierOrganizationCashExcluded);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->newEntity();
        if ($this->request->is('post')) {
            $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->patchEntity($supplierOrganizationCashExcluded, $this->request->getData());
            if ($this->SupplierOrganizationCashExcludeds->save($supplierOrganizationCashExcluded)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Supplier Organization Cash Excluded'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Supplier Organization Cash Excluded'));
        }
        $organizations = $this->SupplierOrganizationCashExcludeds->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->SupplierOrganizationCashExcludeds->SuppliersOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('kSupplierOrganizationCashExcluded', 'organizations', 'supplierOrganizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Supplier Organization Cash Excluded id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->patchEntity($supplierOrganizationCashExcluded, $this->request->getData());
            if ($this->SupplierOrganizationCashExcludeds->save($supplierOrganizationCashExcluded)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Supplier Organization Cash Excluded'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Supplier Organization Cash Excluded'));
        }
        $organizations = $this->SupplierOrganizationCashExcludeds->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->SupplierOrganizationCashExcludeds->SuppliersOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('kSupplierOrganizationCashExcluded', 'organizations', 'supplierOrganizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Supplier Organization Cash Excluded id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $supplierOrganizationCashExcluded = $this->SupplierOrganizationCashExcludeds->get($id);
        if ($this->SupplierOrganizationCashExcludeds->delete($supplierOrganizationCashExcluded)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Supplier Organization Cash Excluded'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Supplier Organization Cash Excluded'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
