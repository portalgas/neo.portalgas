<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * StatArticlesOrders Controller
 *
 * @property \App\Model\Table\StatArticlesOrdersTable $StatArticlesOrders
 *
 * @method \App\Model\Entity\StatArticlesOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatArticlesOrdersController extends AppController
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
            'contain' => ['Organizations', 'StatOrders', 'ArticleOrganizations', 'Articles'],
        ];
        $statArticlesOrders = $this->paginate($this->StatArticlesOrders);

        $this->set(compact('statArticlesOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id K Stat Articles Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $statArticlesOrder = $this->StatArticlesOrders->get($id, [
            'contain' => ['Organizations', 'StatOrders', 'ArticleOrganizations', 'Articles'],
        ]);

        $this->set('statArticlesOrder', $statArticlesOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $statArticlesOrder = $this->StatArticlesOrders->newEntity();
        if ($this->request->is('post')) {
            $statArticlesOrder = $this->StatArticlesOrders->patchEntity($statArticlesOrder, $this->request->getData());
            if ($this->StatArticlesOrders->save($statArticlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Articles Order'));
        }
        $organizations = $this->StatArticlesOrders->Organizations->find('list', ['limit' => 200]);
        $statOrders = $this->StatArticlesOrders->StatOrders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->StatArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->StatArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('statArticlesOrder', 'organizations', 'statOrders', 'articleOrganizations', 'articles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Stat Articles Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $statArticlesOrder = $this->StatArticlesOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $statArticlesOrder = $this->StatArticlesOrders->patchEntity($statArticlesOrder, $this->request->getData());
            if ($this->StatArticlesOrders->save($statArticlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Articles Order'));
        }
        $organizations = $this->StatArticlesOrders->Organizations->find('list', ['limit' => 200]);
        $statOrders = $this->StatArticlesOrders->StatOrders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->StatArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->StatArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('statArticlesOrder', 'organizations', 'statOrders', 'articleOrganizations', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Stat Articles Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $statArticlesOrder = $this->StatArticlesOrders->get($id);
        if ($this->StatArticlesOrders->delete($statArticlesOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Stat Articles Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Stat Articles Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
