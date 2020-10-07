<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * ProdGasPromotionsOrganizations Controller
 *
 * @property \App\Model\Table\ProdGasPromotionsOrganizationsTable $ProdGasPromotionsOrganizations
 *
 * @method \App\Model\Entity\ProdGasPromotionsOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProdGasPromotionsOrganizationsController extends AppController
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
            'contain' => ['ProdGasPromotions', 'Organizations', 'Orders', 'Users'],
        ];
        $prodGasPromotionsOrganizations = $this->paginate($this->ProdGasPromotionsOrganizations);

        $this->set(compact('prodGasPromotionsOrganizations'));
    }

    /**
     * View method
     *
     * @param string|null $id K Prod Gas Promotions Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->get($id, [
            'contain' => ['ProdGasPromotions', 'Organizations', 'Orders', 'Users'],
        ]);

        $this->set('prodGasPromotionsOrganization', $prodGasPromotionsOrganization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->newEntity();
        if ($this->request->is('post')) {
            $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->patchEntity($prodGasPromotionsOrganization, $this->request->getData());
            if ($this->ProdGasPromotionsOrganizations->save($prodGasPromotionsOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotions Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotions Organization'));
        }
        $prodGasPromotions = $this->ProdGasPromotionsOrganizations->ProdGasPromotions->find('list', ['limit' => 200]);
        $organizations = $this->ProdGasPromotionsOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->ProdGasPromotionsOrganizations->Orders->find('list', ['limit' => 200]);
        $users = $this->ProdGasPromotionsOrganizations->Users->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotionsOrganization', 'prodGasPromotions', 'organizations', 'orders', 'users'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Prod Gas Promotions Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->patchEntity($prodGasPromotionsOrganization, $this->request->getData());
            if ($this->ProdGasPromotionsOrganizations->save($prodGasPromotionsOrganization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotions Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotions Organization'));
        }
        $prodGasPromotions = $this->ProdGasPromotionsOrganizations->ProdGasPromotions->find('list', ['limit' => 200]);
        $organizations = $this->ProdGasPromotionsOrganizations->Organizations->find('list', ['limit' => 200]);
        $orders = $this->ProdGasPromotionsOrganizations->Orders->find('list', ['limit' => 200]);
        $users = $this->ProdGasPromotionsOrganizations->Users->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotionsOrganization', 'prodGasPromotions', 'organizations', 'orders', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Prod Gas Promotions Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prodGasPromotionsOrganization = $this->ProdGasPromotionsOrganizations->get($id);
        if ($this->ProdGasPromotionsOrganizations->delete($prodGasPromotionsOrganization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Prod Gas Promotions Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Prod Gas Promotions Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
