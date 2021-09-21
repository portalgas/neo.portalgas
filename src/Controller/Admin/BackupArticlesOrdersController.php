<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * BackupArticlesOrders Controller
 *
 * @property \App\Model\Table\BackupArticlesOrdersTable $BackupArticlesOrders
 *
 * @method \App\Model\Entity\BackupArticlesOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BackupArticlesOrdersController extends AppController
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

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations', 'Orders', 'ArticleOrganizations', 'Articles'],
        ];
        $kBackupArticlesOrders = $this->paginate($this->BackupArticlesOrders);

        $this->set(compact('kBackupArticlesOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Backup Articles Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $kBackupArticlesOrder = $this->BackupArticlesOrders->get($id, [
            'contain' => ['Organizations', 'Orders', 'ArticleOrganizations', 'Articles'],
        ]);

        $this->set('kBackupArticlesOrder', $kBackupArticlesOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $kBackupArticlesOrder = $this->BackupArticlesOrders->newEntity();
        if ($this->request->is('post')) {
            $kBackupArticlesOrder = $this->BackupArticlesOrders->patchEntity($kBackupArticlesOrder, $this->request->getData());
            if ($this->BackupArticlesOrders->save($kBackupArticlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Articles Order'));
        }
        $organizations = $this->BackupArticlesOrders->Organizations->find('list', ['limit' => 200]);
        $orders = $this->BackupArticlesOrders->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->BackupArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->BackupArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('kBackupArticlesOrder', 'organizations', 'orders', 'articleOrganizations', 'articles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Backup Articles Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $kBackupArticlesOrder = $this->BackupArticlesOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $kBackupArticlesOrder = $this->BackupArticlesOrders->patchEntity($kBackupArticlesOrder, $this->request->getData());
            if ($this->BackupArticlesOrders->save($kBackupArticlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Articles Order'));
        }
        $organizations = $this->BackupArticlesOrders->Organizations->find('list', ['limit' => 200]);
        $orders = $this->BackupArticlesOrders->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->BackupArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->BackupArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('kBackupArticlesOrder', 'organizations', 'orders', 'articleOrganizations', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Backup Articles Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $kBackupArticlesOrder = $this->BackupArticlesOrders->get($id);
        if ($this->BackupArticlesOrders->delete($kBackupArticlesOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Backup Articles Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Backup Articles Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
