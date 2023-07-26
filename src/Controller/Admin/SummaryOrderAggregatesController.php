<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SummaryOrderAggregates Controller
 *
 * @property \App\Model\Table\SummaryOrderAggregatesTable $SummaryOrderAggregates
 *
 * @method \App\Model\Entity\SummaryOrderAggregate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryOrderAggregatesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
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
        
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ];
        $summaryOrderAggregates = $this->paginate($this->SummaryOrderAggregates);

        $this->set(compact('summaryOrderAggregates'));
    }

    /**
     * View method
     *
     * @param string|null $id K Summary Order Aggregate id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $summaryOrderAggregate = $this->SummaryOrderAggregates->get($id, [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ]);

        $this->set('summaryOrderAggregate', $summaryOrderAggregate);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $summaryOrderAggregate = $this->SummaryOrderAggregates->newEntity();
        if ($this->request->is('post')) {
            $summaryOrderAggregate = $this->SummaryOrderAggregates->patchEntity($summaryOrderAggregate, $this->request->getData());
            if ($this->SummaryOrderAggregates->save($summaryOrderAggregate)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Aggregate'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Aggregate'));
        }
        $organizations = $this->SummaryOrderAggregates->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderAggregates->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderAggregates->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderAggregate', 'organizations', 'users', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Summary Order Aggregate id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $summaryOrderAggregate = $this->SummaryOrderAggregates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $summaryOrderAggregate = $this->SummaryOrderAggregates->patchEntity($summaryOrderAggregate, $this->request->getData());
            if ($this->SummaryOrderAggregates->save($summaryOrderAggregate)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Aggregate'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Aggregate'));
        }
        $organizations = $this->SummaryOrderAggregates->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderAggregates->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderAggregates->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderAggregate', 'organizations', 'users', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Summary Order Aggregate id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $summaryOrderAggregate = $this->SummaryOrderAggregates->get($id);
        if ($this->SummaryOrderAggregates->delete($summaryOrderAggregate)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Summary Order Aggregate'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Summary Order Aggregate'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
