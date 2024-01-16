<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream;

class UserGroupsController extends AppController
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

        if(!$this->_user->acl['isRoot'] && !$this->_user->acl['isManager']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    /*
     * per ora solo per gestire i ruoli del cassiere per gruppo $this->_user->acl['isGasGroupsCassiere']
     */
    public function index($group_id)
    {
        if($group_id!=Configure::read('group_id_gas_groups_id_cassiere')) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));                
        }
       
        if($group_id==Configure::read('group_id_gas_groups_id_cassiere')) {
            if(!isset($this->_organization->paramsConfig['hasGasGroups']) || 
               $this->_organization->paramsConfig['hasGasGroups']=='N') {
                $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
                return $this->redirect(Configure::read('routes_msg_stop'));                
               }
        }
   
        /* 
        * filters
        */
        $request = $this->request->getQuery();
        $search_user_id = '';
        $where = [];

        if(!empty($request['search_user_id'])) {
            $search_user_id = $request['search_user_id'];
            $where += ['Users.id' => $search_user_id];
        } 
        
        $gasGroupsTable = TableRegistry::get('GasGroups');
        $gas_groups = $gasGroupsTable->find()
                                    ->select(['id', 'name'])
                                    ->where(['GasGroups.organization_id' => $this->_organization->id,
                                        'GasGroups.is_active' => true])
                                    ->order(['name'])
                                    ->all();

        $usersTable = TableRegistry::get('Users');
        $results = $usersTable->gets($this->_user, $this->_organization->id, $where);  
        $results = $results->toArray();
        
        $usersTable->removeBehavior('Users');
        $users = $usersTable->getList($this->_user, $this->_organization->id);  
        $this->set(compact('users', 'search_user_id'));

        $userUsergroupMapTable = TableRegistry::get('UserUsergroupMap');        
        foreach($results as $numResult => $user) {
            $where = ['UserUsergroupMap.user_id' => $user['id'],
                      'UserUsergroupMap.group_id' => $group_id];
            $user_gas_groups = $userUsergroupMapTable->find()
                                                    ->where($where)
                                                    ->all();  
            if($user_gas_groups->count()>0) {
                $results[$numResult]['gas_groups'] = [];
                foreach($gas_groups as $numResult2 => $gas_group) {
                    foreach($user_gas_groups as $user_gas_group) {
                        $results[$numResult]['gas_groups'][$numResult2]['id'] = $gas_group->id;
                        $results[$numResult]['gas_groups'][$numResult2]['name'] = $gas_group->name;
                        $results[$numResult]['gas_groups'][$numResult2]['checked'] = false;
                        if($user_gas_group->gas_group_id==$gas_group->id) {
                            $results[$numResult]['gas_groups'][$numResult2]['checked'] = true;
                            break;
                        }
                    }
                }
            }
            else {
                $results[$numResult]['gas_groups'] = [];
                foreach($gas_groups as $numResult2 => $gas_group) {
                    $results[$numResult]['gas_groups'][$numResult2]['id'] = $gas_group->id;
                    $results[$numResult]['gas_groups'][$numResult2]['name'] = $gas_group->name;
                    $results[$numResult]['gas_groups'][$numResult2]['checked'] = false;
                }
            }
        } // foreach($users as $numResult => $user)

        $this->set(compact('results', 'gas_groups', 'group_id'));
    }    
    
    public function add($group_id, $user_id, $gas_group_id)
    {
        /* 
        * filters
        */
        $search_user_id = $this->request->getQuery('search_user_id');
        
        $userUsergroupMapTable = TableRegistry::get('UserUsergroupMap');
        $where = ['UserUsergroupMap.user_id' => $user_id,
                  'UserUsergroupMap.group_id' => $group_id,
                  'UserUsergroupMap.gas_group_id' => $gas_group_id];
        $user_gas_group = $userUsergroupMapTable->find()
                                                ->where($where)
                                                ->first();
        if(empty($user_gas_group)) {
            $datas = [];
            $datas['user_id'] = $user_id;
            $datas['group_id'] = $group_id;
            $datas['gas_group_id'] = $gas_group_id; 
            $userUsergroupMap = $userUsergroupMapTable->newEntity();
            $userUsergroupMap = $userUsergroupMapTable->patchEntity($userUsergroupMap, $datas);
            if (!$userUsergroupMapTable->save($userUsergroupMap)) 
                $this->Flash->error($userUsergroupMap->getErrors());  
            else 
                $this->Flash->success("Gasista aggiunto al ruolo di cassiere del gruppo scelto");
            
        }
        else 
            $this->Flash->error("Il gasista ha già il ruolo di cassiere del gruppo scelto");

        return $this->redirect(['action' => 'index', $group_id, '?' => ['search_user_id' => $search_user_id]]);
    }

    public function delete($group_id, $user_id, $gas_group_id)
    {
        /* 
        * filters
        */
        $search_user_id = $this->request->getQuery('search_user_id');

        $userUsergroupMapTable = TableRegistry::get('UserUsergroupMap');
        $where = ['UserUsergroupMap.user_id' => $user_id,
                    'UserUsergroupMap.group_id' => $group_id,
                    'UserUsergroupMap.gas_group_id' => $gas_group_id];
        $user_gas_group = $userUsergroupMapTable->find()
                                                ->where($where)
                                                ->first();
        if(!empty($user_gas_group)) {
            if (!$userUsergroupMapTable->delete($user_gas_group)) {
                $this->Flash->error($user_gas_group->getErrors());
            }
            else 
                $this->Flash->success("Gasista eliminato dal ruolo di cassiere del gruppo scelto");
        }
        else 
            $this->Flash->error("Gasista non trovato!");

        return $this->redirect(['action' => 'index', $group_id, '?' => ['search_user_id' => $search_user_id]]);
    }    
}