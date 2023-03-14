<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Decorator\OrderDecorator;
use Cake\Log\Log;
/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    private $_ordersTable = null;    // istanza del model Orders / OrderGasGroups ...
   
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Delivery');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('PriceType');
        $this->loadComponent('ActionsOrder');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $results = $this->Auths->ctrlRoute($this->_user, $this->request);
        if(!empty($results)) {
            $this->Flash->error($results['msg'], ['escape' => false]);
            if(!isset($results['redirect'])) 
                $results['redirect'] = Configure::read('routes_msg_stop');
            Log::error($this->request->params['controller'].'->'.$this->request->params['action'].' '.__('routes_msg_stop'));    
            return $this->redirect($results['redirect']);
        }
    }

    public function index($order_type_id=0)
    {
        $where = [];
        $sorts = ['Deliveries.data asc'];
                   
        /* 
         * filters
         */
        $request = $this->request->getQuery();
        $search_supplier_organization_id = '';
        $order_delivery_date = '';

        if(!empty($request['search_supplier_organization_id'])) {
            $search_supplier_organization_id = $request['search_supplier_organization_id'];
            $where += ['Orders.supplier_organization_id' => $search_supplier_organization_id];
        } 

        // order_delivery_date = 'Deliveries.data desc
        if(!empty($request['order_delivery_date'])) {
            // debug('order_delivery_date '.$request['order_delivery_date']);
            $order_delivery_date = $request['order_delivery_date'];
            list($field, $sort) = explode(' ', $order_delivery_date);
            ($sort=='asc') ? $sort = 'desc': $sort = 'asc';
            foreach($sorts as $key => $value) {
                // debug($value.' = '.$field.' '.$sort);
                if(strtolower($value)==strtolower($field.' '.$sort))
                    unset($sorts[$key]);
            }
            if(!in_array($request['order_delivery_date'], $sorts))
                array_push($sorts, $request['order_delivery_date']);
        } 
        $this->set(compact('search_supplier_organization_id', 'order_delivery_date'));

        $where += ['Orders.organization_id' => $this->_organization->id,
                    'Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.isVisibleBackOffice' => 'Y',
                    'Deliveries.stato_elaborazione' => 'OPEN',
                    'SuppliersOrganizations.stato' => 'Y'];
        if(!empty($order_type_id)) {
            $where += ['Orders.order_type_id' => $order_type_id];
            if($order_type_id==Configure::read('Order.type.gas_groups')) {
                // ctrl che l'utente appartertenga al gruppo 
                $gasGroupsTable = TableRegistry::get('GasGroups');
                $gasGroups = $gasGroupsTable->findMyLists($this->_user, $this->_organization->id, $this->_user->id);
                if(empty($gasGroups))
                    $where += ['Orders.gas_group_id' => '-1']; // utente non associato in alcun gruppo 
                else 
                    $where += ['Orders.gas_group_id IN ' => array_keys($gasGroups)];
            }
        }
        /* 
         * profilazione $user->acl['isReferentGeneric'] 
         */
        if(!$this->_user->acl['isSuperReferente'] && $this->_user->acl['isReferentGeneric']) { 
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgetsList($this->_user, $this->_organization->id, $this->_user->id);
            debug($suppliersOrganizations);
            if(empty($suppliersOrganizations))
                $where += ['SuppliersOrganizations.id' => '-1']; // utente senza referenze
            else
                $where += ['SuppliersOrganizations.id IN ' => array_keys($suppliersOrganizations)];
        }

        // debug($where);
        array_push($sorts, 'Orders.data_inizio asc');
        // debug($sorts);

        $this->paginate = [
            'order' => $sorts,            
            'contain' => ['OrderTypes', 'SuppliersOrganizations' => ['Suppliers'], 
                'OwnerOrganizations', 'OwnerSupplierOrganizations', 'Deliveries'],
            'conditions' => $where,
            'limit' => 75
        ];

        // debug($where);
        $orders = new OrderDecorator($this->_user, $this->paginate($this->Orders));
        $orders = $orders->results;

        // debug($orders);
        if(empty($order_type_id)) $order_type_id  = Configure::read('Order.type.gas');
        $this->set(compact('orders', 'order_type_id'));

        /* 
         * filters
         */
        $suppliersOrganizations = $this->Orders->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
        $order_delivery_dates = ['Deliveries.data asc' => 'Data di consegna ascendente',
                                 'Deliveries.data desc' => 'Data di consegna discendente'];

        $this->set(compact('suppliersOrganizations', 'order_delivery_dates'));

		/*
		 * legenda profilata
		 */
		$group_id = $this->ActionsOrder->getGroupIdToReferente($this->_user);
		$orderStatesToLegenda = $this->ActionsOrder->getOrderStatesToLegenda($this->_user, $group_id);
		$this->set('orderStatesToLegenda', $orderStatesToLegenda);        
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
        
        $order = $this->_ordersTable->newEntity();

        $datas = $this->_getData($order, $order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent'];
        if($order_type_id==Configure::read('Order.type.gas_groups') && 
            empty($parent)) {
            $this->Flash->error(__('msg_error_param_parent_order_id'), ['escape' => false]);
            Log::error($this->request->params['controller'].'->'.$this->request->params['action'].' '.__('msg_error_param_parent_order_id'));
            return $this->redirect(Configure::read('routes_msg_stop')); 
        }

        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));
         
        if ($this->request->is('post')) {
            $request = $this->request->getData();
            $request['order_type_id'] = $order_type_id; 
            $request['parent_id'] = $parent_id;
            // debug($request);
            $order = $this->_ordersTable->patchEntity($order, $request);
            // debug($order);
            if ($this->_ordersTable->save($order)) {

                $afterAddWithRequest = $this->_ordersTable->afterAddWithRequest($this->_user, $this->_organization->id, $order, $request);
                if($afterAddWithRequest!==true)
                    $this->Flash->error($afterAddWithRequest);
                else 
                    $this->Flash->success("L'ordine è stato salvato correttamente");

                $url = ['controller' => 'ArticlesOrders', 'action' => 'index', $order->order_type_id, $order->id]; 
                return $this->redirect($url);                
            }
            else
                $this->Flash->error($order->getErrors());
        } // end post

        $this->set(compact('order'));

        /*
         * controllo variabili (produttore / consegne)
         */
        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('msg_not_order_not_supplier_organizations'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }
        if($order_type_id!=Configure::read('Order.type.gas_groups')) {
            // Configure::read('Order.type.gas_groups') carica le consegne (GasGroupDeliveries) dopo aver selezionato il gruppo (GasGroups)
            if(empty($deliveries)) {
                $this->Flash->error(__('msg_not_order_not_deliveries'), ['escape' => false]);
                return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
            }    
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
        
        $order = $this->_ordersTable->getById($this->_user, $this->_organization->id, $id);
        
        $datas = $this->_getData($order, $order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent']; 
        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $request = $this->request->getData();  
            $request['order_type_id'] = $order_type_id;
            $request['state_code'] = $order->state_code; 
            // $request['parent_id'] = $parent_id;                 
            // debug($request);
            $order = $this->_ordersTable->patchEntity($order, $request);
            // debug($order);
            if ($this->_ordersTable->save($order)) {

                // todo $this->_ordersTable->afterEditWithRequest($this->_user, $this->_organization->id, $request);
                $this->Flash->success("L'ordine è stato salvato correttamente");

                /*
                 * redirect home ordine
                 */
                if($order_type_id==Configure::read('Order.type.gas_parent_groups') || 
                   $order_type_id==Configure::read('Order.type.gas_groups')) {
                    $url = ['controller' => 'Orders', 'action' => 'index', $order_type_id]; 
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

        /*
         * controllo variabili (produttore / consegne)
         */
        if(empty($suppliersOrganizations)) {
            $this->Flash->error(__('msg_not_order_not_supplier_organizations'), ['escape' => false]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
        }
        if($order_type_id!=Configure::read('Order.type.gas_groups')) {
            // Configure::read('Order.type.gas_groups') carica le consegne (GasGroupDeliveries) dopo aver selezionato il gruppo (GasGroups)
            if(empty($deliveries)) {
                $this->Flash->error(__('msg_not_order_not_deliveries'), ['escape' => false]);
                return $this->redirect(['controller' => 'Orders', 'action' => 'index', $order_type_id]);            
            }    
        }     
    }

    public function home($order_type_id, $id = null, $parent_id=0)
    {        
        $debug = false;
            
        $this->_ordersTable = $this->Orders->factory($this->_user, $this->_organization->id, $order_type_id);
        $this->_ordersTable->addBehavior('Orders');

        $order = $this->_ordersTable->getById($this->_user, $this->_organization->id, $id);

        $datas = $this->_getData($order, $order_type_id, $parent_id);
        $suppliersOrganizations = $datas['suppliersOrganizations'];
        $deliveries = $datas['deliveries'];
        $deliveryOptions = $datas['deliveryOptions'];
        $parent = $datas['parent']; 
        $this->set(compact('order_type_id', 'parent', 'suppliersOrganizations', 'deliveries', 'deliveryOptions'));
        $this->set(compact('order'));

		$group_id = $this->ActionsOrder->getGroupIdToReferente($this->_user);	
		$orderActions = $this->ActionsOrder->getOrderActionsToMenu($this->_user, $group_id, $order, $debug);
	    
		/* 
		 * elimino l'item Order home (id=0) perche' sono già in home
		 */
		if($orderActions[0]['OrdersAction']['id']==0) 
			unset($orderActions[0]);
		$this->set('orderActions', $orderActions);
	
		/*
		 * creo degli OrderActions di raggruppamento per controller (Orders, Carts, etc)
		*/
		$raggruppamentoOrderActions = [];
		/*
		 * bugs, per questi stati non raggruppo perche' ho 2 OrderController con conseguenziali
		 */
		if($order->state_code!='PROCESSED-ON-DELIVERY' && $order->prod_gas_promotion_id==0)
			$raggruppamentoOrderActions = $this->ActionsOrder->getRaggruppamentoOrderActions($orderActions, $debug);
		$this->set('raggruppamentoOrderActions', $raggruppamentoOrderActions);

    }

    /* 
     * return 
     *  suppliersOrganizations produttori
     *  deliveries consegne
     *  deliveryOptions gruppo GAS che ha le proprie consegne
     *  parent ordine padre del gruppo GAS / promozione / ordine DES del titolare
     */
    private function _getData($order, $order_type_id, $parent_id, $debug=false) {

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
                    if(!empty($order) && !empty($order->gas_group_id)) { // edit
                        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
                        $deliveries = $gasGroupDeliveriesTable->getsActiveList($this->_user, $this->_organization->id, $order->gas_group_id);
                    }
                    // le consegne (GasGroupDeliveries) le carico dopo aver scelto il gruppo (GasGroups)
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
        exit;

        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success("L'ordine è stato eliminato definitivamente");
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Order'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
