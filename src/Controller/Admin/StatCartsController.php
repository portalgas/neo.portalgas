<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * StatCarts Controller
 *
 * @property \App\Model\Table\StatCartsTable $StatCarts
 *
 * @method \App\Model\Entity\StatCart[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatCartsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auths->isRoot($this->user)) {
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
            'contain' => ['Organizations', 'Users', 'ArticleOrganizations', 'Articles', 'StatOrders'],
        ];
        $statCarts = $this->paginate($this->StatCarts);

        $this->set(compact('statCarts'));
    }

    /**
     * View method
     *
     * @param string|null $id K Stat Cart id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $statCart = $this->StatCarts->get($id, [
            'contain' => ['Organizations', 'Users', 'ArticleOrganizations', 'Articles', 'StatOrders'],
        ]);

        $this->set('statCart', $statCart);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $statCart = $this->StatCarts->newEntity();
        if ($this->request->is('post')) {
            $statCart = $this->StatCarts->patchEntity($statCart, $this->request->getData());
            if ($this->StatCarts->save($statCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Cart'));
        }
        $organizations = $this->StatCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->StatCarts->Users->find('list', ['limit' => 200]);
        $articleOrganizations = $this->StatCarts->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->StatCarts->Articles->find('list', ['limit' => 200]);
        $statOrders = $this->StatCarts->StatOrders->find('list', ['limit' => 200]);
        $this->set(compact('statCart', 'organizations', 'users', 'articleOrganizations', 'articles', 'statOrders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Stat Cart id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $statCart = $this->StatCarts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $statCart = $this->StatCarts->patchEntity($statCart, $this->request->getData());
            if ($this->StatCarts->save($statCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Cart'));
        }
        $organizations = $this->StatCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->StatCarts->Users->find('list', ['limit' => 200]);
        $articleOrganizations = $this->StatCarts->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->StatCarts->Articles->find('list', ['limit' => 200]);
        $statOrders = $this->StatCarts->StatOrders->find('list', ['limit' => 200]);
        $this->set(compact('statCart', 'organizations', 'users', 'articleOrganizations', 'articles', 'statOrders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Stat Cart id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $statCart = $this->StatCarts->get($id);
        if ($this->StatCarts->delete($statCart)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Stat Cart'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Stat Cart'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
