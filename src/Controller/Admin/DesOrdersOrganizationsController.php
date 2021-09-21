<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * DesOrdersOrganizations Controller
 *
 * @property \App\Model\Table\DesOrdersOrganizationsTable $DesOrdersOrganizations
 *
 * @method \App\Model\Entity\DesOrdersOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DesOrdersOrganizationsController extends AppController
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
	
    public function index()
    {
        $this->paginate = [
            'contain' => ['Des', 'DesOrders', 'Organizations', 'Orders'],
        ];
        $desOrdersOrganizations = $this->paginate($this->DesOrdersOrganizations);

        $this->set(compact('desOrdersOrganizations'));
    }

    /**
     * View method
     *
     * @param string|null $id K Des Orders Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $desOrdersOrganization = $this->DesOrdersOrganizations->get($id, [
            'contain' => ['Des', 'DesOrders', 'Organizations', 'Orders'],
        ]);

        $this->set('desOrdersOrganization', $desOrdersOrganization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $desOrdersOrganization = $this->DesOrdersOrganizations->newEntity();
        if ($this->request->is('post')) {
            $desOrdersOrganization = $this->DesOrdersOrganizations->patchEntity($desOrdersOrganization, $this->request->getData());
            if ($this->DesOrdersOrganizations->save($desOrdersOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Orders Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Orders Organization'));
        }
        $des = $this->DesOrdersOrganizations->Des->find('list', ['limit' => 200]);
        $desOrders = $this->DesOrdersOrganizations->DesOrders->find('list', ['limit' => 200]);
        $organizations = $this->DesOrdersOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->DesOrdersOrganizations->Orders->find('list', ['limit' => 200]);
        $this->set(compact('desOrdersOrganization', 'des', 'desOrders', 'organizations', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Des Orders Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $desOrdersOrganization = $this->DesOrdersOrganizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $desOrdersOrganization = $this->DesOrdersOrganizations->patchEntity($desOrdersOrganization, $this->request->getData());
            if ($this->DesOrdersOrganizations->save($desOrdersOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Orders Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Orders Organization'));
        }
        $des = $this->DesOrdersOrganizations->Des->find('list', ['limit' => 200]);
        $desOrders = $this->DesOrdersOrganizations->DesOrders->find('list', ['limit' => 200]);
        $organizations = $this->DesOrdersOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->DesOrdersOrganizations->Orders->find('list', ['limit' => 200]);
        $this->set(compact('desOrdersOrganization', 'des', 'desOrders', 'organizations', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Des Orders Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $desOrdersOrganization = $this->DesOrdersOrganizations->get($id);
        if ($this->DesOrdersOrganizations->delete($desOrdersOrganization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Des Orders Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Des Orders Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
