<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * ProdGasPromotions Controller
 *
 * @property \App\Model\Table\ProdGasPromotionsTable $ProdGasPromotions
 *
 * @method \App\Model\Entity\ProdGasPromotion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProdGasPromotionsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
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
            'contain' => ['Organizations'],
        ];
        $prodGasPromotions = $this->paginate($this->ProdGasPromotions);

        $this->set(compact('prodGasPromotions'));
    }

    /**
     * View method
     *
     * @param string|null $id K Prod Gas Promotion id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $prodGasPromotion = $this->ProdGasPromotions->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('prodGasPromotion', $prodGasPromotion);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $prodGasPromotion = $this->ProdGasPromotions->newEntity();
        if ($this->request->is('post')) {
            $prodGasPromotion = $this->ProdGasPromotions->patchEntity($prodGasPromotion, $this->request->getData());
            if ($this->ProdGasPromotions->save($prodGasPromotion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotion'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotion'));
        }
        $organizations = $this->ProdGasPromotions->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotion', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Prod Gas Promotion id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $prodGasPromotion = $this->ProdGasPromotions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prodGasPromotion = $this->ProdGasPromotions->patchEntity($prodGasPromotion, $this->request->getData());
            if ($this->ProdGasPromotions->save($prodGasPromotion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotion'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotion'));
        }
        $organizations = $this->ProdGasPromotions->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotion', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Prod Gas Promotion id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prodGasPromotion = $this->ProdGasPromotions->get($id);
        if ($this->ProdGasPromotions->delete($prodGasPromotion)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Prod Gas Promotion'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Prod Gas Promotion'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
