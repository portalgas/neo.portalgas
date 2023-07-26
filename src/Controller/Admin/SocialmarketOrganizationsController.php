<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * SocialmarketOrganizations Controller
 *
 * @property \App\Model\Table\SocialmarketOrganizationsTable $SocialmarketOrganizations
 *
 * @method \App\Model\Entity\SocialmarketOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SocialmarketOrganizationsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        if(!$this->_user->acl['isRoot']) {
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
            'contain' => ['SupplierOrganizations', 'Organizations'],
        ];
        $socialmarketOrganizations = $this->paginate($this->SocialmarketOrganizations);

        $this->set(compact('socialmarketOrganizations'));
    }

    /**
     * View method
     *
     * @param string|null $id Socialmarket Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $socialmarketOrganization = $this->SocialmarketOrganizations->get($id, [
            'contain' => ['SupplierOrganizations', 'Organizations'],
        ]);

        $this->set('socialmarketOrganization', $socialmarketOrganization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $socialmarketOrganization = $this->SocialmarketOrganizations->newEntity();
        if ($this->request->is('post')) {
            $socialmarketOrganization = $this->SocialmarketOrganizations->patchEntity($socialmarketOrganization, $this->request->getData());
            if ($this->SocialmarketOrganizations->save($socialmarketOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'Socialmarket Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Socialmarket Organization'));
        }
        $supplierOrganizations = $this->SocialmarketOrganizations->SupplierOrganizations->find('list', ['limit' => 200]);
        $organizations = $this->SocialmarketOrganizations->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('socialmarketOrganization', 'supplierOrganizations', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Socialmarket Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $socialmarketOrganization = $this->SocialmarketOrganizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $socialmarketOrganization = $this->SocialmarketOrganizations->patchEntity($socialmarketOrganization, $this->request->getData());
            if ($this->SocialmarketOrganizations->save($socialmarketOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'Socialmarket Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Socialmarket Organization'));
        }
        $supplierOrganizations = $this->SocialmarketOrganizations->SupplierOrganizations->find('list', ['limit' => 200]);
        $organizations = $this->SocialmarketOrganizations->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('socialmarketOrganization', 'supplierOrganizations', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Socialmarket Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $socialmarketOrganization = $this->SocialmarketOrganizations->get($id);
        if ($this->SocialmarketOrganizations->delete($socialmarketOrganization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Socialmarket Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Socialmarket Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
