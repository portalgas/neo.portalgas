<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * QueueLogs Controller
 *
 * @property \App\Model\Table\QueueLogsTable $QueueLogs
 *
 * @method \App\Model\Entity\QueueLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QueueLogsController extends AppController
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
        $where = [];
        $request = $this->request->getData();
        // debug($request);
        $search_queue_id = '';
        if(!empty($request['search_queue_id'])) {
            $search_queue_id = $request['search_queue_id'];
            array_push($where, ['QueueLogs.queue_id' => $search_queue_id]);
        }
        $search_uuid = '';
        if(!empty($request['search_uuid'])) {
            $search_uuid = $request['search_uuid'];
            array_push($where, ['QueueLogs.uuid' => $search_uuid]);
        }
        if(!empty($where)) {
            if(isset($this->request->query['page']))
                $this->request->query['page'] = 0;
        }
        $this->set(compact('search_queue_id'));

        $this->paginate = [
            'conditions' => $where,
            'order' => ['QueueLogs.uuid' => 'asc', 'QueueLogs.created' => 'asc'],
            'contain' => ['Queues' => ['MasterScopes', 'SlaveScopes']],
            'limit' => 100
        ];
        $queueLogs = $this->paginate($this->QueueLogs);

        $this->set(compact('queueLogs'));

        /*
         * search form
         */
        $queues = $this->QueueLogs->Queues->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);

        $uuids = [];
        $uuidResults = $this->QueueLogs->find('all')
                                ->select(['QueueLogs.uuid', 'QueueLogs.uuid'])
                                ->group('uuid')
                                // ->order(['QueueLogs.created' => 'asc'])
                                ->toList();
        if(!empty($uuidResults)) {
            foreach ($uuidResults as $uuid) {
                $uuids[$uuid->uuid] = $uuid->uuid;
            }
        }

        $this->set(compact('queues', 'uuids'));

    }

    /**
     * View method
     *
     * @param string|null $id Queue Log id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $queueLog = $this->QueueLogs->get($id, [
            'contain' => ['Queues']
        ]);

        $this->set('queueLog', $queueLog);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $queueLog = $this->QueueLogs->newEntity();
        if ($this->request->is('post')) {
            $queueLog = $this->QueueLogs->patchEntity($queueLog, $this->request->getData());
            if ($this->QueueLogs->save($queueLog)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Log'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Log'));
        }
        $queues = $this->QueueLogs->Queues->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $this->set(compact('queueLog', 'queues'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Queue Log id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $queueLog = $this->QueueLogs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $queueLog = $this->QueueLogs->patchEntity($queueLog, $this->request->getData());
            if ($this->QueueLogs->save($queueLog)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Log'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Log'));
        }
        $queues = $this->QueueLogs->Queues->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        
        $this->set(compact('queueLog', 'queues'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Queue Log id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $queueLog = $this->QueueLogs->get($id);
        if ($this->QueueLogs->delete($queueLog)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Queue Log'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Queue Log'));
        }

        return $this->redirect(['action' => 'index']);
    }    

    public function truncate()
    {
        $this->tableTruncate($this->QueueLogs);

        $this->Flash->success(__('Truncate {0} table.', 'Queue Log'));

        return $this->redirect(['action' => 'index']);
    }
}
