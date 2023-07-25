<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * LoopsOrders Controller
 *
 * @property \App\Model\Table\LoopsOrdersTable $LoopsOrders
 *
 * @method \App\Model\Entity\LoopsOrders[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LoopsOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SuppliersOrganization');

        if(!isset($this->_user->acl)) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        if(!$this->_user->acl['isSuperReferente']  && 
           !$this->_user->acl['isReferentGeneric']) { 
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
            'contain' => ['LoopsDeliveries', 'SuppliersOrganizations', 'Users', 'Orders'],
        ];
        $loopsOrders = $this->paginate($this->LoopsOrders);

        $this->set(compact('loopsOrders'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $loopsOrder = $this->LoopsOrders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            
            $datas['organization_id'] = $this->_organization->id;
            $datas['user_id'] = $this->_user->id;
            $datas['flag_send_mail'] = 'N';
            // debug($datas);
            $loopsOrder = $this->LoopsOrders->patchEntity($loopsOrder, $datas);
            if (!$this->LoopsOrders->save($loopsOrder)) {
                $this->Flash->error($loopsOrder->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Loops Order')));

                return $this->redirect(['action' => 'index']);
            }
        } // end post
 
        $loopsDeliveries = $this->LoopsOrders->LoopsDeliveries->getsList($this->_user, $this->_organization->id);
        if($loopsDeliveries->count()==0) {
            $this->Flash->error(__('msg_loops_orders_not_loops_deliveries'));
            return $this->redirect(['action' => 'index']);
        }

        $order_type_id = Configure::read('Order.type.gas');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id);
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        
        $this->set(compact('order_type_id', 'loopsOrder', 'loopsDeliveries', 'suppliersOrganizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Loops Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $loopsOrder = $this->LoopsOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            
            $datas['organization_id'] = $this->_organization->id;
            $datas['user_id'] = $this->_user->id;
            $datas['flag_send_mail'] = 'N';
            // debug($datas);
            $loopsOrder = $this->LoopsOrders->patchEntity($loopsOrder, $datas);
            if (!$this->LoopsOrders->save($loopsOrder)) {
                $this->Flash->error($loopsOrder->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Loops Order')));

                return $this->redirect(['action' => 'index']);
            }
        } // end post

        $loopsDeliveries = $this->LoopsOrders->LoopsDeliveries->getsList($this->_user, $this->_organization->id);
        if($loopsDeliveries->count()==0) {
            $this->Flash->error(__('msg_loops_orders_not_loops_deliveries'));
            return $this->redirect(['action' => 'index']);
        }

        $order_type_id = Configure::read('Order.type.gas');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id);
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
    
        $this->set(compact('order_type_id', 'loopsOrder', 'loopsDeliveries', 'suppliersOrganizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Loops Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $loopsOrder = $this->LoopsOrders->get($id);
        if (!$this->LoopsOrders->delete($loopsOrder)) {
            $this->Flash->error($loopsOrder->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
