<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * SuppliersVotes Controller
 *
 * @property \App\Model\Table\SuppliersVotesTable $SuppliersVotes
 *
 * @method \App\Model\Entity\KSuppliersVote[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SuppliersVotesController extends AppController
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
            'contain' => ['Suppliers', 'Organizations', 'Users'],
        ];
        $suppliersVotes = $this->paginate($this->SuppliersVotes);

        $this->set(compact('suppliersVotes'));
    }

    /**
     * View method
     *
     * @param string|null $id Suppliers Vote id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $suppliersVote = $this->SuppliersVotes->get($id, [
            'contain' => ['Suppliers', 'Organizations', 'Users'],
        ]);

        $this->set('suppliersVote', $suppliersVote);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $suppliersVote = $this->SuppliersVotes->newEntity();
        if ($this->request->is('post')) {
            $suppliersVote = $this->SuppliersVotes->patchEntity($suppliersVote, $this->request->getData());
            if ($this->SuppliersVotes->save($suppliersVote)) {
                $this->Flash->success(__('The {0} has been saved.', 'Suppliers Vote'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Suppliers Vote'));
        }
        $suppliers = $this->SuppliersVotes->Suppliers->find('list', ['limit' => 200]);
        $organizations = $this->SuppliersVotes->Organizations->find('list', ['limit' => 200]);
        $users = $this->SuppliersVotes->Users->find('list', ['limit' => 200]);
        $this->set(compact('suppliersVote', 'suppliers', 'organizations', 'users'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Suppliers Vote id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $suppliersVote = $this->SuppliersVotes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $suppliersVote = $this->SuppliersVotes->patchEntity($suppliersVote, $this->request->getData());
            if ($this->SuppliersVotes->save($suppliersVote)) {
                $this->Flash->success(__('The {0} has been saved.', 'Suppliers Vote'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Suppliers Vote'));
        }
        $suppliers = $this->SuppliersVotes->Suppliers->find('list', ['limit' => 200]);
        $organizations = $this->SuppliersVotes->Organizations->find('list', ['limit' => 200]);
        $users = $this->SuppliersVotes->Users->find('list', ['limit' => 200]);
        $this->set(compact('suppliersVote', 'suppliers', 'organizations', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Suppliers Vote id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $suppliersVote = $this->SuppliersVotes->get($id);
        if ($this->SuppliersVotes->delete($suppliersVote)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Suppliers Vote'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Suppliers Vote'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
