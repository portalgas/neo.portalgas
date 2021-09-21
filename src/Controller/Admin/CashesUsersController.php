<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * CashesUsers Controller
 *
 * @property \App\Model\Table\CashesUsersTable $CashesUsers
 *
 * @method \App\Model\Entity\CashesUser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CashesUsersController extends AppController
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
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
            'contain' => ['Organizations', 'Users'],
        ];
        $cashesUsers = $this->paginate($this->CashesUsers);

        $this->set(compact('cashesUsers'));
    }

    /**
     * View method
     *
     * @param string|null $id K Cashes User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cashesUser = $this->CashesUsers->get($id, [
            'contain' => ['Organizations', 'Users'],
        ]);

        $this->set('cashesUser', $cashesUser);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cashesUser = $this->CashesUsers->newEntity();
        if ($this->request->is('post')) {
            $cashesUser = $this->CashesUsers->patchEntity($cashesUser, $this->request->getData());
            if ($this->CashesUsers->save($cashesUser)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cashes User'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cashes User'));
        }
        $organizations = $this->CashesUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->CashesUsers->Users->find('list', ['limit' => 200]);
        $this->set(compact('cashesUser', 'organizations', 'users'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Cashes User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cashesUser = $this->CashesUsers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cashesUser = $this->CashesUsers->patchEntity($cashesUser, $this->request->getData());
            if ($this->CashesUsers->save($cashesUser)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cashes User'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cashes User'));
        }
        $organizations = $this->CashesUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->CashesUsers->Users->find('list', ['limit' => 200]);
        $this->set(compact('cashesUser', 'organizations', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Cashes User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cashesUser = $this->CashesUsers->get($id);
        if ($this->CashesUsers->delete($cashesUser)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Cashes User'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Cashes User'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
