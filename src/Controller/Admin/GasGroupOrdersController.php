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
class GasGroupOrdersController extends AppController
{
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
             !$user->acl['isGasGroupsManagerOrders']
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
        $this->paginate = [
            'contain' => ['Organizations', 'GasGroups', 'Orders'],
        ];
        $gasGroupOrders = $this->paginate($this->GasGroupOrders);

        $this->set(compact('gasGroupOrders'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gasGroupOrder = $this->GasGroupOrders->get($id, [
            'contain' => ['Organizations', 'GasGroups', 'Orders'],
        ]);

        $this->set('gasGroupOrder', $gasGroupOrder);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gasGroupOrder = $this->GasGroupOrders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // debug($datas);
            $gasGroupOrder = $this->GasGroupOrders->patchEntity($gasGroupOrder, $datas);
            if (!$this->GasGroupOrders->save($gasGroupOrder)) {
                $this->Flash->error($gasGroupOrder->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group Order')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $organizations = $this->GasGroupOrders->Organizations->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupOrders->GasGroups->find('list', ['limit' => 200]);
        $orders = $this->GasGroupOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupOrder', 'organizations', 'gasGroups', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gasGroupOrder = $this->GasGroupOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            // debug($datas);        
            $gasGroupOrder = $this->GasGroupOrders->patchEntity($gasGroupOrder, $datas);
            if (!$this->GasGroupOrders->save($gasGroupOrder)) {
                $this->Flash->error($gasGroupOrder->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Gas Group Order')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $organizations = $this->GasGroupOrders->Organizations->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupOrders->GasGroups->find('list', ['limit' => 200]);
        $orders = $this->GasGroupOrders->Orders->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupOrder', 'organizations', 'gasGroups', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroupOrder = $this->GasGroupOrders->get($id);
        if (!$this->GasGroupOrders->delete($gasGroupOrder)) {
            $this->Flash->error($gasGroupOrder->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
