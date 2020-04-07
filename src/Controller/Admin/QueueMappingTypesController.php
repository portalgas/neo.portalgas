<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * QueueMappingTypes Controller
 *
 * @property \App\Model\Table\QueueMappingTypesTable $QueueMappingTypes
 *
 * @method \App\Model\Entity\QueueMappingType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QueueMappingTypesController extends AppController
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
        $queueMappingTypes = $this->paginate($this->QueueMappingTypes);

        $this->set(compact('queueMappingTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Queue Mapping Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $queueMappingType = $this->QueueMappingTypes->get($id, [
            'contain' => ['Queues']
        ]);

        $this->set('queueMappingType', $queueMappingType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $queueMappingType = $this->QueueMappingTypes->newEntity();
        if ($this->request->is('post')) {
            $queueMappingType = $this->QueueMappingTypes->patchEntity($queueMappingType, $this->request->getData());
            if ($this->QueueMappingTypes->save($queueMappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Mapping Type'));
        }
        $this->set(compact('queueMappingType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Queue Mapping Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $queueMappingType = $this->QueueMappingTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $queueMappingType = $this->QueueMappingTypes->patchEntity($queueMappingType, $this->request->getData());
            if ($this->QueueMappingTypes->save($queueMappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Queue Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Queue Mapping Type'));
        }
        $this->set(compact('queueMappingType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Queue Mapping Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $queueMappingType = $this->QueueMappingTypes->get($id);
        if ($this->QueueMappingTypes->delete($queueMappingType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Queue Mapping Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Queue Mapping Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
