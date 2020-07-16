<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * TemplatesOrdersStates Controller
 *
 * @property \App\Model\Table\TemplatesOrdersStatesTable $templatesOrdersStates
 *
 * @method \App\Model\Entity\TemplatesOrdersState[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TemplatesOrdersStatesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auths->isRoot($this->user)) {
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
            'contain' => ['Templates', 'Groups'],
        ];
        $templatesOrdersStates = $this->paginate($this->TemplatesOrdersStates);

        $this->set(compact('templatesOrdersStates'));
    }

    /**
     * View method
     *
     * @param string|null $id K Templates Orders State id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $templatesOrdersState = $this->TemplatesOrdersStates->get($id, [
            'contain' => ['Templates', 'Groups'],
        ]);

        $this->set('templatesOrdersState', $templatesOrdersState);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $templatesOrdersState = $this->TemplatesOrdersStates->newEntity();
        if ($this->request->is('post')) {
            $templatesOrdersState = $this->TemplatesOrdersStates->patchEntity($templatesOrdersState, $this->request->getData());
            if ($this->TemplatesOrdersStates->save($templatesOrdersState)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Templates Orders State'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Templates Orders State'));
        }
        $templates = $this->TemplatesOrdersStates->Templates->find('list', ['limit' => 200]);
        $groups = $this->TemplatesOrdersStates->Groups->find('list', ['limit' => 200]);
        $this->set(compact('templatesOrdersState', 'templates', 'groups'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Templates Orders State id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $templatesOrdersState = $this->TemplatesOrdersStates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $templatesOrdersState = $this->TemplatesOrdersStates->patchEntity($templatesOrdersState, $this->request->getData());
            if ($this->TemplatesOrdersStates->save($templatesOrdersState)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Templates Orders State'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Templates Orders State'));
        }
        $templates = $this->TemplatesOrdersStates->Templates->find('list', ['limit' => 200]);
        $groups = $this->TemplatesOrdersStates->Groups->find('list', ['limit' => 200]);
        $this->set(compact('templatesOrdersState', 'templates', 'groups'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Templates Orders State id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $templatesOrdersState = $this->TemplatesOrdersStates->get($id);
        if ($this->TemplatesOrdersStates->delete($templatesOrdersState)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Templates Orders State'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Templates Orders State'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
