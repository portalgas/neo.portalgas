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
    private $_order_type_id = 0;
    private $_user = null;
    private $_organization = null; // gas scelto
    private $_ordersTable = null;    // istanza del model Orders / OrderGasGroups ...
   
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Delivery');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('PriceType');

        /* 
         * gestisco solo sotto gruppi
         */
        $this->_order_type_id = Configure::read('Order.type.gas_groups');
        $this->_user = $this->Authentication->getIdentity();
        if(!isset($this->_user->acl)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }            
        $this->_organization = $this->_user->organization; // gas scelto
        // debug($this->_user);
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        /* per ora solo i sotto-gruppi
        || (
        !$user->acl['isSuperReferente'] && 
        !$user->acl['isReferentGeneric'])) 
        */ 
        if($this->_organization->paramsConfig['hasGasGroups']=='N' || !$this->_user->acl['isGasGropusManagerOrders']) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function form() {
        $order = new OrderForm($this->Authentication->getIdentity());
        if ($this->request->is('post')) {

            $isValid = $order->validate($this->request->getData());
            if(!$isValid) {
                $errors = $order->getErrors();
                debug($errors);
            }
            if ($order->execute($this->request->getData())) {
                $this->Flash->success('We will get back to you soon.');
            } else {
                $this->Flash->error('There was a problem submitting your form.');
            }
        }

        if ($this->request->is('get')) {
            $suppliersOrganizationTable = TableRegistry::get('SuppliersOrganizations');

            $supplier_organizations = $suppliersOrganizationTable->gets($this->Authentication->getIdentity());
            $supplier_organizations = $this->Orders->SuppliersOrganizations->find('list', ['limit' => 200]);        
            $this->set('supplier_organizations', $supplier_organizations);

            $order->setData([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]);

        }

        $this->set('order', $order);
    }

    public function index($order_type_id=0)
    {
        // gestisco solo sotto-gruppi
        $order_type_id = $this->_order_type_id;

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
        // gestisco solo sotto-gruppi
        $order_type_id = $this->_order_type_id;
                
        $this->_ordersTable = $this->Orders->factory($this->_user, $this->_organization->id, $order_type_id);
        $this->_ordersTable->addBehavior('Orders');
        
        $datas = $this->_getData($order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent']; 
        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));

        $order = $this->_ordersTable->newEntity(); 
        if ($this->request->is('post')) {
            $request = $this->request->getData();
            $request['order_type_id'] = $order_type_id;
            $request['state_code'] = 'CREATE-INCOMPLETE';
            $request['hasTrasport'] = 'N';
            $request['hasCostMore'] = 'N';
            $request['hasCostLess'] = 'N';            
            // debug($request);
            $order = $this->_ordersTable->patchEntity($order, $request);
            // debug($order);

            if ($this->_ordersTable->save($order)) {

                $this->_ordersTable->afterSaveWithRequest($this->_user, $this->_organization->id, $request);
                $this->Flash->success(__('The {0} has been saved.', 'Order'));

                /*
                 * redirect home ordine
                 */
                if($order_type_id==Configure::read('Order.type.gas_groups')) {
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
    }

    /**
     * Edit method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $order_type_id=0, $parent_id=0)
    {            
        // gestisco solo sotto-gruppi
        $order_type_id = $this->_order_type_id;
                
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
                if($order_type_id==Configure::read('Order.type.gas_groups')) {
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
                break;
            case Configure::read('Order.type.gas_groups'):
                $order_id = $parent_id; // ordine 
                if(!empty($order_id)) {
                    $parent = $this->_ordersTable->getParent($this->_user, $this->_organization->id, $order_id);
                }

                $suppliersOrganizations = $this->_ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
                $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

                $gasGroupsTable = TableRegistry::get('GasGroups');
                $deliveryOptions = $gasGroupsTable->findMyLists($this->_user, $this->_organization->id, $this->_user->id);

                $gas_group_id = 1;
                $deliveries = $this->_ordersTable->getDeliveries($this->_user, $this->_organization->id, $where=['gas_group_id' => $gas_group_id]);
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
