<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Mappings Controller
 *
 * @property \App\Model\Table\MappingsTable $Mappings
 *
 * @method \App\Model\Entity\Mapping[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MappingsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
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
        $where = [];
        $request = $this->request->getData();
        // debug($request);
        $search_queue_id = '';
        $search_master_scope_id = '';
        $search_master_table_id = '';
        $search_mapping_type_id = '';
        $search_slave_scope_id = '';
        $search_slave_table_id = '';
        if(!empty($request['search_queue_id'])) {
            $search_queue_id = $request['search_queue_id'];
            array_push($where, ['Mappings.queue_id' => $search_queue_id]);
        }
        if(!empty($request['search_master_scope_id'])) {
            $search_master_scope_id = $request['search_master_scope_id'];
            array_push($where, ['Mappings.master_scope_id' => $search_master_scope_id]);
        }
        if(!empty($request['search_master_table_id'])) {
            $search_master_table_id = $request['search_master_table_id'];
            array_push($where, ['Mappings.master_table_id' => $search_master_table_id]);
        }
        if(!empty($request['search_mapping_type_id'])) {
            $search_mapping_type_id = $request['search_mapping_type_id'];
            array_push($where, ['Mappings.mapping_type_id' => $search_mapping_type_id]);
        }
        if(!empty($request['search_slave_scope_id'])) {
            $search_slave_scope_id = $request['search_slave_scope_id'];
            array_push($where, ['Mappings.slave_scope_id' => $search_slave_scope_id]);
        }
        if(!empty($request['search_slave_table_id'])) {
            $search_slave_table_id = $request['search_slave_table_id'];
            array_push($where, ['Mappings.slave_table_id' => $search_slave_table_id]);
        }
        if(!empty($where)) {
            if(isset($this->request->getQuery('page')))
                $this->request->getQuery('page') = 0;
        }
        $this->set(compact('search_queue_id', 'search_master_scope_id', 'search_master_table_id', 'search_mapping_type_id', 'search_slave_scope_id', 'search_slave_table_id'));

        // debug($where);
        
        $this->paginate = [
            'conditions' => $where,
            'order' => ['Mappings.queue_id' => 'asc', 'Mappings.master_scope_id' => 'asc', 'Mappings.master_table_id' => 'asc', 'Mappings.sort' => 'asc'],
            'contain' => ['Queues', 'QueueTables' => ['Tables'], 'MappingTypes', 'MasterScopes', 'SlaveScopes', 'MasterTables', 'SlaveTables'],
            'limit' => 100
        ];
        $mappings = $this->paginate($this->Mappings);
        // debug($mappings);

        $this->set(compact('mappings'));

        /*
         * search form
         */
        $queues = $this->Mappings->Queues->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);

        $master_scopes = $this->Mappings->MasterScopes->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);
        $slave_scopes = $this->Mappings->SlaveScopes->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);

        $master_tables = $this->Mappings->MasterTables->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);
        $slave_tables = $this->Mappings->SlaveTables->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);
        $mapping_types = $this->Mappings->MappingTypes->find('list', ['conditions' => ['is_active' => 1], 'limit' => 200]);

        $this->set(compact('queues', 'master_scopes', 'slave_scopes', 'master_tables', 'slave_tables', 'mapping_types'));

        $btns_queues = $this->Mappings->Queues->find('all', ['conditions' => ['is_active' => true], 'limit' => 200]);

        $this->set(compact('btns_queues'));
    }

    /**
     * View method
     *
     * @param string|null $id Mapping id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mapping = $this->Mappings->get($id, [
            'contain' => ['Queues', 'MasterScopes', 'MasterTables', 'SlaveScopes', 'SlaveTables', 'MappingTypes', 'MappingValueTypesTable', 'QueueTables']
        ]);

        $this->set('mapping', $mapping);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($queue_id)
    {   
         $reAdd = $this->request->getQuery('reAdd');
        if(empty($reAdd)) 
            $reAdd = $this->request->getData('reAdd');
        if(empty($reAdd)) 
            $reAdd = 'N';

        $mapping = $this->Mappings->newEntity();
        if ($this->request->is('post')) {
            $mapping = $this->Mappings->patchEntity($mapping, $this->request->getData());
            if ($this->Mappings->save($mapping)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping'));

                if($reAdd=='Y') 
                    $url = ['action' => 'add', $mapping->queue_id, '?' => ['reAdd' => $reAdd]];
                else
                    $url = ['action' => 'index'];
                
                return $this->redirect($url);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping'));
        } // end post

        $queue = $this->Mappings->Queues->get($queue_id, ['contain' => 'QueueMappingTypes']);

        $master_scopes = $this->Mappings->MasterScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $master_tables = $this->Mappings->MasterTables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slaveScopes = $this->Mappings->SlaveScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slaveTables = $this->Mappings->SlaveTables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $mapping_types = $this->Mappings->MappingTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        $mapping_value_types = $this->Mappings->MappingValueTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        
        /*
         * workaround list
         */
        $tmp_queue_tables = $this->Mappings->QueueTables->find('all', [
             // 'keyField' => 'QueueTables.id', 'valueField' => 'Tables.name',
            // 'fields' => ['keyField' => 'QueueTables.id', 'valueField' => 'Tables.name'], 
             'order' => ['sort' => 'asc'], 'limit' => 200])->contain(['Tables']);
        $queue_tables = [];  
        foreach($tmp_queue_tables as $queue_table) {
            $queue_tables[$queue_table->id] = $queue_table->table->name;
        }

        $sort = $this->getSort('Mappings');

        $reAdds = ['Y' => __('ReAddY'), 'N' => __('ReAddN')];
        $this->set(compact('reAdds', 'reAdd'));        
    
        $this->set(compact('mapping', 'queue', 'master_scopes', 'master_tables', 'slaveScopes', 'slaveTables', 'mapping_types', 'mapping_value_types', 'queue_tables', 'sort'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Mapping id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mapping = $this->Mappings->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $request = $this->request->getData();
            $mapping = $this->Mappings->patchEntity($mapping, $request);
            if ($this->Mappings->save($mapping)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping'));
        }
        $queue_id = $mapping->queue_id;
        $queue = $this->Mappings->Queues->get($queue_id, ['contain' => 'QueueMappingTypes']);

        $master_scopes = $this->Mappings->MasterScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $master_tables = $this->Mappings->MasterTables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slaveScopes = $this->Mappings->SlaveScopes->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $slaveTables = $this->Mappings->SlaveTables->find('list', ['conditions' => ['is_active' => true], 'limit' => 200]);
        $mapping_types = $this->Mappings->MappingTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        $mapping_value_types = $this->Mappings->MappingValueTypes->find('list', ['conditions' => ['is_active' => true], 'order' => ['sort' => 'asc'], 'limit' => 200]);
        
        /*
         * workaround list
         */
        $tmp_queue_tables = $this->Mappings->QueueTables->find('all', [
             // 'keyField' => 'QueueTables.id', 'valueField' => 'Tables.name',
            // 'fields' => ['keyField' => 'QueueTables.id', 'valueField' => 'Tables.name'], 
             'order' => ['sort' => 'asc'], 'limit' => 200])->contain(['Tables']);
        $queue_tables = [];  
        foreach($tmp_queue_tables as $queue_table) {
            $queue_tables[$queue_table->id] = $queue_table->table->name;
        }

        $this->set(compact('mapping', 'queue', 'master_scopes', 'master_tables', 'slaveScopes', 'slaveTables', 'mapping_types', 'mapping_value_types', 'queue_tables'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Mapping id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mapping = $this->Mappings->get($id);
        if ($this->Mappings->delete($mapping)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Mapping'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Mapping'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
