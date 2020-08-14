<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * MappingValueTypes Controller
 *
 * @property \App\Model\Table\MappingValueTypesTable $MappingValueTypes
 *
 * @method \App\Model\Entity\MappingValueType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MappingValueTypesController extends AppController
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
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $mappingValueTypes = $this->paginate($this->MappingValueTypes);

        $this->set(compact('mappingValueTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mappingValueType = $this->MappingValueTypes->get($id, [
            'contain' => ['Mappings'],
        ]);

        $this->set('mappingValueType', $mappingValueType);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mappingValueType = $this->MappingValueTypes->newEntity();
        if ($this->request->is('post')) {
            $mappingValueType = $this->MappingValueTypes->patchEntity($mappingValueType, $this->request->getData());
            if ($this->MappingValueTypes->save($mappingValueType)) {
                $this->Flash->success(__('The mapping value type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The mapping value type could not be saved. Please, try again.'));
        }
        $this->set(compact('mappingValueType'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mappingValueType = $this->MappingValueTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mappingValueType = $this->MappingValueTypes->patchEntity($mappingValueType, $this->request->getData());
            if ($this->MappingValueTypes->save($mappingValueType)) {
                $this->Flash->success(__('The mapping value type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The mapping value type could not be saved. Please, try again.'));
        }
        $this->set(compact('mappingValueType'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mappingValueType = $this->MappingValueTypes->get($id);
        if ($this->MappingValueTypes->delete($mappingValueType)) {
            $this->Flash->success(__('The mapping value type has been deleted.'));
        } else {
            $this->Flash->error(__('The mapping value type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
