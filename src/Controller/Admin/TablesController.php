<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Tables Controller
 *
 * @property \App\Model\Table\TablesTable $Tables
 *
 * @method \App\Model\Entity\Table[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TablesController extends AppController
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
            'contain' => ['Scopes']
        ];
        $tables = $this->paginate($this->Tables);

        $this->set(compact('tables'));
    }

    /**
     * View method
     *
     * @param string|null $id Table id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $table = $this->Tables->get($id, [
            'contain' => ['Scopes', 'QueueTables']
        ]);

        $this->set('table', $table);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $table = $this->Tables->newEntity();
        if ($this->request->is('post')) {
            $table = $this->Tables->patchEntity($table, $this->request->getData());
            if ($this->Tables->save($table)) {
                $this->Flash->success(__('The {0} has been saved.', 'Table'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Table'));
        }
        $scopes = $this->Tables->Scopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $this->set(compact('table', 'scopes'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Table id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $table = $this->Tables->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $table = $this->Tables->patchEntity($table, $this->request->getData());
            if ($this->Tables->save($table)) {
                $this->Flash->success(__('The {0} has been saved.', 'Table'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Table'));
        }
        $scopes = $this->Tables->Scopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $this->set(compact('table', 'scopes'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Table id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $table = $this->Tables->get($id);
        if ($this->Tables->delete($table)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Table'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Table'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
