<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * DesOrders Controller
 *
 * @property \App\Model\Table\DesOrdersTable $DesOrders
 *
 * @method \App\Model\Entity\DesOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DesOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
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
            'contain' => ['Des', 'DesSuppliers', 'Organizations', 'Orders'],
        ];
        $desOrders = $this->paginate($this->DesOrders);

        $this->set(compact('desOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Des Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $desOrder = $this->DesOrders->get($id, [
            'contain' => ['Des', 'DesSuppliers', 'Organizations', 'Orders'],
        ]);

        $this->set('desOrder', $desOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $desOrder = $this->DesOrders->newEntity();
        if ($this->request->is('post')) {
            $desOrder = $this->DesOrders->patchEntity($desOrder, $this->request->getData());
            if ($this->DesOrders->save($desOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Order'));
        }
        $des = $this->DesOrders->Des->find('list', ['limit' => 200]);
        $desSuppliers = $this->DesOrders->DesSuppliers->find('list', ['limit' => 200]);
        $organizations = $this->DesOrders->Organizations->find('list', ['limit' => 200]);
        $orders = $this->DesOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('desOrder', 'des', 'desSuppliers', 'organizations', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Des Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $desOrder = $this->DesOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $desOrder = $this->DesOrders->patchEntity($desOrder, $this->request->getData());
            if ($this->DesOrders->save($desOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Order'));
        }
        $des = $this->DesOrders->Des->find('list', ['limit' => 200]);
        $desSuppliers = $this->DesOrders->DesSuppliers->find('list', ['limit' => 200]);
        $organizations = $this->DesOrders->Organizations->find('list', ['limit' => 200]);
        $orders = $this->DesOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('desOrder', 'des', 'desSuppliers', 'organizations', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Des Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $desOrder = $this->DesOrders->get($id);
        if ($this->DesOrders->delete($desOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Des Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Des Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
