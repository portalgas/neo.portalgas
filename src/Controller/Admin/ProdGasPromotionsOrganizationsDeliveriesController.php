<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * ProdGasPromotionsOrganizationsDeliveries Controller
 *
 * @property \App\Model\Table\ProdGasPromotionsOrganizationsDeliveriesTable $ProdGasPromotionsOrganizationsDeliveries
 *
 * @method \App\Model\Entity\ProdGasPromotionsOrganizationsDelivery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProdGasPromotionsOrganizationsDeliveriesController extends AppController
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
            'contain' => ['Suppliers', 'ProdGasPromotions', 'Organizations', 'Deliveries'],
        ];
        $prodGasPromotionsOrganizationsDeliveries = $this->paginate($this->ProdGasPromotionsOrganizationsDeliveries);

        $this->set(compact('prodGasPromotionsOrganizationsDeliveries'));
    }

    /**
     * View method
     *
     * @param string|null $id K Prod Gas Promotions Organizations Delivery id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->get($id, [
            'contain' => ['Suppliers', 'ProdGasPromotions', 'Organizations', 'Deliveries'],
        ]);

        $this->set('prodGasPromotionsOrganizationsDelivery', $prodGasPromotionsOrganizationsDelivery);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->newEntity();
        if ($this->request->is('post')) {
            $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->patchEntity($prodGasPromotionsOrganizationsDelivery, $this->request->getData());
            if ($this->ProdGasPromotionsOrganizationsDeliveries->save($prodGasPromotionsOrganizationsDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotions Organizations Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotions Organizations Delivery'));
        }
        $suppliers = $this->ProdGasPromotionsOrganizationsDeliveries->Suppliers->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->ProdGasPromotionsOrganizationsDeliveries->ProdGasPromotions->find('list', ['limit' => 200]);
        $organizations = $this->ProdGasPromotionsOrganizationsDeliveries->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->ProdGasPromotionsOrganizationsDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotionsOrganizationsDelivery', 'suppliers', 'prodGasPromotions', 'organizations', 'deliveries'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Prod Gas Promotions Organizations Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->patchEntity($prodGasPromotionsOrganizationsDelivery, $this->request->getData());
            if ($this->ProdGasPromotionsOrganizationsDeliveries->save($prodGasPromotionsOrganizationsDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Prod Gas Promotions Organizations Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Prod Gas Promotions Organizations Delivery'));
        }
        $suppliers = $this->ProdGasPromotionsOrganizationsDeliveries->Suppliers->find('list', ['limit' => 200]);
        $prodGasPromotions = $this->ProdGasPromotionsOrganizationsDeliveries->ProdGasPromotions->find('list', ['limit' => 200]);
        $organizations = $this->ProdGasPromotionsOrganizationsDeliveries->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->ProdGasPromotionsOrganizationsDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('prodGasPromotionsOrganizationsDelivery', 'suppliers', 'prodGasPromotions', 'organizations', 'deliveries'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Prod Gas Promotions Organizations Delivery id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prodGasPromotionsOrganizationsDelivery = $this->ProdGasPromotionsOrganizationsDeliveries->get($id);
        if ($this->ProdGasPromotionsOrganizationsDeliveries->delete($prodGasPromotionsOrganizationsDelivery)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Prod Gas Promotions Organizations Delivery'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Prod Gas Promotions Organizations Delivery'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
