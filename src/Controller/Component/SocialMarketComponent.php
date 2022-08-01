<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;

class SocialMarketComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request$this->_registry->load('Distance');
        $this->_registry->load('Distance');
    }

    /*
     * da /admin/api/orders/user-cart-gets/9
     * da stampa carrello   
     * elenco ordini con acquisti dell'utente x fe
     */
    public function userCartGets($user, $organization_id, $order_id, $debug=false) {

        $results = [];

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.id' => $order_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.state_code != ' => 'CREATE-INCOMPLETE'];

        $result = $ordersTable->find()
                                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries', 'SuppliersOrganizations' => [
                                    'Suppliers',
                                    'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles' => ['sort' => ['ordering']]]]
                                  ],
                                          /* estrae anche gli ordini senza acquisti, perche' query aggiuntiva hasMany
                                          'Carts' => ['conditions' => ['Carts.user_id' => $user->id,
                                                                       'Carts.organization_id' => $organization_id,
                                                                        'Carts.deleteToReferent' => 'N']]
                                            */
                                          ])
                                ->where($where)
                                ->first();

        $i=0;
        $newResults[$i] = $result;
        $newResults[$i]['article_orders'] = [];

        $found_cart = false;

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $result);

        if($articlesOrdersTable!==false) {

            $where['order_id'] = $result['id'];

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');
            $options['page'] = 1;
            $articlesOrdersResults = $articlesOrdersTable->getCartsByUser($user, $organization_id, $user->id, $result, $where, $options);
            // debug($articlesOrdersResults);

            /*
             * estraggo solo quelli acquistati dallo user
             */
            $ii=0;
            foreach($articlesOrdersResults as  $numResult2 => $articlesOrdersResult) {
                /*
                 * se lo user non ha acquisti e' cmq valorizzato qta / qta_new
                 */
                if(!isset($articlesOrdersResult['cart']) || !isset($articlesOrdersResult['cart']['user_id']) ||
                    empty($articlesOrdersResult['cart']['user_id'])) {
                     unset($articlesOrdersResult[$numResult2]);

                }
                else {
                    $found_cart = true;
                    $articlesOrdersResult = new ApiArticleOrderDecorator($user, $articlesOrdersResult, $result);
                    $newResults[$i]['article_orders'][$ii] = $articlesOrdersResult->results;
                    $ii++;
                }
            }
        } // end if($articlesOrdersTable!==false)

        /*
         * distance
         */
        $distance = $this->_registry->Distance->get($user, $result->suppliers_organization);
        $newResults[$i]['distance'] = $distance;


        return $newResults;
    }
}