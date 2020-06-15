<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * QueueTables Controller
 *
 * @property \App\Model\Table\QueueTablesTable $QueueTables
 *
 * @method \App\Model\Entity\QueueTable[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QueueTablesController extends AppController
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
            'contain' => ['Queues', 'Tables']
        ];
        $queueTables = $this->paginate($this->QueueTables);

        $this->set(compact('queueTables'));
    }

    /**
     * View method
     *
     * @param string|null $id Queue Table id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $queueTable = $this->QueueTables->get($id, [
            'contain' => ['Queues', 'Tables']
        ]);

        $this->set('queueTable', $queueTable);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $queueTable = $this->QueueTables->newEntity();
        if ($this->request->is('post')) {
            $queueTable = $this->QueueTables->patchEntity($queueTable, $this->request->getData());
            if ($this->QueueTables->save($queueTable)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Table'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Table'));
        }
        $queues = $this->QueueTables->Queues->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $tables = $this->QueueTables->Tables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $this->set(compact('queueTable', 'queues', 'tables'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Queue Table id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $queueTable = $this->QueueTables->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $queueTable = $this->QueueTables->patchEntity($queueTable, $this->request->getData());
            if ($this->QueueTables->save($queueTable)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Table'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Table'));
        }
        $queues = $this->QueueTables->Queues->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $tables = $this->QueueTables->Tables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $this->set(compact('queueTable', 'queues', 'tables'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Queue Table id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $queueTable = $this->QueueTables->get($id);
        if ($this->QueueTables->delete($queueTable)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Queue Table'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Queue Table'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
