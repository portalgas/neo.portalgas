<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * MarketArticles Controller
 *
 * @property \App\Model\Table\MarketArticlesTable $MarketArticles
 *
 * @method \App\Model\Entity\MarketArticle[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MarketArticlesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot']) {
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
            'contain' => ['Organizations', 'Markets', 'Articles'],
        ];
        $marketArticles = $this->paginate($this->MarketArticles);

        $this->set(compact('marketArticles'));
    }

    /**
     * View method
     *
     * @param string|null $id Market Article id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $marketArticle = $this->MarketArticles->get($id, [
            'contain' => ['Organizations', 'Markets', 'Articles'],
        ]);

        $this->set('marketArticle', $marketArticle);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $marketArticle = $this->MarketArticles->newEntity();
        if ($this->request->is('post')) {
            $marketArticle = $this->MarketArticles->patchEntity($marketArticle, $this->request->getData());
            if ($this->MarketArticles->save($marketArticle)) {
                $this->Flash->success(__('The {0} has been saved.', 'Market Article'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Market Article'));
        }
        $organizations = $this->MarketArticles->Organizations->find('list', ['limit' => 200]);
        $markets = $this->MarketArticles->Markets->find('list', ['limit' => 200]);
        $articles = $this->MarketArticles->Articles->find('list', ['limit' => 200]);
        $this->set(compact('marketArticle', 'organizations', 'markets', 'articles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Market Article id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $marketArticle = $this->MarketArticles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $marketArticle = $this->MarketArticles->patchEntity($marketArticle, $this->request->getData());
            if ($this->MarketArticles->save($marketArticle)) {
                $this->Flash->success(__('The {0} has been saved.', 'Market Article'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Market Article'));
        }
        $organizations = $this->MarketArticles->Organizations->find('list', ['limit' => 200]);
        $markets = $this->MarketArticles->Markets->find('list', ['limit' => 200]);
        $articles = $this->MarketArticles->Articles->find('list', ['limit' => 200]);
        $this->set(compact('marketArticle', 'organizations', 'markets', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Market Article id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $marketArticle = $this->MarketArticles->get($id);
        if ($this->MarketArticles->delete($marketArticle)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Market Article'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Market Article'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
