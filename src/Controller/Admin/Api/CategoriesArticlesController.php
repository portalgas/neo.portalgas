<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CategoriesArticlesController extends ApiAppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);
    }

    /*
     * lista di tutte le categorie degli articoli
     */
    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $organization_id = $this->_user->organization->id;

        /*
         * se filtro per produttore verifico chi gestisce il listino
         * se il produttore visualizzo le categorie del produttore
         */
        $search_order_id = $this->request->getData('search_order_id');
        if(!empty($search_order_id)) {
            $where = [];
            $where = ['Orders.id' => $search_order_id,
                      'Orders.organization_id' => $this->_user->organization->id
                    ];
            $ordersTable = TableRegistry::get('Orders');

            $order = $ordersTable->find()
                                  ->where($where)
                                  ->first();
            // se e' socialmarket puo' essere null
            if(!empty($order) && $order->owner_organization_id!=$this->_user->organization->id)
                $organization_id = $order->owner_organization_id;
        }

        $search_supplier_organization_id = $this->request->getData('search_supplier_organization_id');
        if(!empty($search_supplier_organization_id)) {
            $where = [];
            $where = ['SuppliersOrganizations.id' => $search_supplier_organization_id,
                      'SuppliersOrganizations.organization_id' => $this->_user->organization->id
                    ];
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $suppliers_organization = $suppliersOrganizationsTable->find()
                                                            ->where($where)
                                                            ->first();

            if(!empty($suppliers_organization) && $suppliers_organization->owner_organization_id!=$this->_user->organization->id)
                $organization_id = $suppliers_organization->owner_organization_id;
        } // end if(!empty($search_supplier_organization_id))

        /*
         * estraggo i CategoriesArticles
         */
        $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
        /*
        $categoriesArticles = $categoriesArticlesTable->find('treeList',
                        ['spacer' => '&nbsp;&nbsp;&nbsp;',
                         'conditions' => ['organization_id' => $organization_id],
                         'order' => ['CategoriesArticles.name' => 'asc']]);
        */
        $categoriesArticles = $categoriesArticlesTable->jsListGets($this->_user, $this->_organization->id);

        $results['results'] = $categoriesArticles;

        /*
         * il json viene ordinato per id
         * debug($categoriesSuppliersResults->toArray());
         */
        return $this->_response($results);
    }
}
