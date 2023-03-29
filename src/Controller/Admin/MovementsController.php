<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * Movements Controller
 *
 * @property \App\Model\Table\MovementsTable $Movements
 *
 * @method \App\Model\Entity\Movement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MovementsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

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
            'contain' => ['MovementTypes', 'Users', 'SuppliersOrganizations'],
        ];
        $movements = $this->paginate($this->Movements);
        $this->set('types', $this->Movements->enum('type'));
        $this->set(compact('movements'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $movement = $this->Movements->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // debug($datas);
            $movement = $this->Movements->patchEntity($movement, $datas);
            if (!$this->Movements->save($movement)) {
                $this->Flash->error($movement->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Movement')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $this->set('types', $this->Movements->enum('type'));
        
        $movementTypes = $this->Movements->MovementTypes->find('list', ['limit' => 200]);
        $users = $this->Movements->Users->find('list', ['limit' => 200]);
        $suppliersOrganizations = $this->Movements->SuppliersOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('movement', 'movementTypes', 'users', 'suppliersOrganizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Movement id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $movement = $this->Movements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            // debug($datas);        
            $movement = $this->Movements->patchEntity($movement, $datas);
            if (!$this->Movements->save($movement)) {
                $this->Flash->error($movement->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Movement')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set('types', $this->Movements->enum('type'));
        $movementTypes = $this->Movements->MovementTypes->find('list', ['limit' => 200]);
        $users = $this->Movements->Users->find('list', ['limit' => 200]);
        $suppliersOrganizations = $this->Movements->SuppliersOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('movement', 'movementTypes', 'users', 'suppliersOrganizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Movement id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $movement = $this->Movements->get($id);
        if (!$this->Movements->delete($movement)) {
            $this->Flash->error($movement->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
