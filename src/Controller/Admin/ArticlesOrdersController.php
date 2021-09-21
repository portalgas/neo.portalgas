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
        $this->paginate = [
            'contain' => ['Organizations', 'Orders', 'ArticleOrganizations', 'Articles'],
        ];
        $articlesOrders = $this->paginate($this->ArticlesOrders);

        $this->set(compact('articlesOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id Articles Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $articlesOrder = $this->ArticlesOrders->get($id, [
            'contain' => ['Organizations', 'Orders', 'ArticleOrganizations', 'Articles'],
        ]);

        $this->set('articlesOrder', $articlesOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($order_id)
    {
        $debug = false;

        $ordersTable = TableRegistry::get('Orders');    
        $order = $ordersTable->getById($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $order_id, $debug);

        $scope = $order->owner_articles;
        $supplier_organization_id = $order->supplier_organization_id; 

        $articlesTable = TableRegistry::get('Articles');
        $articles = $articlesTable->getTotArticlesPresentiInArticlesOrder($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $supplier_organization_id);
        // debug($articleResults);
        if ($this->request->is('post')) {

            foreach($articles as $article) {

                $data = [];
                
                /* 
                 * key 
                 *
                 * article_organization_id / article_id 
                 *      dati dell owner_ dell'articolo REFERENT / SUPPLIER / DES / PACT
                 */
                $data['organization_id'] = $this->Authentication->getIdentity()->organization->id;
                $data['order_id'] = $order_id;
                $data['article_organization_id'] = $article->organization_id;
                $data['article_id'] = $article->id;

                $data['name'] = $article->name;
                $data['prezzo'] = $article->prezzo;
                $data['pezzi_confezione'] = $article->pezzi_confezione;
                $data['qta_minima'] = $article->qta_minima;
                $data['qta_massima'] = $article->qta_massima;
                $data['qta_minima_order'] = $article->qta_minima_order;
                $data['qta_massima_order'] = $article->qta_massima_order;
                $data['qta_multipli'] = $article->qta_multipli;
                $data['alert_to_qta'] = $article->alert_to_qta;
                $data['prezzo'] = $article->prezzo;
                $data['prezzo'] = $article->prezzo;
                    
                $data['send_mail'] = 'N';
                $data['qta_cart'] = '0';
                $data['flag_bookmarks'] = 'N';
                $data['stato'] = 'Y';

                $articlesOrder = $this->ArticlesOrders->newEntity();
                $articlesOrder = $this->ArticlesOrders->patchEntity($articlesOrder, $data);
                debug($data);
                /*
                 * workaround
                 */
                $articlesOrder->organization_id = $this->Authentication->getIdentity()->organization->id;
                $articlesOrder->order_id = $order_id;
                $articlesOrder->article_organization_id = $article->organization_id;
                $articlesOrder->article_id = $article->id;

                if (!$this->ArticlesOrders->save($articlesOrder)) {
                    $this->Flash->error($articlesOrder->getErrors());
                }  

                /*
                 * aggiorno stato ordine
                 */   
                $orderState = $ordersTable->get($order_id, ['contain' => []]);
                $data = [];
                $data['state_code'] = 'OPEN'; // OPEN-NEXT                
                $orderState = $ordersTable->patchEntity($orderState, $data);
                if (!$ordersTable->save($orderState)) {
                    $this->Flash->error($orderState->getErrors());
                }
            } // foreach($articles as $article)

            $this->Flash->success(__('The {0} has been saved.', 'Articles Order'));
            // return $this->redirect(['action' => 'index']);

        } // end if ($this->request->is('post')) 
          
        $this->set(compact('scope', 'order', 'articles'));
    }

    public function add2() {
        $articlesOrder = $this->ArticlesOrders->newEntity();
        if ($this->request->is('post')) {
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
