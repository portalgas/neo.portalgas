<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * ProdGasArticlesPromotions Controller
 *
 * @property \App\Model\Table\ProdGasArticlesPromotionsTable $ProdGasArticlesPromotions
 *
 * @method \App\Model\Entity\ProdGasArticlesPromotion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProdGasArticlesPromotionsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
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
            'contain' => ['Organizations', 'ProdGasPromotions', 'Articles'],
        ];
        $prodGasArticlesPromotions = $this->paginate($this->ProdGasArticlesPromotions);

        $this->set(compact('prodGasArticlesPromotions'));
    }

    /**
     * View method
     *
     * @param string|null $id K Prod Gas Articles Promotion id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->get($id, [
            'contain' => ['Organizations', 'ProdGasPromotions', 'Articles'],
        ]);

        $this->set('prodGasArticlesPromotion', $prodGasArticlesPromotion);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->newEntity();
        if ($this->request->is('post')) {
            $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->patchEntity($prodGasArticlesPromotion, $this->request->getData());
            if ($this->ProdGasArticlesPromotions->save($prodGasArticlesPromotion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Articles Promotion'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Articles Promotion'));
        }
        $organizations = $this->ProdGasArticlesPromotions->Organizations->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->ProdGasArticlesPromotions->ProdGasPromotions->find('list', ['limit' => 200]);
        $articles = $this->ProdGasArticlesPromotions->Articles->find('list', ['limit' => 200]);
        $this->set(compact('prodGasArticlesPromotion', 'organizations', 'prodGasPromotions', 'articles'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Prod Gas Articles Promotion id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->patchEntity($prodGasArticlesPromotion, $this->request->getData());
            if ($this->ProdGasArticlesPromotions->save($prodGasArticlesPromotion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Articles Promotion'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Articles Promotion'));
        }
        $organizations = $this->ProdGasArticlesPromotions->Organizations->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->ProdGasArticlesPromotions->ProdGasPromotions->find('list', ['limit' => 200]);
        $articles = $this->ProdGasArticlesPromotions->Articles->find('list', ['limit' => 200]);
        $this->set(compact('prodGasArticlesPromotion', 'organizations', 'prodGasPromotions', 'articles'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Prod Gas Articles Promotion id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prodGasArticlesPromotion = $this->ProdGasArticlesPromotions->get($id);
        if ($this->ProdGasArticlesPromotions->delete($prodGasArticlesPromotion)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Prod Gas Articles Promotion'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Prod Gas Articles Promotion'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
