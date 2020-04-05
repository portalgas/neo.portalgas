<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * OrganizationsPays Controller
 *
 * @property \App\Model\Table\OrganizationsPaysTable $OrganizationsPays
 *
 * @method \App\Model\Entity\OrganizationsPay[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsPaysController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Auth->isRoot($this->user)) {
            die("not root");
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
            'contain' => ['Organizations'],
        ];
        $organizationsPays = $this->paginate($this->OrganizationsPays);

        $this->set(compact('organizationsPays'));
    }

    /**
     * View method
     *
     * @param string|null $id K Organizations Pay id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $organizationsPay = $this->OrganizationsPays->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('organizationsPay', $organizationsPay);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $organizationsPay = $this->OrganizationsPays->newEntity();
        if ($this->request->is('post')) {
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $this->request->getData());
            if ($this->OrganizationsPays->save($organizationsPay)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organizations Pay'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organizations Pay'));
        }
        $organizations = $this->OrganizationsPays->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('organizationsPay', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Organizations Pay id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $organizationsPay = $this->OrganizationsPays->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $this->request->getData());
            if ($this->OrganizationsPays->save($organizationsPay)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organizations Pay'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organizations Pay'));
        }
        $organizations = $this->OrganizationsPays->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('organizationsPay', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Organizations Pay id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $organizationsPay = $this->OrganizationsPays->get($id);
        if ($this->OrganizationsPays->delete($organizationsPay)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Organizations Pay'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Organizations Pay'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
