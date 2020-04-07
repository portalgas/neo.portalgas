<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * ScopeMappingTypes Controller
 *
 * @property \App\Model\Table\ScopeMappingTypesTable $ScopeMappingTypes
 *
 * @method \App\Model\Entity\ScopeMappingType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ScopeMappingTypesController extends AppController
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
        $scopeMappingTypes = $this->paginate($this->ScopeMappingTypes);

        $this->set(compact('scopeMappingTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Scope Mapping Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $scopeMappingType = $this->ScopeMappingTypes->get($id, [
            'contain' => ['Scopes']
        ]);

        $this->set('scopeMappingType', $scopeMappingType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $scopeMappingType = $this->ScopeMappingTypes->newEntity();
        if ($this->request->is('post')) {
            $scopeMappingType = $this->ScopeMappingTypes->patchEntity($scopeMappingType, $this->request->getData());
            if ($this->ScopeMappingTypes->save($scopeMappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Scope Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Scope Mapping Type'));
        }
        $this->set(compact('scopeMappingType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Scope Mapping Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $scopeMappingType = $this->ScopeMappingTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $scopeMappingType = $this->ScopeMappingTypes->patchEntity($scopeMappingType, $this->request->getData());
            if ($this->ScopeMappingTypes->save($scopeMappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Scope Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Scope Mapping Type'));
        }
        $this->set(compact('scopeMappingType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Scope Mapping Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $scopeMappingType = $this->ScopeMappingTypes->get($id);
        if ($this->ScopeMappingTypes->delete($scopeMappingType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Scope Mapping Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Scope Mapping Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
