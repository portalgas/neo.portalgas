<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use Cake\Http\Exception\NotFoundException;

class GdxpsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);    

        /*
         * $user = $this->Authentication->getIdentity();
         * if(!isset($user->acl) || !$user->acl['isSuperReferente'] || !$user->acl['isReferentGeneric']) {
         */
    }

    /*
     * list suppliers gdxp da http://www.economiasolidale.net/api/v1/list.php
     * se no lo trovo 
     * has been blocked by CORS policy: Request header field x-csrf-token is not allowed by Access-Control-Allow-Headers in preflight response.
     *  prendo quello locale
     */
    public function index()
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(!$user->acl['isRoot'] || !$user->acl['isSuperReferente'] || $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $gdxp_suppliers_index_url_remote = Configure::read('Gdxp.suppliers.index.url.remote');
        $gdxp_suppliers_index_url_local = Configure::read('Gdxp.suppliers.index.url.local');
        $gdxp_articles_index_url = Configure::read('Gdxp.articles.index.url');

        $this->set(compact('gdxp_suppliers_index_url_remote', 'gdxp_suppliers_index_url_local', 'gdxp_articles_index_url'));
    }

    public function articlesExport()
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(!$user->acl['isRoot'] || !$user->acl['isSuperReferente'] || $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $articles = [];
        $supplier_organization_id = 0;
        $acl_supplier_organizations = $this->Auths->getAclSupplierOrganizationsList($user);

        if ($this->request->is('post')) {

            $supplier_organization_id = $this->request->getData('supplier_organization_id');

            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $supplier_organization = $suppliersOrganizationsTable->get($user, ['SuppliersOrganizations.id' => $supplier_organization_id]);
            $this->set(compact('supplier_organization'));

            /*
             * ricerco chi gestisce il listino articoli del produttore del GAS
             */
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $ownArticles = $suppliersOrganizationsTable->getOwnArticles($user, $user->organization->id, $supplier_organization_id, $debug);

            $articlesTable = TableRegistry::get('Articles');

            $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                      'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                      'Articles.stato' => 'Y'];

            $articles = $articlesTable->gets($user, $where);
            // debug($articles);
        } // end if ($this->request->is('post')) {

        $this->set(compact('acl_supplier_organizations', 'supplier_organization_id', 'articles'));
    }

    public function orderExport($order_type_id, $order_id, $parent_id=0) {

        $debug = false;
        $results = [];

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(!$user->acl['isRoot'] || $user->organization->paramsConfig['hasOrdersGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');

        switch ($order_type_id) {
            case Configure::read('Order.type.promotion'):
                 $ordersTable->addBehavior('OrderPromotions');
                 $prod_gas_promotion_id = $parent_id;
                break;
            case Configure::read('Order.type.des'):
            case Configure::read('Order.type.des_titolare'):
                $des_order_id = $parent_id;
                break;
        }

        // debug($ordersTable);
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);
        // debug($orderResults);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);

        if($articlesOrdersTable!==false) {

            $where = [];
            // $where['ArticlesOrders'] = ['article_id' => 2662]; 
            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');
            $results = $articlesOrdersTable->getCarts($user, $organization_id, $orderResults, $where, $options);

            if(!empty($results)) {
                $results = new ApiArticleOrderDecorator($user, $results, $orderResults);
                //$results = new ArticleDecorator($results);
                $results = $results->results;
            }
        }
        debug($results);

        $this->set(compact('results'));
    }
}