<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Queues Controller
 *
 * @property \App\Model\Table\QueuesTable $Queues
 *
 * @method \App\Model\Entity\Queue[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QueuesController extends AppController
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
            'contain' => ['QueueMappingTypes', 'MasterScopes', 'SlaveScopes']
        ];
        $queues = $this->paginate($this->Queues);

        $this->set(compact('queues'));
    }

    /**
     * View method
     *
     * @param string|null $id Queue id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $queue = $this->Queues->get($id, [
            'contain' => ['QueueMappingTypes', 'MasterScopes', 'SlaveScopes', 'Mappings', 'QueueLogs', 'QueueTables']
        ]);

        $this->set('queue', $queue);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $queue = $this->Queues->newEntity();
        if ($this->request->is('post')) {
            $queue = $this->Queues->patchEntity($queue, $this->request->getData());
            if ($this->Queues->save($queue)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue'));
        }
        $queueMappingTypes = $this->Queues->QueueMappingTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        $master_scopes = $this->Queues->MasterScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slave_scopes = $this->Queues->SlaveScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);

        $this->set(compact('queue', 'queueMappingTypes', 'master_scopes', 'slave_scopes'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Queue id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $queue = $this->Queues->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $queue = $this->Queues->patchEntity($queue, $this->request->getData());
            if ($this->Queues->save($queue)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue'));
        }
        $queueMappingTypes = $this->Queues->QueueMappingTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        $master_scopes = $this->Queues->MasterScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slave_scopes = $this->Queues->SlaveScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);

        $this->set(compact('queue', 'queueMappingTypes', 'master_scopes', 'slave_scopes'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Queue id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $queue = $this->Queues->get($id);
        if ($this->Queues->delete($queue)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Queue'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Queue'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
