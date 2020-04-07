<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SummaryOrders Controller
 *
 * @property \App\Model\Table\SummaryOrdersTable $SummaryOrders
 *
 * @method \App\Model\Entity\SummaryOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryOrdersController extends AppController
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
            'contain' => ['Organizations', 'Users', 'Deliveries', 'Orders'],
        ];
        $summaryOrders = $this->paginate($this->SummaryOrders);

        $this->set(compact('summaryOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Summary Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $summaryOrder = $this->SummaryOrders->get($id, [
            'contain' => ['Organizations', 'Users', 'Deliveries', 'Orders'],
        ]);

        $this->set('summaryOrder', $summaryOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $summaryOrder = $this->SummaryOrders->newEntity();
        if ($this->request->is('post')) {
            $summaryOrder = $this->SummaryOrders->patchEntity($summaryOrder, $this->request->getData());
            if ($this->SummaryOrders->save($summaryOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order'));
        }
        $organizations = $this->SummaryOrders->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrders->Users->find('list', ['limit' => 200]);
        $deliveries = $this->SummaryOrders->Deliveries->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrder', 'organizations', 'users', 'deliveries', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Summary Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $summaryOrder = $this->SummaryOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $summaryOrder = $this->SummaryOrders->patchEntity($summaryOrder, $this->request->getData());
            if ($this->SummaryOrders->save($summaryOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order'));
        }
        $organizations = $this->SummaryOrders->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrders->Users->find('list', ['limit' => 200]);
        $deliveries = $this->SummaryOrders->Deliveries->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrder', 'organizations', 'users', 'deliveries', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Summary Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $summaryOrder = $this->SummaryOrders->get($id);
        if ($this->SummaryOrders->delete($summaryOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Summary Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Summary Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
