<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * GasGroups Controller
 *
 * @property \App\Model\Table\GasGroupsTable $GasGroups
 *
 * @method \App\Model\Entity\GasGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupsController extends AppController
{ 
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();
        $organization = $user->organization; // gas scelto
      
        if(!isset($user->acl) ||
            !isset($organization->paramsConfig['hasGasGroups']) || 
            $organization->paramsConfig['hasGasGroups']=='N' || 
             !$user->acl['isGasGropusManagerGroups']
            ) { 
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
            'contain' => ['Organizations'],
        ];
        $gasGroups = $this->paginate($this->GasGroups);

        $this->set(compact('gasGroups'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gasGroup = $this->GasGroups->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('gasGroup', $gasGroup);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gasGroup = $this->GasGroups->newEntity();
        if ($this->request->is('post')) {
            $gasGroup = $this->GasGroups->patchEntity($gasGroup, $this->request->getData());
            if ($this->GasGroups->save($gasGroup)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group'));
        }
        $organizations = $this->GasGroups->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('gasGroup', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gasGroup = $this->GasGroups->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gasGroup = $this->GasGroups->patchEntity($gasGroup, $this->request->getData());
            if ($this->GasGroups->save($gasGroup)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group'));
        }
        $organizations = $this->GasGroups->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('gasGroup', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroup = $this->GasGroups->get($id);
        if ($this->GasGroups->delete($gasGroup)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Gas Group'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Gas Group'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
