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
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();
        $organization = $user->organization; // gas scelto
      
        if(!isset($user->acl) ||
            /* per ora solo i sotto-gruppi
            || (
            !$user->acl['isSuperReferente'] && 
            !$user->acl['isReferentGeneric'])) 
            */ 
            !isset($organization->paramsConfig['hasGasGroups']) || 
            $organization->paramsConfig['hasGasGroups']=='N' || 
             !$user->acl['isGasGropusManagerOrders']
            ) { 
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

        $user = $this->Authentication->getIdentity();
        $organization = $user->organization; // gas scelto

        $where = ['Orders.organization_id' => $organization->id,
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

    public function test()
    { 
        exit;
        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $order_type_id = Configure::read('Order.type.promotion');
        $order_type_id = Configure::read('Order.type.pact-pre'); ;
        $order_type_id = Configure::read('Order.type.pact'); ;
        $order_type_id = Configure::read('Order.type.gas');
        debug($order_type_id);
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');
        // debug($ordersTable);
        $order = $ordersTable->newEntity();
                
        /*
         * se il form ha degli errori di validazione, recupero i dati della priceTypes e creo una variabile js che vue con il metodo getRows() recupera
         */ 
        $json_price_types = '{}';
        
        if ($this->request->is('post')) {   
            
            //debug($this->request->getData());  
            $order = $ordersTable->patchEntity($order, $this->request->getData());
            if (!$ordersTable->save($order)) {

                /*
                 * se in errore recupero i valori dei priceType inseriti dall'utente
                 */ 
                $json_price_types = $this->PriceType->jsonToRequest($user, $this->request->getData());     
                // debug($json_price_types);  
                // debug($order); 
                $this->Flash->error($order->getErrors());
            }
            else {
                $order_id = $order->id;
                $this->Flash->success(__('The {0} has been saved.', 'Order'));
                return $this->redirect(['controller' => 'ArticlesOrders', 'action' => 'add', $order_id]);
            }
        }
        
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($user, $organization_id);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $suppliersOrganizations);
        // debug($suppliersOrganizations);
        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('no suppliersOrganizations'));
        }
        else {

        }
        $deliveries = $ordersTable->getDeliveries($user, $organization_id);

        $priceTypesTable = TableRegistry::get('PriceTypes');
        $this->set('price_type_enums', $priceTypesTable->enum('type'));

        $this->set(compact('order_type_id', 'order', 'suppliersOrganizations', 'deliveries', 'json_price_types'));
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
        
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        // debug($order_type_id);
        
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);
        $ordersTable->addBehavior('Orders');

        /*
         *
         * */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $deliveriesTable = TableRegistry::get('Deliveries');
        
        $suppliersOrganizations = [];
        $deliveries = [];
        /* 
         * oggetto padre (ex ordine DES del titolare)
         * ordine del gas-group-ordine
         */
        $parent = null; 
        switch ($order_type_id) {
            case Configure::read('Order.type.gas'):
                $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($user, $organization_id, $user->id);                    
                $deliveries = $deliveriesTable->getsActiveList($user, $organization_id);
            break;                
            case Configure::read('Order.type.promotion'):
                $ordersTable->addBehavior('OrderPromotions');
                $prod_gas_promotion_id = $parent_id;
                $parent = $ordersTable->getParent($user, $organization_id, $prod_gas_promotion_id);
                if(!empty($parent)) {
                   $where = ['SuppliersOrganizations.supplier_id' => $parent->suppliersOrganization->organizations->supplier_id];
                   $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($user, $organization_id, $where);    
                   $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $suppliersOrganizations);
                   // debug($suppliersOrganizations);
                   if(empty($suppliersOrganizations)) {
                       $this->Flash->error(__("Il produttore della promozione non è presente!"));
                   }
                }
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
                $des_order_id = $parent_id;
                break;
            case Configure::read('Order.type.gas_groups'):
                $order_id = $parent_id; // ordine 
                if(!empty($order_id)) {
                    $parent = $ordersTable->getParent($user, $organization_id, $order_id);
                    if(!empty($parent)) {
                        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $parent->suppliers_organization);
                        $deliveries = $this->Delivery->getListByResults($user, $parent->delivery);
                    }
                }
                else {
                    $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($user, $organization_id, $user->id);                    
                    
                    $gasGroupsTable = TableRegistry::get('GasGroups');
                    $gasGroups = $gasGroupsTable->findMyLists($user, $organization_id, $user->id);
                    $this->set(compact('gasGroups'));

                    $gas_group_id = 1;
                    $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
                    $deliveries = $gasGroupDeliveriesTable->getsActiveList($user, $organization_id, $gas_group_id);
                }
                break;
        }

        // debug($ordersTable);

        $order = $ordersTable->newEntity();
        
        if ($this->request->is('post')) {
            $request = $this->request->getData();
            $order = $ordersTable->patchEntity($order, $request);
            // debug($order);

            /* 
             * OrderBehavior / OrderPromotionsBehavior
             */
            if ($ordersTable->save($order)) {

                $ordersTable->afterSaveWithRequest($user, $organization_id, $request);

                $this->Flash->success(__('The {0} has been saved.', 'Order'));

                /*
                 * recirect home ordine
                 */
                $url = ['controller' => 'joomla25Salts', 'action' => 'index', 
                        '?'=> ['scope' => 'BO', 'c_to' => 'Orders', 'a_to' => 'home', 
                               'delivery_id' => $order->delivery_id, 'order_id' => $order->id]
                        ];

                return $this->redirect($url);                
            }
            else
                $this->Flash->error($order->getErrors());
        }

        // $organizations = $ordersTable->Organizations->find('list', ['limit' => 200]);
        // $suppliersOrganizations = $ordersTable->SuppliersOrganizations->find('list', ['limit' => 200]);
        // $ownerOrganizations = $ordersTable->OwnerOrganizations->find('list', ['limit' => 200]);
        // $ownerSupplierOrganizations = $ordersTable->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        
        /* 
         * id => id_organization;id_delivery 
         * $deliveries = $this->Orders->Deliveries->find('list', ['limit' => 200]);
         * perche' doppia key
        $where = ['ProdGasPromotionsOrganizationsDeliveries.prod_gas_promotion_id' => $prod_gas_promotion_id];
        $deliveries = $ordersTable->getDeliveries($user, $organization_id, $where);
        if(empty($deliveries)) {
            $this->Flash->error(__("Il Gas non ha consegne disponibili!"));
        }
         */ 

        $this->set(compact('order_type_id', 'order', 'parent', 'suppliersOrganizations', 'deliveries'));
    }

    /**
     * Edit method
     *
     * @param string|null $id K Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // gestisco solo sotto-gruppi
        $order_type_id = $this->_order_type_id;

        $order = $this->Orders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The {0} has been saved.', 'Order'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Order'));
        }
        $organizations = $this->Orders->Organizations->find('list', ['limit' => 200]);
        $suppliersOrganizations = $this->Orders->SuppliersOrganizations->find('list', ['limit' => 200]);
        $ownerOrganizations = $this->Orders->OwnerOrganizations->find('list', ['limit' => 200]);
        $ownersSupplierOrganizations = $this->Orders->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        $deliveries = $this->Orders->Deliveries->find('list', ['limit' => 200]);
        $prodGasPromotions = []; // $this->Orders->ProdGasPromotions->find('list', ['limit' => 200]);
        $desOrders = []; // $this->Orders->DesOrders->find('list', ['limit' => 200]);
        $this->set(compact('order', 'organizations', 'suppliersOrganizations', 'ownerOrganizations', 'ownerSupplierOrganizations', 'deliveries', 'prodGasPromotions', 'desOrders'));
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
