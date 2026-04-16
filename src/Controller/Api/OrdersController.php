<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;
use App\Decorator\ApiArticleOrderDecorator;

class OrdersController extends ApiAppController
{
    private $_has_cache = false;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Gas');
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets']);
        $this->Authorization->skipAuthorization(['gets']);
    }

    /*
     * elenco ordini x fe da consegne
     */
    public function gets() {

        $debug = false;
        $results = [];

        $user = $this->Authentication->getIdentity();
        $organization_id = $this->request->getData('organization_id');
        $delivery_id = $this->request->getData('delivery_id');

        /*
         * escludo Order.type.gas_parent_groups perche' li non posso fare acquisti
         * solo Order.type.gas_groups
         */
        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.order_type_id != ' => Configure::read('Order.type.gas_parent_groups')
                ];

        if(!empty($delivery_id)) {
            /*
             * per gli ordini per produttore non ho la consegna
             */
            $where += ['Orders.delivery_id' => $delivery_id];
        }

        if(!empty($user) && isset($user->organization->paramsConfig['hasGasGroups']) && $user->organization->paramsConfig['hasGasGroups']=='Y') {
            // ctrl che l'utente appartertenga al gruppo
            $gasGroupsTable = TableRegistry::get('GasGroups');
            $gasGroups = $gasGroupsTable->findMyLists($user, $organization_id, $user->id);
            if(empty($gasGroups))
                $where += ['Orders.gas_group_id' => 0]; // utente non associato in alcun gruppo, prendo ordini non del gruppo
            else {
                $acls = array_keys($gasGroups);
                // $acls = array_merge($acls, [0]);
                $where += ['Orders.gas_group_id IN ' => $acls];
            }
        } // end if($user->organization->paramsConfig['hasGasGroups']=='Y')

        if(!empty($user)) {
            $contains = ['OrderStateCodes', 'OrderTypes', 'Deliveries',
                'SuppliersOrganizations' => [
                    'Suppliers',
                    'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles']]
                ],
                'ArticlesOrders' => [
                    'sort' => ['ArticlesOrders.name'],
                    'Carts' => ['conditions' => ['Carts.user_id' => $user->id]], 'Articles']
                ];
        }
        else {
            $contains = ['OrderStateCodes', 'OrderTypes', 'Deliveries',
                'SuppliersOrganizations' => [
                    'Suppliers',
                    /* 'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles']]*/
                ],
                'ArticlesOrders' => [
                    'sort' => ['ArticlesOrders.name'],
                    'Articles']
                ];
        }
        
        $ordersTable = TableRegistry::get('Orders');
        $orders = $ordersTable->find()
            ->contain($contains)
            ->where($where)
            ->order(['Orders.data_fine' => 'desc'])
            ->all();
        
        $results = [];
        $delivery_final_price = 0;
        foreach($orders as $numResult => $order) {
            
            $order_final_price = 0;

            $referents = new ApiSuppliersOrganizationsReferentDecorator($user, $order->suppliers_organization->suppliers_organizations_referents);
            $order->suppliers_organization->suppliers_organizations_referents = $referents->results;

            /*
             * prendo solo quelli con acquisti
             */
            $articles_orders = [];
            foreach($order->articles_orders as $articles_order) {
                if(!empty($articles_order->carts)) {
                    $art_orders = new ApiArticleOrderDecorator($user, $articles_order, $result);
                    $art_orders = $art_orders->results;

                    $order_final_price += $art_orders['carts'][0]['final_price'];
                    $delivery_final_price += $order_final_price;
                    $articles_orders[] = $art_orders;
                }
            }
            $results[$numResult]['order_final_price'] = $order_final_price;
            $results[$numResult]['order'] = $order;
            unset($results[$numResult]['order']['articles_orders']);
            $results[$numResult]['articles_orders'] = $articles_orders;
            
            /* per ogni ordine aggiunge le info dell'utente (autenticato o null)
             */
            $results[$numResult]['user'] = $user;
        }                

        return $this->_response(['delivery_final_price' => $delivery_final_price, 'user' => $user, 'datas' => $results]);
    }
}
