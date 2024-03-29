<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Cashes Controller
 *
 * @property \App\Model\Table\CashesTable $Cashes
 *
 * @method \App\Model\Entity\Cash[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CashesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    /* 
     * elenco produttori per gestione chi e' escluso dal prepagato
     * i produttori che sono inseriti in supplier_organization_cash_excludeds saranno esclusi dal calcolo del prepagato:
     *  => gli acquisti effettuati con loro sono esclusi dal calcolo del prepagato CashesUser::ctrlLimitCart() => CashesUser::isSupplierOrganizationCashExcluded
     */
    public function supplierOrganizationFilter()
    {     
        if(!$this->_user->acl['isManager'] || $this->_user->organization->paramsConfig['hasCashFilterSupplier']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }   

        $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');
        $results = $supplierOrganizationCashExcludedsTable->gets($this->Authentication->getIdentity());

        $this->set(compact('results'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $this->paginate = [
            'contain' => ['Organizations', 'Users'],
        ];
        $cashes = $this->paginate($this->Cashes);

        $this->set(compact('cashes'));
    }

    /**
     * View method
     *
     * @param string|null $id K Cash id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $cash = $this->Cashes->get($id, [
            'contain' => ['Organizations', 'Users'],
        ]);

        $this->set('cash', $cash);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $cash = $this->Cashes->newEntity();
        if ($this->request->is('post')) {
            $cash = $this->Cashes->patchEntity($cash, $this->request->getData());
            if ($this->Cashes->save($cash)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cash'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cash'));
        }
        $organizations = $this->Cashes->Organizations->find('list', ['limit' => 200]);
        $users = $this->Cashes->Users->find('list', ['limit' => 200]);
        $this->set(compact('cash', 'organizations', 'users'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Cash id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $cash = $this->Cashes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cash = $this->Cashes->patchEntity($cash, $this->request->getData());
            if ($this->Cashes->save($cash)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Cash'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Cash'));
        }
        $organizations = $this->Cashes->Organizations->find('list', ['limit' => 200]);
        $users = $this->Cashes->Users->find('list', ['limit' => 200]);
        $this->set(compact('cash', 'organizations', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Cash id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        $this->request->allowMethod(['post', 'delete']);
        $cash = $this->Cashes->get($id);
        if ($this->Cashes->delete($cash)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Cash'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Cash'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
