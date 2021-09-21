<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Organizations Controller
 *
 * @property \App\Model\Table\OrganizationsTable $Organizations
 *
 * @method \App\Model\Entity\KOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsController extends AppController
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
        $this->Organizations->addBehavior('OrganizationsParams');
        $this->paginate = [
            'contain' => ['Templates', 'JPageCategories', 'Gcalendars'],
        ];
        $organizations = $this->paginate($this->Organizations);

        $this->set(compact('organizations'));
    }

    /**
     * View method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Organizations->addBehavior('OrganizationsParams');
        $organization = $this->Organizations->get($id, [
            'contain' => ['Templates', 'JPageCategories', 'Gcalendars'],
        ]);

        $this->set('organization', $organization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $organization = $this->Organizations->newEntity();
        if ($this->request->is('post')) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organization'));
        }
        $templates = $this->Organizations->Templates->find('list', ['limit' => 200]);
        $jPageCategories = $this->Organizations->JPageCategories->find('list', ['limit' => 200]);
        $gcalendars = $this->Organizations->Gcalendars->find('list', ['limit' => 200]);
        $this->set(compact('organization', 'templates', 'jPageCategories', 'gcalendars'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->Organizations->addBehavior('OrganizationsParams');
        $organization = $this->Organizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organization'));
        }
        $templates = $this->Organizations->Templates->find('list', ['limit' => 200]);
        $jPageCategories = $this->Organizations->JPageCategories->find('list', ['limit' => 200]);
        $gcalendars = $this->Organizations->Gcalendars->find('list', ['limit' => 200]);
        $this->set(compact('organization', 'templates', 'jPageCategories', 'gcalendars'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $organization = $this->Organizations->get($id);
        if ($this->Organizations->delete($organization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
