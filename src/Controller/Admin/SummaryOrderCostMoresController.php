<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SummaryOrderCostMores Controller
 *
 * @property \App\Model\Table\SummaryOrderCostMoresTable $SummaryOrderCostMores
 *
 * @method \App\Model\Entity\SummaryOrderCostMore[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryOrderCostMoresController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ];
        $summaryOrderCostMores = $this->paginate($this->SummaryOrderCostMores);

        $this->set(compact('summaryOrderCostMores'));
    }

    /**
     * View method
     *
     * @param string|null $id K Summary Order Cost More id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $summaryOrderCostMore = $this->SummaryOrderCostMores->get($id, [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ]);

        $this->set('summaryOrderCostMore', $summaryOrderCostMore);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $summaryOrderCostMore = $this->SummaryOrderCostMores->newEntity();
        if ($this->request->is('post')) {
            $summaryOrderCostMore = $this->SummaryOrderCostMores->patchEntity($summaryOrderCostMore, $this->request->getData());
            if ($this->SummaryOrderCostMores->save($summaryOrderCostMore)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Cost More'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Cost More'));
        }
        $organizations = $this->SummaryOrderCostMores->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderCostMores->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderCostMores->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderCostMore', 'organizations', 'users', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Summary Order Cost More id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $summaryOrderCostMore = $this->SummaryOrderCostMores->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $summaryOrderCostMore = $this->SummaryOrderCostMores->patchEntity($summaryOrderCostMore, $this->request->getData());
            if ($this->SummaryOrderCostMores->save($summaryOrderCostMore)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Cost More'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Cost More'));
        }
        $organizations = $this->SummaryOrderCostMores->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderCostMores->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderCostMores->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderCostMore', 'organizations', 'users', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Summary Order Cost More id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $summaryOrderCostMore = $this->SummaryOrderCostMores->get($id);
        if ($this->SummaryOrderCostMores->delete($summaryOrderCostMore)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Summary Order Cost More'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Summary Order Cost More'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
