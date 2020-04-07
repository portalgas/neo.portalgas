<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
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
        $orders = $this->paginate($this->Orders);

        $this->set(compact('orders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Organizations', 'SupplierOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries', 'ProdGasPromotions', 'DesOrders'],
        ]);

        $this->set('order', $order);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Order'));
        }
        $organizations = $this->Orders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->Orders->SupplierOrganizations->find('list', ['limit' => 200]);
        $ownerOrganizations = $this->Orders->OwnerOrganizations->find('list', ['limit' => 200]);
        $ownerSupplierOrganizations = $this->Orders->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        $deliveries = $this->Orders->Deliveries->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->Orders->ProdGasPromotions->find('list', ['limit' => 200]);
        $desOrders = $this->Orders->DesOrders->find('list', ['limit' => 200]);
        $this->set(compact('order', 'organizations', 'supplierOrganizations', 'ownerOrganizations', 'ownerSupplierOrganizations', 'deliveries', 'prodGasPromotions', 'desOrders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Order'));
        }
        $organizations = $this->Orders->Organizations->find('list', ['limit' => 200]);
        $supplierOrganizations = $this->Orders->SupplierOrganizations->find('list', ['limit' => 200]);
        $ownerOrganizations = $this->Orders->OwnerOrganizations->find('list', ['limit' => 200]);
        $ownerSupplierOrganizations = $this->Orders->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        $deliveries = $this->Orders->Deliveries->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->Orders->ProdGasPromotions->find('list', ['limit' => 200]);
        $desOrders = $this->Orders->DesOrders->find('list', ['limit' => 200]);
        $this->set(compact('order', 'organizations', 'supplierOrganizations', 'ownerOrganizations', 'ownerSupplierOrganizations', 'deliveries', 'prodGasPromotions', 'desOrders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
