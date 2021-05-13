<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class GdxpExportsController extends AppController
{
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        // $this->viewBuilder()->setClassName('Xml'); 
        $this->viewBuilder()->setClassName('Json'); 
        // $this->viewBuilder()->setOption('serialize', true);
    }

    /* 
     * esportazione in formato GDXP degli articoli di un produttore
     * https://github.com/madbob/GDXP/tree/master/v1
     * 
     * GdxpSupplierBehavior
     * GdxpArticlesBehavior 
     */
    public function articles($supplier_organization_id) {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(/* !$user->acl['isRoot'] || */ $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $gdxp = $this->_getHeader(); 

        $subject = ['subject' => $this->_getOrganization($organization_id)];

        $blocks =[]; 
        $supplier = $this->_getSupplier($user, $organization_id, $supplier_organization_id);
        if(!empty($supplier)) {
            $blocks[0]['supplier'] = $supplier;
            $blocks[0]['supplier']['products'] = $this->_getArticlesBySupplierOrganizationId($user, $organization_id, $supplier_organization_id); 

            $supplier_name = $blocks[0]['supplier']['name'];
        } 
        else {
            $supplier_name = 'Produttore non trovato';
        } // end if(!empty($supplier))

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set($subject);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);
        
        /*
         * commentare per visualizzarlo a video
         * Set Force Download https://book.cakephp.org/3/en/views/json-and-xml-views.html#example-usage
         */
        $supplier_name = $this->setFileName($supplier_name);
        $file_name = Configure::read('Gdxp.file.prefix').$supplier_name.'-'.date('YmdHis').'.json';

        // Prior to 3.4.0
        if(!$debug) return $this->response->download($file_name);
        
        // return $this->response->withDownload($file_name);       
    } 

    /* 
     * esportazione in formato GDXP di un ordine
     * https://github.com/madbob/GDXP/tree/master/v1
     *
     * GdxpSupplierBehavior
     * GdxpArticleOrdersBehavior
     */
    public function order($order_type_id, $order_id, $parent_id=0) {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(/* !$user->acl['isRoot'] || */ $user->organization->paramsConfig['hasOrdersGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $gdxp = $this->_getHeader(); 

        $subject = ['subject' => $this->_getOrganization($this->Authentication->getIdentity()->organization)];

        $results = [];

        $orderResults = $this->_getOrder($user, $organization_id, $order_type_id, $order_id, $debug);

        $supplier_name = $orderResults->suppliers_organization->name;
        $supplier_organization_id = $orderResults->suppliers_organization->id;
        // debug($supplier_name);

        $article_orders = $this->_getArticlesByOrderId($user, $organization_id, $orderResults, $debug); 
        // debug($article_orders);

        $blocks =[];  
        $supplier = $this->_getSupplier($user, $organization_id, $supplier_organization_id);
        if(!empty($supplier)) {
            $blocks[0]['supplier'] = $supplier;
            $blocks[0]['supplier']['products'] = $article_orders; 
            $blocks[0]['supplier']['orderInfo'] = $this->_getOrderInfo($orderResults); 

            $supplier_name = $blocks[0]['supplier']['name'];
        } 
        else {
            $supplier_name = 'Produttore non trovato';
        } // end if(!empty($supplier))

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set($subject);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);
        
        /*
         * commentare per visualizzarlo a video
         * Set Force Download https://book.cakephp.org/3/en/views/json-and-xml-views.html#example-usage
         */
        $supplier_name = $this->setFileName($supplier_name);
        $delivery_data = $this->setFileName($this->getDeliveryDate($orderResults->delivery));
        $file_name = Configure::read('Gdxp.file.prefix').$supplier_name.'-'.$delivery_data.'-'.date('YmdHis').'.json';

        // Prior to 3.4.0
        if(!$debug) return $this->response->download($file_name);
        
        // return $this->response->withDownload($file_name); 
    }

    private function _getHeader() {

        return [
            'protocolVersion' => Configure::read('Gdxp.protocolVersion'),
            'creationDate' => date('Y-m-d'),
            'applicationSignature' => Configure::read('Gdxp.applicationSignature')
        ];
    } 

    private function _getOrganization($organization) {

        $results = [];
        $results['name'] = $organization->name;
        $results['taxCode'] = $organization->cf;
        $results['vatNumber'] = $organization->piva;
        $results['address']['street'] = $organization->indirizzo;
        $results['address']['locality'] = $organization->localita;
        $results['address']['zipCode'] = $organization->cap;
        $results['contacts'] = [];

        $contacts = [];
        $contacts['type'] = 'emailAddress';
        $contacts['value'] = $organization->mail;
        $results['contacts'][0] = $contacts;

        return $results;
    }   

    /*
     * Suppliers
     */
    private function _getSupplier($user, $organization_id, $supplier_organization_id) {

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');        
        $results = [];
        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
            'SuppliersOrganizations.id' => $supplier_organization_id,
            ];
        // debug($where);    

        $suppliersOrganizationsResults = $suppliersOrganizationsTable->find('all', [
                        'conditions' => $where,
                        'fields' => ['supplier_id']
                        /*
                        'contain' => ['OwnerOrganizations', 'OwnerSupplierOrganizations']
                        */
                        ])
                        ->first();
        // debug($suppliersOrganizationsResults);   
        if(!empty($suppliersOrganizationsResults)) {

            $supplier_id = $suppliersOrganizationsResults->supplier_id; 
            // debug($suppliersOrganizationsResults->supplier_id); 

            $suppliersTable = TableRegistry::get('Suppliers');
            $suppliersTable->addBehavior('GdxpSuppliers');

            $where = ['Suppliers.id' => $supplier_id,
                ];
            // debug($where);    

            $results = $suppliersTable->find('all', ['conditions' => $where])->first();
            // debug($results);
        } // end if(!empty($suppliersOrganizationsResults))            

        return $results;
    }

    private function _getOrder($user, $organization_id, $order_type_id, $order_id, $debug=false) {

        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');

        switch ($order_type_id) {
            case Configure::read('Order.type.promotion'):
                 $ordersTable->addBehavior('OrderPromotions');
                // $prod_gas_promotion_id = $parent_id;
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
               // $des_order_id = $parent_id;
                break;
        }

        // debug($ordersTable);
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);
        // debug($orderResults);

        return $orderResults;
    }

    private function _getOrderInfo($orderResults) {
        
        $results = [];
        $results = [
                "phase" => "booking",
                "openDate" => $orderResults->data_inizio->i18nFormat('Y-MM-dd'),
                "closeDate" => $orderResults->data_fine->i18nFormat('Y-MM-dd'),
                "deliveryDate" => $this->getDeliveryDate($orderResults->delivery)
            ];

        return $results; 
    }

    /*
     * Articles
     */
    private function _getArticlesBySupplierOrganizationId($user, $organization_id, $supplier_organization_id) { 

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $suppliersOrganizationsTable->Articles->addBehavior('GdxpArticles');

        $where = ['Articles.organization_id' => $organization_id,
                  'Articles.supplier_organization_id' => $supplier_organization_id,
                  'Articles.stato' => 'Y'
            ];
        // debug($where);    

        $articleResults = $suppliersOrganizationsTable->Articles->find('all', [
                        'conditions' => $where,
                        'order' => ['Articles.name asc'],
                        // 'limit' => 2,
                        'contain' => ['CategoriesArticles']
                        ]);
        // debug($articleResults);

        return $articleResults;
    }  

    /*
     * non posso prendere il totale della qta acquistata da ArticlesOrders.qta_cart perche' se ordine DES e' la somma di tutti i GAS
     * sommo qta / qta_forzato da Carts
     */
    private function _getArticlesByOrderId($user, $organization_id, $orderResults) {

        $results = [];

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        

        /*
         * articoli associati all'ordine 
         */
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);
        $articlesOrdersTable->addBehavior('GdxpArticleOrders');

        if($articlesOrdersTable!==false) {

            $where = [];
            $where['ArticlesOrders'] = [$articlesOrdersTable->getAlias().'.organization_id' => $orderResults->organization_id,
                                        $articlesOrdersTable->getAlias().'.order_id' => $orderResults->id,
                                        /*
                                         * prendo solo articoli acquistati
                                         */
                                        $articlesOrdersTable->getAlias().'.qta_cart > ' => 0
                                    ]; 

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');
            $results = $articlesOrdersTable->gets($user, $organization_id, $orderResults, $where, $options=[]);
        }

        // debug($results);
        return $results;
    } 
}