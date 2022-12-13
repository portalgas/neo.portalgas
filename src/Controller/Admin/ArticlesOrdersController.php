<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * ArticlesOrders Controller
 *
 * @property \App\Model\Table\ArticlesOrdersTable $ArticlesOrders
 *
 * @method \App\Model\Entity\ArticlesOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');

        if(!isset($this->_user->acl)) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        // per ora solo i gruppi
        if($this->_organization->paramsConfig['hasGasGroups']=='N' || 
            !$this->_user->acl['isGasGroupsManagerParentOrders']  || 
            !$this->_user->acl['isGasGroupsManagerOrders']) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        } 
    }

    public function index($order_type_id, $order_id)
    {
        $debug = false;

        $ordersTable = TableRegistry::get('Orders');    
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);

$ArticlesOrders = [];
$articles = [];        
        if ($this->request->is('post')) {

            foreach($articles as $article) {

                $data = [];
                

            } // foreach($articles as $article)

            $this->Flash->success(__('The {0} has been saved.', 'Articles Order'));
            // return $this->redirect(['action' => 'index']);

        } // end if ($this->request->is('post')) 
          
        $this->set(compact('order_type_id', 'order', 'ArticlesOrders', 'articles'));
    }

    public function add2($order_type_id, $order_id) {
        $articlesOrder = $this->ArticlesOrders->newEntity();
        if ($this->request->is('post')) {
            $articlesOrder = $this->ArticlesOrders->patchEntity($articlesOrder, $this->request->getData());
            if ($this->ArticlesOrders->save($articlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Articles Order'));
        }
        $orders = $this->ArticlesOrders->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->ArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->ArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('articlesOrder', 'orders', 'articleOrganizations', 'articles'));
        $this->set(compact('order_type_id', 'order'));        
    }


    /**
     * Edit method
     *
     * @param string|null $id Articles Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $articlesOrder = $this->ArticlesOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $articlesOrder = $this->ArticlesOrders->patchEntity($articlesOrder, $this->request->getData());
            if ($this->ArticlesOrders->save($articlesOrder)) {
                $this->Flash->success(__('The {0} has been saved.', 'Articles Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Articles Order'));
        }
        $organizations = $this->ArticlesOrders->Organizations->find('list', ['limit' => 200]);
        $orders = $this->ArticlesOrders->Orders->find('list', ['limit' => 200]);
        $articleOrganizations = $this->ArticlesOrders->ArticleOrganizations->find('list', ['limit' => 200]);
        $articles = $this->ArticlesOrders->Articles->find('list', ['limit' => 200]);
        $this->set(compact('articlesOrder', 'organizations', 'orders', 'articleOrganizations', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Articles Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $articlesOrder = $this->ArticlesOrders->get($id);
        if ($this->ArticlesOrders->delete($articlesOrder)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Articles Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Articles Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
