<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * MovementTypes Controller
 *
 * @property \App\Model\Table\MovementTypesTable $MovementTypes
 *
 * @method \App\Model\Entity\MovementType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MovementTypesController extends AppController
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
        $movementTypes = $this->paginate($this->MovementTypes);
        $this->set('models', $this->MovementTypes->enum('model'));
        $this->set(compact('movementTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Movement Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $movementType = $this->MovementTypes->get($id, [
            'contain' => ['Movements'],
        ]);

        $this->set('movementType', $movementType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $movementType = $this->MovementTypes->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // debug($datas);
            $movementType = $this->MovementTypes->patchEntity($movementType, $datas);
            if (!$this->MovementTypes->save($movementType)) {
                $this->Flash->error($movementType->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Movement Type')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $this->set('models', $this->MovementTypes->enum('model'));
        $this->set(compact('movementType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Movement Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $movementType = $this->MovementTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            // debug($datas);        
            $movementType = $this->MovementTypes->patchEntity($movementType, $datas);
            if (!$this->MovementTypes->save($movementType)) {
                $this->Flash->error($movementType->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Movement Type')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set('models', $this->MovementTypes->enum('model'));
        $this->set(compact('movementType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Movement Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $movementType = $this->MovementTypes->get($id);
        if (!$this->MovementTypes->delete($movementType)) {
            $this->Flash->error($movementType->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
