<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * DesSuppliers Controller
 *
 * @property \App\Model\Table\DesSuppliersTable $DesSuppliers
 *
 * @method \App\Model\Entity\DesSupplier[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DesSuppliersController extends AppController
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
	
    public function index()
    {
        $this->paginate = [
            'contain' => ['Des', 'Suppliers', 'OwnOrganizations'],
        ];
        $desSuppliers = $this->paginate($this->DesSuppliers);

        $this->set(compact('desSuppliers'));
    }

    /**
     * View method
     *
     * @param string|null $id K Des Supplier id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $desSupplier = $this->DesSuppliers->get($id, [
            'contain' => ['Des', 'Suppliers', 'OwnOrganizations'],
        ]);

        $this->set('desSupplier', $desSupplier);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $desSupplier = $this->DesSuppliers->newEntity();
        if ($this->request->is('post')) {
            $desSupplier = $this->DesSuppliers->patchEntity($desSupplier, $this->request->getData());
            if ($this->DesSuppliers->save($desSupplier)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Supplier'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Supplier'));
        }
        $des = $this->DesSuppliers->Des->find('list', ['limit' => 200]);
        $suppliers = $this->DesSuppliers->Suppliers->find('list', ['limit' => 200]);
        $ownOrganizations = $this->DesSuppliers->OwnOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('desSupplier', 'des', 'suppliers', 'ownOrganizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Des Supplier id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $desSupplier = $this->DesSuppliers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $desSupplier = $this->DesSuppliers->patchEntity($desSupplier, $this->request->getData());
            if ($this->DesSuppliers->save($desSupplier)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Des Supplier'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Des Supplier'));
        }
        $des = $this->DesSuppliers->Des->find('list', ['limit' => 200]);
        $suppliers = $this->DesSuppliers->Suppliers->find('list', ['limit' => 200]);
        $ownOrganizations = $this->DesSuppliers->OwnOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('desSupplier', 'des', 'suppliers', 'ownOrganizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Des Supplier id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $desSupplier = $this->DesSuppliers->get($id);
        if ($this->DesSuppliers->delete($desSupplier)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Des Supplier'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Des Supplier'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
