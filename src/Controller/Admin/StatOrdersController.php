<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * StatOrders Controller
 *
 * @property \App\Model\Table\StatOrdersTable $StatOrders
 *
 * @method \App\Model\Entity\StatOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatOrdersController extends AppController
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
            'contain' => ['Organizations', 'SupplierOrganizations', 'StatDeliveries'],
        ];
        $statOrders = $this->paginate($this->StatOrders);

        $this->set(compact('statOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Stat Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $statOrder = $this->StatOrders->get($id, [
            'contain' => ['Organizations', 'SupplierOrganizations', 'StatDeliveries'],
        ]);

        $this->set('statOrder', $statOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $statOrder = $this->StatOrders->newEntity();
        if ($this->request->is('post')) {
            $statOrder = $this->StatOrders->patchEntity($statOrder, $this->request->getData());
            if ($this->StatOrders->save($statOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Order'));
        }
        $organizations = $this->StatOrders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->StatOrders->SupplierOrganizations->find('list', ['limit' => 200]);
        $statDeliveries = $this->StatOrders->StatDeliveries->find('list', ['limit' => 200]);
        $this->set(compact('statOrder', 'organizations', 'supplierOrganizations', 'statDeliveries'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Stat Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $statOrder = $this->StatOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $statOrder = $this->StatOrders->patchEntity($statOrder, $this->request->getData());
            if ($this->StatOrders->save($statOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Order'));
        }
        $organizations = $this->StatOrders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->StatOrders->SupplierOrganizations->find('list', ['limit' => 200]);
        $statDeliveries = $this->StatOrders->StatDeliveries->find('list', ['limit' => 200]);
        $this->set(compact('statOrder', 'organizations', 'supplierOrganizations', 'statDeliveries'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Stat Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $statOrder = $this->StatOrders->get($id);
        if ($this->StatOrders->delete($statOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Stat Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Stat Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
