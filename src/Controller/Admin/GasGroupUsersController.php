<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * GasGroupUsers Controller
 *
 * @property \App\Model\Table\GasGroupUsersTable $GasGroupUsers
 *
 * @method \App\Model\Entity\GasGroupUser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupUsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
  
        if((!isset($this->_organization->paramsConfig['hasGasGroups']) || 
            $this->_organization->paramsConfig['hasGasGroups']=='N') &&  
            !$this->_user->acl['isGasGroupsManagerGroups'] &&
            !$this->_user->acl['isManager']) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function management($gas_group_id)
    {
        $gasGroupUser = $this->GasGroupUsers->newEntity();
        if ($this->request->is('post')) {
            
            $continua = true;
            $datas = $this->request->getData();

            if(empty($datas['user_ids'])) {
                $msg = "Devi selezionare almeno un utente da associare al gruppo";
                $this->Flash->error($msg);
                $continua = false;
            }

            if($continua) {
                /* 
                * cancello tutti gli utenti associati al gruppo 
                */
                $where = ['GasGroupUsers.gas_group_id' => $gas_group_id, 
                        'GasGroupUsers.organization_id' => $this->_organization->id, 
                ];    
                $this->GasGroupUsers->deleteAll($where);

                if(strpos($datas['user_ids'], ',')!==false)
                    $this->_user_ids = explode(',', $datas['user_ids']);
                else 
                    $this->_user_ids = [$datas['user_ids']];
             
                foreach($this->_user_ids as $this->_user_id) {

                    $datas['organization_id'] = $this->_organization->id;
                    $datas['gas_group_id'] = $gas_group_id;
                    $datas['user_id'] = $this->_user_id;

                    $gasGroupUser = $this->GasGroupUsers->newEntity();
                    $gasGroupUser = $this->GasGroupUsers->patchEntity($gasGroupUser, $datas);
                    // dd($gasGroupUser);
                    if (!$this->GasGroupUsers->save($gasGroupUser)) {
                        $this->Flash->error($gasGroupUser->getErrors());
                        $continua = false;
                        break;
                    }
                } // end foreach
            }            

            if($continua) {
                $this->Flash->success("Gasisti associati a ".__('Gas Group'));    
                return $this->redirect(['controller' => 'GasGroups', 'action' => 'index']);
            }
        } // end post 

        $gasGroup = $this->GasGroupUsers->GasGroups->get($gas_group_id, ['contain' => ['Users']]);
        $users = $this->GasGroupUsers->getUsersToAssocitateList($this->_user, $this->_organization->id, $gas_group_id);
        $gasGroupUsers = $this->GasGroupUsers->getUsersAssocitateList($this->_user, $this->_organization->id, $gas_group_id);;
        $this->set(compact('gasGroup', 'gasGroupUser', 'users', 'gasGroupUsers', 'gas_group_id'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        exit;
        $this->paginate = [
            'contain' => ['Organizations', 'Users', 'GasGroups'],
        ];
        $gasGroupUsers = $this->paginate($this->GasGroupUsers);

        $this->set(compact('gasGroupUsers'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        exit;
        $gasGroupUser = $this->GasGroupUsers->get($id, [
            'contain' => ['Organizations', 'Users', 'GasGroups'],
        ]);

        $this->set('gasGroupUser', $gasGroupUser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        exit;
        $gasGroupUser = $this->GasGroupUsers->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // debug($datas);
            $gasGroupUser = $this->GasGroupUsers->patchEntity($gasGroupUser, $datas);
            if (!$this->GasGroupUsers->save($gasGroupUser)) {
                $this->Flash->error($gasGroupUser->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group User')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $organizations = $this->GasGroupUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->GasGroupUsers->Users->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupUsers->GasGroups->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupUser', 'organizations', 'users', 'gasGroups'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        exit;
        $gasGroupUser = $this->GasGroupUsers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            // debug($datas);        
            $gasGroupUser = $this->GasGroupUsers->patchEntity($gasGroupUser, $datas);
            if (!$this->GasGroupUsers->save($gasGroupUser)) {
                $this->Flash->error($gasGroupUser->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group User')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $organizations = $this->GasGroupUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->GasGroupUsers->Users->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupUsers->GasGroups->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupUser', 'organizations', 'users', 'gasGroups'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroupUser = $this->GasGroupUsers->get($id);
        if (!$this->GasGroupUsers->delete($gasGroupUser)) {
            $this->Flash->error($gasGroupUser->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
