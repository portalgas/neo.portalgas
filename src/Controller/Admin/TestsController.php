<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class TestsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('MappingGdxpPortalgas');
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('Sitemap');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function sitemap()
    {
        $results = $this->Sitemap->create();

        $this->set(compact('results'));
        $this->render('index');
    }

    /*
     * testing chiamata ajax
     */
    public function ajax()
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $service_urls = [];
        $service_urls['/admin/api/ProdGasSuppliers/import'] = '/admin/api/ProdGasSuppliers/import';
        // $service_urls['/admin/api/SuppliersOrganizations/import'] = '/admin/api/SuppliersOrganizations/import';

        $service_url = $this->request->getData('service_url');
        $this->set(compact('service_url'));

        if(!empty($service_url)) {

            $results = [];
        
        }  // end if(!empty($service_url)) 
            
        $value1 = 'supplier_id';            

        $this->set(compact('service_urls', 'value1'));
    }

    /*
     * testing chiamata ajax getCartsByOrder
     */
    public function ajaxCart()
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $order_type_ids = [];
        $order_type_ids[Configure::read('Order.type.gas')] = 'Order.type.gas';
        $order_type_ids[Configure::read('Order.type.des')] = 'Order.type.des';
        $order_type_ids[Configure::read('Order.type.-titolare')] = 'Order.type.-titolare'; 
        $order_type_ids[Configure::read('Order.type.promotion')] = 'Order.type.promotion';
        $order_type_ids[Configure::read('Order.type.pact-pre')] = 'Order.type.pact-pre';
        $order_type_ids[Configure::read('Order.type.pact')] = 'Order.type.pact';
        $order_type_ids[Configure::read('Order.type.supplier')] = 'Order.type.supplier';

        $order_type_id = 0;
        $order_type_id = $this->request->getData('order_type_id');
        $this->set(compact('order_type_ids', 'order_type_id'));

        if(!empty($order_type_id)) {

            $results = [];
        
            $service_urls = [];
            $service_urls['/admin/api/orders-gas/getCartsByOrder'] = '/admin/api/orders-gas/getCartsByOrder';

            $ordersTable = TableRegistry::get('Orders');
            $ordersTable = $ordersTable->factory($user, $organization_id, $order_type_id);
            $ordersTable->addBehavior('Orders');

            $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($user, $organization_id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($user, $suppliersOrganizations);

            $deliveries = $ordersTable->getDeliveries($user, $organization_id);
            
            $where = [$ordersTable->getAlias().'.state_code IN ' => ['OPEN', 'PROCESSED-BEFORE-DELIVERY']];
            $orders = $ordersTable->getsList($user, $organization_id, $where);  
        
            $this->set(compact('service_urls', 'suppliersOrganizations', 'deliveries', 'orders'));
        } // end if(!empty($order_type_id))
    }

    /*
     * simula chiamata ajax delle queue
     */
    public function queue() {
        
        $debug = true;

        $request['code'] = Configure::read('Gdxp.queue.code');
        $request['id'] = '';
		
        $queuesTable = TableRegistry::get('Queues');
        $queue = $queuesTable->findByCode($request['code']);  

        $this->loadComponent($queue->queue_mapping_type->component);

        $results = $this->{$queue->queue_mapping_type->component}->queue($request, $debug);

        $this->set(compact('results'));
        $this->render('index');
    }

    public function index()
    {
        $results = [];
       
		$organization_id = 1;
		$results = $this->MappingGdxpPortalgas->getMaxArticleId($organization_id);
		
        $this->set(compact('results'));
    }

    /*
     *  SELECT name, MATCH(name) AGAINST('azie*' 'agric*' 'biologica' IN BOOLEAN MODE) AS relevance
     FROM k_suppliers WHERE MATCH(name) AGAINST('azie*' 'agric*' 'biologica' IN BOOLEAN MODE) ORDER BY relevance DESC LIMIT 10;
     *
     */
    public function searchable() {

        $search = "'iris*'";
        $search = "'azie*' 'agric*' 'biologica*'";
 
        $suppliersTable = TableRegistry::get('Suppliers');
        $results = $suppliersTable->find() 
        ->select(['name', 'id', 
                  'relevance' => "MATCH(Suppliers.name) AGAINST(".$search." IN BOOLEAN MODE)"])
            ->where([
                "MATCH(Suppliers.name) AGAINST(:search IN BOOLEAN MODE)" 
            ])
            ->order(['relevance' => 'desc'])
            ->bind(':search', $search, 'string');

        debug('Found '.$results->count());    
        debug($search);    
        foreach($results as $result) {
            debug($result->relevance.' '.$result->name);
        }

        $this->set(compact('results'));
        $this->render('index');        
    }

    public function salt() {
        
        $debug = true;

        $salt = 'QllTdjVQc05lSEFaOHBoRGxGQkloYnRrT2MyRDZsOEs3Vk5ER3ladTBhT1VBdW1NbEpnS0VEMXdscklhcXZyaldCb0RTSm96dUJ3NFZMRm91NFI3UUdZWXp2SjlzVlFWaCtVbFR5UE5GMUUyTXBPWkpsYWhnT3BHeTY1Y3I5cUdEbHVVbUY2L1V6Tnd0MldvSmRRMnh3PT0=';
        
        $date = date('Ymd');
        debug($date);
        $results =$this->decrypt($salt, $date);
        debug($results);

        $date = date('Ymd',strtotime("-1 days"));
        debug($date);
        $results =$this->decrypt($salt, $date);
        debug($results);

        $date = date('Ymd',strtotime("+1 days"));
        debug($date);
        $results =$this->decrypt($salt, $date);
        debug($results);
        
        $this->set(compact('results'));
        $this->render('index');
    }    
}