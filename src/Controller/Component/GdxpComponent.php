<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class GdxpComponent extends Component {

	private $controller = '';
	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));
	}

	public function exportArticles($user, $organization_id, $supplier_organization_id, $debug=false) {
		
		$results = [];

        $results['gdxp'] = $this->_getHeader(); 

        $results['subject'] = ['subject' => $this->_getOrganization($user->organization)];
        $results['blocks'] =[]; 
        $results['supplier_name'] = '';
        $results['supplier'] = $this->_getSupplier($user, $organization_id, $supplier_organization_id);
        if(!empty($results['supplier'])) {
            $results['blocks'][0]['supplier'] = $results['supplier'];
            $results['blocks'][0]['supplier']['products'] = $this->_getArticlesBySupplierOrganizationId($user, $organization_id, $supplier_organization_id); 

            $results['supplier_name'] = $results['blocks'][0]['supplier']['name'];
        } 
        else {
            $results['supplier_name'] = 'Produttore non trovato';
        } // end if(!empty($results['supplier'])) 

        return $results;
	}

    public function exportOrder($user, $organization_id, $supplier_organization_id, $order_type_id, $order_id, $debug=false) {

		$results = [];

        $results['gdxp'] = $this->_getHeader(); 

        $results['subject'] = ['subject' => $this->_getOrganization($user->organization)];
        $results['blocks'] = []; 
        $results['supplier_name'] = '';
        $results['supplier'] = []; 

        $orderResults = $this->_getOrder($user, $organization_id, $order_type_id, $order_id, $debug);
        if(!empty($orderResults)) {

            $supplier_name = $orderResults->suppliers_organization->name;
            $supplier_organization_id = $orderResults->suppliers_organization->id;
            // debug($supplier_name);

            $article_orders = $this->_getArticlesByOrderId($user, $organization_id, $orderResults, $debug); 
            // debug($article_orders);

            $results['supplier'] = $this->_getSupplier($user, $organization_id, $supplier_organization_id);
            if(!empty($results['supplier'])) {
                $results['blocks'][0]['supplier'] = $results['supplier'];
                $results['blocks'][0]['supplier']['products'] = $article_orders; 
                $results['blocks'][0]['supplier']['orderInfo'] = $this->_getOrderInfo($orderResults); 

                $results['supplier_name'] = $results['blocks'][0]['supplier']['name'];
            } 
            else {
                $results['supplier_name'] = 'Produttore non trovato';
            } // end if(!empty($supplier))
        }

        return $results;
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