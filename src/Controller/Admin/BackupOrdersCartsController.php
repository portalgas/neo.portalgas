<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * BackupOrdersCarts Controller
 *
 * @property \App\Model\Table\BackupOrdersCartsTable $BackupOrdersCarts
 *
 * @method \App\Model\Entity\BackupOrdersCart[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BackupOrdersCartsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
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
            'contain' => ['Organizations', 'Users', 'Orders', 'ArticleOrganizations', 'Articles'],
        ];
        $backupOrdersCarts = $this->paginate($this->BackupOrdersCarts);

        $this->set(compact('backupOrdersCarts'));
    }

    /**
     * View method
     *
     * @param string|null $id K Backup Orders Cart id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $backupOrdersCart = $this->BackupOrdersCarts->get($id, [
            'contain' => ['Organizations', 'Users', 'Orders', 'ArticleOrganizations', 'Articles'],
        ]);

        $this->set('backupOrdersCart', $backupOrdersCart);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $backupOrdersCart = $this->BackupOrdersCarts->newEntity();
        if ($this->request->is('post')) {
            $backupOrdersCart = $this->BackupOrdersCarts->patchEntity($backupOrdersCart, $this->request->getData());
            if ($this->BackupOrdersCarts->save($backupOrdersCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Cart'));
        }
        $organizations = $this->BackupOrdersCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->BackupOrdersCarts->Users->find('list', ['limit' => 200]);
        $orders = $this->BackupOrdersCarts->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->BackupOrdersCarts->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->BackupOrdersCarts->Articles->find('list', ['limit' => 200]);
        $this->set(compact('backupOrdersCart', 'organizations', 'users', 'orders', 'articleOrganizations', 'articles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Backup Orders Cart id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $backupOrdersCart = $this->BackupOrdersCarts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $backupOrdersCart = $this->BackupOrdersCarts->patchEntity($backupOrdersCart, $this->request->getData());
            if ($this->BackupOrdersCarts->save($backupOrdersCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Backup Orders Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Backup Orders Cart'));
        }
        $organizations = $this->BackupOrdersCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->BackupOrdersCarts->Users->find('list', ['limit' => 200]);
        $orders = $this->BackupOrdersCarts->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->BackupOrdersCarts->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->BackupOrdersCarts->Articles->find('list', ['limit' => 200]);
        $this->set(compact('backupOrdersCart', 'organizations', 'users', 'orders', 'articleOrganizations', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Backup Orders Cart id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $backupOrdersCart = $this->BackupOrdersCarts->get($id);
        if ($this->BackupOrdersCarts->delete($backupOrdersCart)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Backup Orders Cart'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Backup Orders Cart'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
