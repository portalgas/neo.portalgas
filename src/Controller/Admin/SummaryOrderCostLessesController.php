<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SummaryOrderCostLesses Controller
 *
 * @property \App\Model\Table\SummaryOrderCostLessesTable $SummaryOrderCostLesses
 *
 * @method \App\Model\Entity\SummaryOrderCostLess[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryOrderCostLessesController extends AppController
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
        $summaryOrderCostLesses = $this->paginate($this->SummaryOrderCostLesses);

        $this->set(compact('summaryOrderCostLesses'));
    }

    /**
     * View method
     *
     * @param string|null $id K Summary Order Cost Less id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $summaryOrderCostLess = $this->SummaryOrderCostLesses->get($id, [
            'contain' => ['Organizations', 'Users', 'Orders'],
        ]);

        $this->set('summaryOrderCostLess', $summaryOrderCostLess);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $summaryOrderCostLess = $this->SummaryOrderCostLesses->newEntity();
        if ($this->request->is('post')) {
            $summaryOrderCostLess = $this->SummaryOrderCostLesses->patchEntity($summaryOrderCostLess, $this->request->getData());
            if ($this->SummaryOrderCostLesses->save($summaryOrderCostLess)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Cost Less'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Cost Less'));
        }
        $organizations = $this->SummaryOrderCostLesses->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderCostLesses->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderCostLesses->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderCostLess', 'organizations', 'users', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Summary Order Cost Less id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $summaryOrderCostLess = $this->SummaryOrderCostLesses->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $summaryOrderCostLess = $this->SummaryOrderCostLesses->patchEntity($summaryOrderCostLess, $this->request->getData());
            if ($this->SummaryOrderCostLesses->save($summaryOrderCostLess)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Summary Order Cost Less'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Summary Order Cost Less'));
        }
        $organizations = $this->SummaryOrderCostLesses->Organizations->find('list', ['limit' => 200]);
        $users = $this->SummaryOrderCostLesses->Users->find('list', ['limit' => 200]);
        $orders = $this->SummaryOrderCostLesses->Orders->find('list', ['limit' => 200]);
        $this->set(compact('summaryOrderCostLess', 'organizations', 'users', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Summary Order Cost Less id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $summaryOrderCostLess = $this->SummaryOrderCostLesses->get($id);
        if ($this->SummaryOrderCostLesses->delete($summaryOrderCostLess)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Summary Order Cost Less'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Summary Order Cost Less'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
