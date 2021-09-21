<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * BackupOrdersOrders Controller
 *
 * @property \App\Model\Table\BackupOrdersOrdersTable $BackupOrdersOrders
 *
 * @method \App\Model\Entity\BackupOrdersOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BackupOrdersOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
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
            'contain' => ['Organizations', 'SupplierOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries', 'ProdGasPromotions', 'DesOrders'],
        ];
        $kBackupOrdersOrders = $this->paginate($this->BackupOrdersOrders);

        $this->set(compact('kBackupOrdersOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Backup Orders Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $kBackupOrdersOrder = $this->BackupOrdersOrders->get($id, [
            'contain' => ['Organizations', 'SupplierOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries', 'ProdGasPromotions', 'DesOrders'],
        ]);

        $this->set('kBackupOrdersOrder', $kBackupOrdersOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $kBackupOrdersOrder = $this->BackupOrdersOrders->newEntity();
        if ($this->request->is('post')) {
            $kBackupOrdersOrder = $this->BackupOrdersOrders->patchEntity($kBackupOrdersOrder, $this->request->getData());
            if ($this->BackupOrdersOrders->save($kBackupOrdersOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Order'));
        }
        $organizations = $this->BackupOrdersOrders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->BackupOrdersOrders->SupplierOrganizations->find('list', ['limit' => 200]);
        $ownerOrganizations = $this->BackupOrdersOrders->OwnerOrganizations->find('list', ['limit' => 200]);
        $ownerSupplierOrganizations = $this->BackupOrdersOrders->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        $deliveries = $this->BackupOrdersOrders->Deliveries->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->BackupOrdersOrders->ProdGasPromotions->find('list', ['limit' => 200]);
        $desOrders = $this->BackupOrdersOrders->DesOrders->find('list', ['limit' => 200]);
        $this->set(compact('kBackupOrdersOrder', 'organizations', 'supplierOrganizations', 'ownerOrganizations', 'ownerSupplierOrganizations', 'deliveries', 'prodGasPromotions', 'desOrders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Backup Orders Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $kBackupOrdersOrder = $this->BackupOrdersOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $kBackupOrdersOrder = $this->BackupOrdersOrders->patchEntity($kBackupOrdersOrder, $this->request->getData());
            if ($this->BackupOrdersOrders->save($kBackupOrdersOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Order'));
        }
        $organizations = $this->BackupOrdersOrders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->BackupOrdersOrders->SupplierOrganizations->find('list', ['limit' => 200]);
        $ownerOrganizations = $this->BackupOrdersOrders->OwnerOrganizations->find('list', ['limit' => 200]);
        $ownerSupplierOrganizations = $this->BackupOrdersOrders->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        $deliveries = $this->BackupOrdersOrders->Deliveries->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->BackupOrdersOrders->ProdGasPromotions->find('list', ['limit' => 200]);
        $desOrders = $this->BackupOrdersOrders->DesOrders->find('list', ['limit' => 200]);
        $this->set(compact('kBackupOrdersOrder', 'organizations', 'supplierOrganizations', 'ownerOrganizations', 'ownerSupplierOrganizations', 'deliveries', 'prodGasPromotions', 'desOrders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Backup Orders Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $kBackupOrdersOrder = $this->BackupOrdersOrders->get($id);
        if ($this->BackupOrdersOrders->delete($kBackupOrdersOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Backup Orders Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Backup Orders Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
