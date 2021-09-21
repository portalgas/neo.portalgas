<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Storerooms Controller
 *
 * @property \App\Model\Table\StoreroomsTable $Storerooms
 *
 * @method \App\Model\Entity\KStoreroom[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StoreroomsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
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
            'contain' => ['Organizations', 'Deliveries', 'Users', 'Articles', 'ArticleOrganizations'],
        ];
        $storerooms = $this->paginate($this->Storerooms);

        $this->set(compact('storerooms'));
    }

    /**
     * View method
     *
     * @param string|null $id K Storeroom id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $storeroom = $this->Storerooms->get($id, [
            'contain' => ['Organizations', 'Deliveries', 'Users', 'Articles', 'ArticleOrganizations'],
        ]);

        $this->set('storeroom', $storeroom);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $storeroom = $this->Storerooms->newEntity();
        if ($this->request->is('post')) {
            $storeroom = $this->Storerooms->patchEntity($storeroom, $this->request->getData());
            if ($this->Storerooms->save($storeroom)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Storeroom'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Storeroom'));
        }
        $organizations = $this->Storerooms->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->Storerooms->Deliveries->find('list', ['limit' => 200]);
        $users = $this->Storerooms->Users->find('list', ['limit' => 200]);
        $articles = $this->Storerooms->Articles->find('list', ['limit' => 200]);
        $articleOrganizations = $this->Storerooms->ArticleOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('storeroom', 'organizations', 'deliveries', 'users', 'articles', 'articleOrganizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Storeroom id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $storeroom = $this->Storerooms->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $storeroom = $this->Storerooms->patchEntity($storeroom, $this->request->getData());
            if ($this->Storerooms->save($storeroom)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Storeroom'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Storeroom'));
        }
        $organizations = $this->Storerooms->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->Storerooms->Deliveries->find('list', ['limit' => 200]);
        $users = $this->Storerooms->Users->find('list', ['limit' => 200]);
        $articles = $this->Storerooms->Articles->find('list', ['limit' => 200]);
        $articleOrganizations = $this->Storerooms->ArticleOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('storeroom', 'organizations', 'deliveries', 'users', 'articles', 'articleOrganizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Storeroom id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $storeroom = $this->Storerooms->get($id);
        if ($this->Storerooms->delete($storeroom)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Storeroom'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Storeroom'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
