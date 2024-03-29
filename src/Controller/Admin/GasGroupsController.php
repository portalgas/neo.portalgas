<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;

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
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        /*
         * non filtro per gruppi creati dall'utente, se no altri referenti non potranno vederlo 
        $where = ['GasGroups.user_id' => $this->_user->id, 
                  'GasGroups.organization_id' => $this->_user->organization_id, 
                 ];
        $this->paginate = [
            'contain' => ['GasGroupUsers', 'GasGroupDeliveries'],
            'conditions' => $where,
            'order' => ['GasGroups.name']
        ];
        $gasGroups = $this->paginate($gasGroups);
        */
        if($this->_user->acl['isManager']) {

            $where_gas_groups_user = [];
            $where_gas_groups_user = ['GasGroupUsers.organization_id' => $this->_organization->id];

            /* 
            * filters
            */
            $request = $this->request->getQuery();
            $search_user_id = '';
              
            if(!empty($request['search_user_id'])) {
                $search_user_id = $request['search_user_id'];
                $where_gas_groups_user += ['GasGroupUsers.user_id' => $search_user_id];
            } 
            $this->set(compact('search_user_id'));

            $usersTable = TableRegistry::get('Users');
            $users = $usersTable->getList($this->_user, $this->_organization->id);   
            $this->set(compact('users')); 

            $gasGroups = $this->GasGroups->find()->contain([
                'GasGroupDeliveries', 
                'Users', // chi l'ha creato
                'GasGroupUsers' => [
                    'conditions' => $where_gas_groups_user]])
                ->where(['GasGroups.organization_id' => $this->_organization->id,
                        'GasGroups.is_active' => true])
                ->order(['GasGroups.name'])
                ->all();
                
            if($gasGroups->count()>0)
                $gasGroups = $gasGroups->toArray();

            if(!empty($request['search_user_id'])) {
                // escludo i gruppi che non hanno l'utente filtrato
                foreach($gasGroups as $numResult => $gasGroup) {
                    if(empty($gasGroup->gas_group_users)) 
                        unset($gasGroups[$numResult]);
                }
            }
        }
        else
            $gasGroups = $this->GasGroups->findMy($this->_user, $this->_organization->id, $this->_user->id);
        
        $this->set(compact('gasGroups'));
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
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_user->organization->id;
            $datas['user_id'] = $this->_user->id;
            $datas['is_system'] = false;
            $datas['is_active'] = true;
            // debug($datas);
            $gasGroup = $this->GasGroups->patchEntity($gasGroup, $datas);
            if (!$this->GasGroups->save($gasGroup)) {
                $this->Flash->error($gasGroup->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $this->set(compact('gasGroup'));
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
            $datas = $this->request->getData();
            // debug($datas);        
            $gasGroup = $this->GasGroups->patchEntity($gasGroup, $datas);
            if (!$this->GasGroups->save($gasGroup)) {
                $this->Flash->error($gasGroup->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $organizations = $this->GasGroups->Organizations->find('list', ['limit' => 200]);
        $users = $this->GasGroups->Users->find('list', ['limit' => 200]);
        $this->set(compact('gasGroup', 'organizations', 'users'));
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
        if (!$this->GasGroups->delete($gasGroup)) {
            $this->Flash->error($gasGroup->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
