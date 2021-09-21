<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SummaryOrderTrasports Controller
 *
 * @property \App\Model\Table\SummaryOrderTrasportsTable $SummaryOrderTrasports
 *
 * @method \App\Model\Entity\SummaryOrderTrasport[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryOrderTrasportsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
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
            'contain' => ['Organizations', 'Users', 'Orders'],
        ];
        $summaryOrderTrasports = $this->paginate($this->SummaryOrderTrasports);

        $this->set(compact('summaryOrderTrasports'));
    }

    /**
     * View method
     *
     * @param string|null $id K Summary Order Trasport id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $summaryOrderTrasport = $this->SummaryOrderTrasports->get($id, [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ]);

        $this->set('summaryOrderTrasport', $summaryOrderTrasport);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $summaryOrderTrasport = $this->SummaryOrderTrasports->newEntity();
        if ($this->request->is('post')) {
            $summaryOrderTrasport = $this->SummaryOrderTrasports->patchEntity($summaryOrderTrasport, $this->request->getData());
            if ($this->SummaryOrderTrasports->save($summaryOrderTrasport)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Trasport'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Trasport'));
        }
        $organizations = $this->SummaryOrderTrasports->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderTrasports->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderTrasports->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderTrasport', 'organizations', 'users', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Summary Order Trasport id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $summaryOrderTrasport = $this->SummaryOrderTrasports->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $summaryOrderTrasport = $this->SummaryOrderTrasports->patchEntity($summaryOrderTrasport, $this->request->getData());
            if ($this->SummaryOrderTrasports->save($summaryOrderTrasport)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Trasport'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Trasport'));
        }
        $organizations = $this->SummaryOrderTrasports->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderTrasports->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderTrasports->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderTrasport', 'organizations', 'users', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Summary Order Trasport id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $summaryOrderTrasport = $this->SummaryOrderTrasports->get($id);
        if ($this->SummaryOrderTrasports->delete($summaryOrderTrasport)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Summary Order Trasport'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Summary Order Trasport'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
