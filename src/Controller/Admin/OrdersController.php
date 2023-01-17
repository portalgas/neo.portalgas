<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Form\OrderForm;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    private $_order_type_ids = [];  // tipologie d'ordine abilitate
    private $_ordersTable = null;    // istanza del model Orders / OrderGasGroups ...
   
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Delivery');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('PriceType');

        /* 
         * gestisco solo gruppi
         */
        $this->_order_type_ids = [
            Configure::read('Order.type.gas'),
            Configure::read('Order.type.des'),
            Configure::read('Order.type.des_titolare'),
            Configure::read('Order.type.gas_parent_groups'),
            Configure::read('Order.type.gas_groups')
        ];

        if(!isset($this->_user->acl)) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        /* 
         * order_type_id 
         * se Configure::read('Order.type.gas_groups'); 
         * ctrl che lo user abbia creato un gruppo
         */
        $pass = $this->request->pass;
        if(!empty($pass) && isset($pass[0])) {
            $order_type_id = $pass[0];

            if(!in_array($order_type_id, $this->_order_type_ids)) {
                $this->Flash->error(__('msg_error_param_order_type_id'), ['escape' => false]);
                return $this->redirect(Configure::read('routes_msg_stop'));    
            }
           
            switch($order_type_id) {
                case Configure::read('Order.type.gas'):
                    if(!$this->_user->acl['isReferentGeneric']  &&
                       !$this->_user->acl['isSuperReferente']) { 
                        $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
                        return $this->redirect(Configure::read('routes_msg_stop'));
                    }             
                break;
                case Configure::read('Order.type.gas_groups'):
                case Configure::read('Order.type.gas_parent_groups'):
                    if($this->_organization->paramsConfig['hasGasGroups']=='N' || (
                        !$this->_user->acl['isGasGroupsManagerParentOrders']  && 
                        !$this->_user->acl['isGasGroupsManagerOrders'])) { 
                        $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
                        return $this->redirect(Configure::read('routes_msg_stop'));
                    } 
        
                    if($order_type_id==Configure::read('Order.type.gas_groups')) {
                        $gasGroupsTable = TableRegistry::get('GasGroups');
                        $gasGroups = $gasGroupsTable->findMyLists($this->_user, $this->_organization->id, $this->_user->id);
                        if($gasGroups->count()==0) {
                            $this->Flash->error(__('msg_not_gas_groups'), ['escape' => false]);
                            return $this->redirect(['controller' => 'GasGroups', 'action' => 'index']);
                        }    
                    }
                break;
                case Configure::read('Order.type.des'):
                case Configure::read('Order.type.des_titolare'):
                    if($this->_organization->paramsConfig['hasDes']=='N' || 
                       !$this->_user->acl['isDes']) { 
                        $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
                        return $this->redirect(Configure::read('routes_msg_stop'));
                    } 
                break;
            }
        }
        else {
            $this->Flash->error(__('msg_error_param_order_type_id'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    // gestisco solo gruppi
    public function index($order_type_id=0)
    {
        $where = ['Orders.organization_id' => $this->_organization->id,
                 'Orders.order_type_id' => $order_type_id];
        $this->paginate = [
            'order' => ['Deliveries.data', 'Orders.data_inizio'],            
            'contain' => ['SuppliersOrganizations' => ['Suppliers'], 
                'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries'],
            'conditions' => $where
        ];

        // debug($where);
        $orders = $this->paginate($this->Orders);

        $this->set(compact('orders', 'order_type_id'));
    }

    public function view($id = null)
    {
        exit;
        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $order = $this->Orders->get($id, [
            'contain' => ['Organizations', 'SuppliersOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries', 'ProdGasPromotions', 'DesOrders'],
        ]);

        $this->set('order', $order);
    }

    /*
    $parent_id = des_order_id / prod_gas_promotion_id / order_id (gas_groups)
    $order_type_id = Configure::read('Order.type.pact_pre'); ;
    $order_type_id = Configure::read('Order.type.pact'); ;
    $order_type_id = Configure::read('Order.type.gas');
    $order_type_id = Configure::read('Order.type.promotion');
    $order_type_id = Configure::read('Order.type.gas_groups');
    */
    public function add($order_type_id=0, $parent_id=0)
    {           
        $this->_ordersTable = $this->Orders->factory($this->_user, $this->_organization->id, $order_type_id);
        $this->_ordersTable->addBehavior('Orders');
        
        $datas = $this->_getData($order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent'];
        if($order_type_id==Configure::read('Order.type.gas_groups') && 
            empty($parent)) {
            $this->Flash->error(__('msg_error_param_parent_order_id'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop')); 
        }

        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));

        $order = $this->_ordersTable->newEntity(); 
        if ($this->request->is('post')) {
            $request = $this->request->getData();
            $request['order_type_id'] = $order_type_id; 
            $request['parent_id'] = $parent_id;
            // debug($request);
            $order = $this->_ordersTable->patchEntity($order, $request);
            // debug($order);
            if ($this->_ordersTable->save($order)) {

                $this->_ordersTable->afterSaveWithRequest($this->_user, $this->_organization->id, $request);
                $this->Flash->success(__('The {0} has been saved.', __('Order')));

                $url = ['controller' => 'ArticlesOrders', 'action' => 'index', $order->order_type_id, $order->id]; 
                return $this->redirect($url);                
            }
            else
                $this->Flash->error($order->getErrors());
        } // end post

        $this->set(compact('order'));

        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('msg_not_order_not_supplier_organizations'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }
        if(empty($deliveries)) {
            $this->Flash->error(__('msg_not_order_not_deliveries'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($order_type_id, $id = null, $parent_id=0)
    {            
        $this->_ordersTable = $this->Orders->factory($this->_user, $this->_organization->id, $order_type_id);
        $this->_ordersTable->addBehavior('Orders');
        
        $datas = $this->_getData($order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent']; 
        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));

        $order = $this->_ordersTable->get([$this->_organization->id, $id], [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $request = $this->request->getData();       
            // debug($request);
            $order = $this->_ordersTable->patchEntity($order, $request);
            // debug($order);

            if ($this->_ordersTable->save($order)) {

                $this->_ordersTable->afterSaveWithRequest($this->_user, $this->_organization->id, $request);
                $this->Flash->success(__('The {0} has been saved.', 'Order'));

                /*
                 * redirect home ordine
                 */
                if($order_type_id==Configure::read('Order.type.gas_parent_groups') || 
                   $order_type_id==Configure::read('Order.type.gas_groups')) {
                    $url = ['controller' => 'Orders', 'action' => 'index']; 
                }
                else 
                    $url = ['controller' => 'joomla25Salts', 'action' => 'index', 
                        '?'=> ['scope' => 'BO', 'c_to' => 'Orders', 'a_to' => 'home', 
                               'delivery_id' => $order->delivery_id, 'order_id' => $order->id]
                        ];

                return $this->redirect($url);                
            }
            else
                $this->Flash->error($order->getErrors());
        } // end post

        $this->set(compact('order'));

        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('msg_not_order_not_supplier_organizations'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }
        if(empty($deliveries)) {
            $this->Flash->error(__('msg_not_order_not_deliveries'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }        
    }

    /* 
     * return 
     *  suppliersOrganizations produttori
     *  deliveries consegne
     *  deliveryOptions gruppo GAS che ha le proprie consegne
     *  parent ordine padre del gruppo GAS / promozione / ordine DES del titolare
     */
    private function _getData($order_type_id, $parent_id) {

        $results = [];
        $results['suppliersOrganizations'] = [];
        $results['deliveries'] = [];
        $results['deliveryOptions'] = [];
        $results['parent'] = [];
        
        $suppliersOrganizations = [];
        $deliveries = [];
        $deliveryOptions = [];
        $parent = [];        
        switch ($order_type_id) {
            case Configure::read('Order.type.gas'):
            case Configure::read('Order.type.gas_parent_groups'):
                $suppliersOrganizations = $this->_ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
                $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

                $deliveries = $this->_ordersTable->getDeliveries($this->_user, $this->_organization->id); 
            break;                
            case Configure::read('Order.type.promotion'):
                $this->_ordersTable->addBehavior('OrderPromotions');
                $prod_gas_promotion_id = $parent_id;
                $parent = $this->_ordersTable->getParent($this->_user, $this->_organization->id, $prod_gas_promotion_id);
                if(!empty($parent)) {
                    $where = ['SuppliersOrganizations.supplier_id' => $parent->suppliersOrganization->organizations->supplier_id];
                    $suppliersOrganizations = $this->_ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $where);    
                    $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
                    // debug($suppliersOrganizations);
                }
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
                $des_order_id = $parent_id;
                $parent = $this->_ordersTable->getParent($this->_user, $this->_organization->id, $des_order_id);
           
                if(!empty($parent) && isset($parent->des_supplier->supplier_id)) {
                    
                    $suppliersTable = TableRegistry::get('Suppliers');
                    $where = [];
                    $where['SuppliersOrganizations'] = ['organization_id' => $this->_organization->id];
                    $supplier = $suppliersTable->getById($this->_user, $parent->des_supplier->supplier_id, $where);
                    
                    $where = [];
                    $where['SuppliersOrganizations.id'] = $supplier->suppliers_organizations[0]->id; 
                    $suppliersOrganizations = $this->_ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id, $where);
                    $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

                    $deliveries = $this->_ordersTable->getDeliveries($this->_user, $this->_organization->id);    
                }
                break;
            case Configure::read('Order.type.gas_groups'):
                $order_id = $parent_id; // ordine 
                $parent = $this->_ordersTable->getParent($this->_user, $this->_organization->id, $order_id);
                if(!empty($parent)) {
                    $where['supplier_organization_id'] = $parent->supplier_organization_id; 
                    $suppliersOrganizations = $this->_ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id, $where);
                    $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

                    $gasGroupsTable = TableRegistry::get('GasGroups');
                    $deliveryOptions = $gasGroupsTable->findMyLists($this->_user, $this->_organization->id, $this->_user->id);
    
                    $gas_group_id = 1;
                    $deliveries = $this->_ordersTable->getDeliveries($this->_user, $this->_organization->id, $where=['gas_group_id' => $gas_group_id]);    
                }
            break;
        }

        $results['suppliersOrganizations'] = $suppliersOrganizations;
        $results['deliveries'] = $deliveries;
        $results['deliveryOptions'] = $deliveryOptions;
        $results['parent'] = $parent;

        return $results;
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Order'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
