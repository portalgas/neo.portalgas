<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * DocumentOwnerModels Controller
 *
 * @property \App\Model\Table\DocumentOwnerModelsTable $DocumentOwnerModels
 *
 * @method \App\Model\Entity\DocumentOwnerModel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocumentOwnerModelsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
    
    public function index()
    {
        $documentOwnerModels = $this->paginate($this->DocumentOwnerModels);

        $this->set(compact('documentOwnerModels'));
    }

    /**
     * View method
     *
     * @param string|null $id Document Owner Model id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documentOwnerModel = $this->DocumentOwnerModels->get($id, [
            'contain' => ['Documents'],
        ]);

        $this->set('documentOwnerModel', $documentOwnerModel);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $documentOwnerModel = $this->DocumentOwnerModels->newEntity();
        if ($this->request->is('post')) {
            $documentOwnerModel = $this->DocumentOwnerModels->patchEntity($documentOwnerModel, $this->request->getData());
            if ($this->DocumentOwnerModels->save($documentOwnerModel)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document Owner Model'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document Owner Model'));
        }
        $this->set(compact('documentOwnerModel'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Document Owner Model id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documentOwnerModel = $this->DocumentOwnerModels->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $documentOwnerModel = $this->DocumentOwnerModels->patchEntity($documentOwnerModel, $this->request->getData());
            if ($this->DocumentOwnerModels->save($documentOwnerModel)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document Owner Model'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document Owner Model'));
        }
        $this->set(compact('documentOwnerModel'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Document Owner Model id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documentOwnerModel = $this->DocumentOwnerModels->get($id);
        if ($this->DocumentOwnerModels->delete($documentOwnerModel)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Document Owner Model'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Document Owner Model'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
