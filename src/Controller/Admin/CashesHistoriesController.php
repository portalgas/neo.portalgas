<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * CashesHistories Controller
 *
 * @property \App\Model\Table\CashesHistoriesTable $CashesHistories
 *
 * @method \App\Model\Entity\CashesHistory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CashesHistoriesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot']) {
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
            'contain' => ['Organizations', 'Cashes', 'Users'],
        ];
        $cashesHistories = $this->paginate($this->CashesHistories);

        $this->set(compact('cashesHistories'));
    }

    /**
     * View method
     *
     * @param string|null $id K Cashes History id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cashesHistory = $this->CashesHistories->get($id, [
            'contain' => ['Organizations', 'Cashes', 'Users'],
        ]);

        $this->set('cashesHistory', $cashesHistory);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cashesHistory = $this->CashesHistories->newEntity();
        if ($this->request->is('post')) {
            $cashesHistory = $this->CashesHistories->patchEntity($cashesHistory, $this->request->getData());
            if ($this->CashesHistories->save($cashesHistory)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cashes History'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cashes History'));
        }
        $organizations = $this->CashesHistories->Organizations->find('list', ['limit' => 200]);
        $cashes = $this->CashesHistories->Cashes->find('list', ['limit' => 200]);
        $users = $this->CashesHistories->Users->find('list', ['limit' => 200]);
        $this->set(compact('cashesHistory', 'organizations', 'cashes', 'users'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Cashes History id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cashesHistory = $this->CashesHistories->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cashesHistory = $this->CashesHistories->patchEntity($cashesHistory, $this->request->getData());
            if ($this->CashesHistories->save($cashesHistory)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cashes History'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cashes History'));
        }
        $organizations = $this->CashesHistories->Organizations->find('list', ['limit' => 200]);
        $cashes = $this->CashesHistories->Cashes->find('list', ['limit' => 200]);
        $users = $this->CashesHistories->Users->find('list', ['limit' => 200]);
        $this->set(compact('cashesHistory', 'organizations', 'cashes', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Cashes History id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cashesHistory = $this->CashesHistories->get($id);
        if ($this->CashesHistories->delete($cashesHistory)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Cashes History'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Cashes History'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
