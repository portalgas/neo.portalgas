<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

use App\Model\Entity\OrderGas;
use App\Model\Entity\OrderDes;
use App\Model\Entity\OrderPact;
use App\Model\Entity\OrderPromotion;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('PriceType');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot']) {
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
            $order->setData([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com'
            ]);

            $suppliersOrganizationTable = TableRegistry::get('SuppliersOrganizations');

            $supplier_organizations = $suppliersOrganizationTable->gets($this->Authentication->getIdentity());
            $supplier_organizations = $this->Orders->SuppliersOrganizations->find('list', ['limit' => 200]);        
            $this->set('supplier_organizations', $supplier_organizations);
        }

        $this->set('order', $order);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SuppliersOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries'
            /* , 'ProdGasPromotions', 'DesOrders' */
            ],
        ];
        $orders = $this->paginate($this->Orders);

        $this->set(compact('orders'));
    }

    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Organizations', 'SuppliersOrganizations', 'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries', 'ProdGasPromotions', 'DesOrders'],
        ]);

        $this->set('order', $order);
    }

    public function test()
    { 
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
    $parent_id = des_order_id / prod_gas_promotion_id
    $order_type_id = Configure::read('Order.type.pact_pre'); ;
    $order_type_id = Configure::read('Order.type.pact'); ;
    $order_type_id = Configure::read('Order.type.gas');
    $order_type_id = Configure::read('Order.type.promotion');
    */
    public function add($order_type_id=1, $parent_id=0)
    { 
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        // debug($order_type_id);
        switch ($order_type_id) {
            case Configure::read('Order.type.promotion'):
                $prod_gas_promotion_id = $parent_id;
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
                $des_order_id = $parent_id;
                break;
        }

        
        $ordersTable = $this->Orders->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');

        switch ($order_type_id) {
            case Configure::read('Order.type.promotion'):
                 $ordersTable->addBehavior('OrderPromotions');
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
                break;
        }

        // debug($ordersTable);

        $order = $ordersTable->newEntity();
        
        if ($this->request->is('post')) {
            $request = $this->request->getData();  
            $order = $ordersTable->patchEntity($order, $request);
             debug($order); exit;
            if ($ordersTable->save($order)) {
                $this->Flash->success(__('The {0} has been saved.', 'Order'));

                // return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($order->getErrors());
        }

        /*
         * dati promozione / order des
         */
        $parent = $ordersTable->getParent($user, $organization_id, $parent_id);

        $where = ['SuppliersOrganizations.supplier_id' => $parent->suppliersOrganization->organizations->supplier_id];
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($user, $organization_id, $where);
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $suppliersOrganizations);
        // debug($suppliersOrganizations);
        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('no suppliersOrganizations'));
        }
        else {

        }
        
        // $organizations = $ordersTable->Organizations->find('list', ['limit' => 200]);
        // $suppliersOrganizations = $ordersTable->SuppliersOrganizations->find('list', ['limit' => 200]);
        // $ownerOrganizations = $ordersTable->OwnerOrganizations->find('list', ['limit' => 200]);
        // $ownerSupplierOrganizations = $ordersTable->OwnerSupplierOrganizations->find('list', ['limit' => 200]);
        
        /* 
         * id => id_organization;id_delivery 
         * $deliveries = $this->Orders->Deliveries->find('list', ['limit' => 200]);
         * perche' doppia key
         */ 
        $where = ['ProdGasPromotionsOrganizationsDeliveries.prod_gas_promotion_id' => $prod_gas_promotion_id];
        $deliveries = $ordersTable->getDeliveries($user, $organization_id, $where);

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
