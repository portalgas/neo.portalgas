<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * BackupOrdersDesOrdersOrganizations Controller
 *
 * @property \App\Model\Table\BackupOrdersDesOrdersOrganizationsTable $BackupOrdersDesOrdersOrganizations
 *
 * @method \App\Model\Entity\BackupOrdersDesOrdersOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BackupOrdersDesOrdersOrganizationsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot']) {
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
            'contain' => ['Des', 'DesOrders', 'Organizations', 'Orders'],
        ];
        $kBackupOrdersDesOrdersOrganizations = $this->paginate($this->BackupOrdersDesOrdersOrganizations);

        $this->set(compact('kBackupOrdersDesOrdersOrganizations'));
    }

    /**
     * View method
     *
     * @param string|null $id K Backup Orders Des Orders Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->get($id, [
            'contain' => ['Des', 'DesOrders', 'Organizations', 'Orders'],
        ]);

        $this->set('kBackupOrdersDesOrdersOrganization', $kBackupOrdersDesOrdersOrganization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->newEntity();
        if ($this->request->is('post')) {
            $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->patchEntity($kBackupOrdersDesOrdersOrganization, $this->request->getData());
            if ($this->BackupOrdersDesOrdersOrganizations->save($kBackupOrdersDesOrdersOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Des Orders Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Des Orders Organization'));
        }
        $des = $this->BackupOrdersDesOrdersOrganizations->Des->find('list', ['limit' => 200]);
        $desOrders = $this->BackupOrdersDesOrdersOrganizations->DesOrders->find('list', ['limit' => 200]);
        $organizations = $this->BackupOrdersDesOrdersOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->BackupOrdersDesOrdersOrganizations->Orders->find('list', ['limit' => 200]);
        $this->set(compact('kBackupOrdersDesOrdersOrganization', 'des', 'desOrders', 'organizations', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Backup Orders Des Orders Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->patchEntity($kBackupOrdersDesOrdersOrganization, $this->request->getData());
            if ($this->BackupOrdersDesOrdersOrganizations->save($kBackupOrdersDesOrdersOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Des Orders Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Des Orders Organization'));
        }
        $des = $this->BackupOrdersDesOrdersOrganizations->Des->find('list', ['limit' => 200]);
        $desOrders = $this->BackupOrdersDesOrdersOrganizations->DesOrders->find('list', ['limit' => 200]);
        $organizations = $this->BackupOrdersDesOrdersOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->BackupOrdersDesOrdersOrganizations->Orders->find('list', ['limit' => 200]);
        $this->set(compact('kBackupOrdersDesOrdersOrganization', 'des', 'desOrders', 'organizations', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Backup Orders Des Orders Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $kBackupOrdersDesOrdersOrganization = $this->BackupOrdersDesOrdersOrganizations->get($id);
        if ($this->BackupOrdersDesOrdersOrganizations->delete($kBackupOrdersDesOrdersOrganization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Backup Orders Des Orders Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Backup Orders Des Orders Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
