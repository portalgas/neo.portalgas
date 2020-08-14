<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * DocumentStates Controller
 *
 * @property \App\Model\Table\DocumentStatesTable $DocumentStates
 *
 * @method \App\Model\Entity\DocumentState[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocumentStatesController extends AppController
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
    
    public function index()
    {
        $documentStates = $this->paginate($this->DocumentStates);

        $this->set(compact('documentStates'));
    }

    /**
     * View method
     *
     * @param string|null $id Document State id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documentState = $this->DocumentStates->get($id, [
            'contain' => ['Documents'],
        ]);

        $this->set('documentState', $documentState);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $documentState = $this->DocumentStates->newEntity();
        if ($this->request->is('post')) {
            $documentState = $this->DocumentStates->patchEntity($documentState, $this->request->getData());
            if ($this->DocumentStates->save($documentState)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document State'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document State'));
        }
        $this->set(compact('documentState'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Document State id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documentState = $this->DocumentStates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $documentState = $this->DocumentStates->patchEntity($documentState, $this->request->getData());
            if ($this->DocumentStates->save($documentState)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document State'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document State'));
        }
        $this->set(compact('documentState'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Document State id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documentState = $this->DocumentStates->get($id);
        if ($this->DocumentStates->delete($documentState)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Document State'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Document State'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
