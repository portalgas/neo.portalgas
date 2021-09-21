<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * DocumentReferenceModels Controller
 *
 * @property \App\Model\Table\DocumentReferenceModelsTable $DocumentReferenceModels
 *
 * @method \App\Model\Entity\DocumentReferenceModel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocumentReferenceModelsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
    
    public function index()
    {
        $documentReferenceModels = $this->paginate($this->DocumentReferenceModels);

        $this->set(compact('documentReferenceModels'));
    }

    /**
     * View method
     *
     * @param string|null $id Document Reference Model id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documentReferenceModel = $this->DocumentReferenceModels->get($id, [
            'contain' => ['Documents'],
        ]);

        $this->set('documentReferenceModel', $documentReferenceModel);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $documentReferenceModel = $this->DocumentReferenceModels->newEntity();
        if ($this->request->is('post')) {
            $documentReferenceModel = $this->DocumentReferenceModels->patchEntity($documentReferenceModel, $this->request->getData());
            if ($this->DocumentReferenceModels->save($documentReferenceModel)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document Reference Model'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document Reference Model'));
        }
        $this->set(compact('documentReferenceModel'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Document Reference Model id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documentReferenceModel = $this->DocumentReferenceModels->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $documentReferenceModel = $this->DocumentReferenceModels->patchEntity($documentReferenceModel, $this->request->getData());
            if ($this->DocumentReferenceModels->save($documentReferenceModel)) {
                $this->Flash->success(__('The {0} has been saved.', 'Document Reference Model'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Document Reference Model'));
        }
        $this->set(compact('documentReferenceModel'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Document Reference Model id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documentReferenceModel = $this->DocumentReferenceModels->get($id);
        if ($this->DocumentReferenceModels->delete($documentReferenceModel)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Document Reference Model'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Document Reference Model'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
