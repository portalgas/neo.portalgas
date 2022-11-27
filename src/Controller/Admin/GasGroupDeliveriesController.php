<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * GasGroupOrders Controller
 *
 * @property \App\Model\Table\GasGroupOrdersTable $GasGroupOrders
 *
 * @method \App\Model\Entity\GasGroupOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupDeliveriesController extends AppController
{
    private $_type='GAS-GROUP';

    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();
        $organization = $user->organization; // gas scelto
      
        if(!isset($user->acl) ||
            !isset($organization->paramsConfig['hasGasGroups']) || 
            $organization->paramsConfig['hasGasGroups']=='N' || 
             !$user->acl['isGasGropusManagerDeliveries']
            ) { 
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
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $where = [// 'Deliveries.type' => $this->_type, 
                  'Deliveries.organization_id' => $organization_id, 
                 ];
        $this->paginate = [
            'contain' => ['GasGroups' => ['GasGroupUsers'], 'Deliveries' => ['Orders']],
            'conditions' => $where,
            'order' => ['Deliveries.data']
        ];
        
        $gasGroupDeliveries = $this->paginate($this->GasGroupDeliveries);

        $this->set(compact('gasGroupDeliveries'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => ['Organizations', 'GasGroups', 'Deliveries'],
        ]);

        $this->set('gasGroupDelivery', $gasGroupDelivery);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // debug($datas);
            $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $datas);
            if (!$this->GasGroupDeliveries->save($gasGroupDelivery)) {
                $this->Flash->error($gasGroupDelivery->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group Delivery')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $organizations = $this->GasGroupDeliveries->Organizations->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupDeliveries->GasGroups->find('list', ['limit' => 200]);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupDelivery', 'organizations', 'gasGroups', 'deliveries'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            // debug($datas);        
            $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $datas);
            if (!$this->GasGroupDeliveries->save($gasGroupDelivery)) {
                $this->Flash->error($gasGroupDelivery->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group Delivery')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $organizations = $this->GasGroupDeliveries->Organizations->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupDeliveries->GasGroups->find('list', ['limit' => 200]);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupDelivery', 'organizations', 'gasGroups', 'deliveries'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id);
        if (!$this->GasGroupDeliveries->delete($gasGroupDelivery)) {
            $this->Flash->error($gasGroupDelivery->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
