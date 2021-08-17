<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;

class OrderComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
        $this->_registry->load('Distance');
    }

    /*
     * da /admin/api/orders/user-cart-gets
     * da stampa carrello   
     * elenco ordini con acquisti dell'utente x fe
     */
    public function userCartGets($user, $organization_id, $delivery_id, $debug=false) {

        $results = [];
        
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.isVisibleBackOffice' => 'Y',
                  'Orders.state_code != ' => 'CREATE-INCOMPLETE'];

        if(!empty($delivery_id)) {
            /*
             * per gli ordini per produttore non ho la consegna
             */
            $where += ['Orders.delivery_id' => $delivery_id, 
                    // 'Orders.id' => 20240
                      ];
        }

        $ordersResults = $ordersTable->find()
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
                                ->order(['Orders.data_inizio'])
                                ->toArray();

        /*
         * elimino ordini senza acquisti
         */
        $i=0;
        $newResults = [];
        foreach($ordersResults as $numResult => $result) {

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
                         unset($ordersResults[$numResult]);
                    }
                    else {
                        $found_cart = true;
                        $articlesOrdersResult = new ApiArticleOrderDecorator($user, $articlesOrdersResult, $result); 
                        $newResults[$i]['article_orders'][$ii] = $articlesOrdersResult->results;
                        $ii++;
                    }
                }
            } // end if($articlesOrdersTable!==false) 

            if($found_cart) {

                /*
                 * aggiunge ad un ordine le eventuali 
                 *  SummaryOrder 
                 *  SummaryOrderTrapsort spese di trasporto
                 *  SummaryOrderMore spese generiche
                 *  SummaryOrderLess sconti
                 */
                $lifeCycleSummaryOrdersTable = TableRegistry::get('LifeCycleSummaryOrders');
                $summaryOrderPlusTable = TableRegistry::get('SummaryOrderPlus');

                if($lifeCycleSummaryOrdersTable->canAddSummaryOrder($user, $result->state_code)) {
                    
                    $resultsSummaryOrderPlus = $summaryOrderPlusTable->addSummaryOrder($user, $result, $user->id);
               
                    $newResults[$i]['summary_order'] = $resultsSummaryOrderPlus->summary_order;
                    $newResults[$i]['summary_order_aggregate'] = $resultsSummaryOrderPlus->summary_order_aggregate;
                    $newResults[$i]['summary_order_trasport'] = $resultsSummaryOrderPlus->summary_order_trasport;
                    $newResults[$i]['summary_order_cost_more'] = $resultsSummaryOrderPlus->summary_order_cost_more;
                    $newResults[$i]['summary_order_cost_less'] = $resultsSummaryOrderPlus->summary_order_cost_less;

                    // $newResults = $this->ExportDoc->getCartCompliteOrder($order_id, $results, $resultsSummaryOrderAggregate, $resultsSummaryOrderTrasport, $resultsSummaryOrderCostMore, $resultsSummaryOrderCostLess, $debug);                 
                }  // if($result->state_code=='PROCESSED-ON-DELIVERY' || $result->state_code=='CLOSE')

                /*
                 * referenti
                 */
                if(isset($result->suppliers_organization->suppliers_organizations_referents)) { 
                    $referentsResult = new ApiSuppliersOrganizationsReferentDecorator($user, $result->suppliers_organization->suppliers_organizations_referents, $result); 
                    $newResults[$i]['referents'] = $referentsResult->results;
                    unset($result->suppliers_organization->suppliers_organizations_referents);
                }
                
                /*
                 * distance
                 */
                $distance = $this->_registry->Distance->get($user, $result->suppliers_organization);
                $newResults[$i]['distance'] = $distance;
                
                $i++;
                $found_cart = false;
            }
            else {
                unset($newResults[$i]);
            }

        } // end foreach($ordersResults as $numResult => $result) 

        return $newResults;  
    } 

    /* 
     * /admin/api/orders/getArticlesOrdersByOrderId 
     * da stampa carrello   
     * front-end - estrae gli articoli associati ad un ordine ed evenuali acquisti per user  
     */
    public function getArticlesOrdersByOrderId($user, $organization_id, $order_id, $order_type_id, $options=[], $debug=false) {
        
        $results = [];

        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($user, $organization_id, $order_type_id);

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);

        if($articlesOrdersTable!==false) {

            /*
             * options
             */
            isset($options['q'])? $q = $options['q'] : $q = '';
            isset($options['sort'])? $sort = $options['sort'] : $sort = '';
            isset($options['page'])?  $page = $options['page'] : $page = '1';
            isset($options['sql_limit'])?  $sql_limit = $options['sql_limit'] : $sql_limit = Configure::read('sql.limit');

            $where=[];
            if(!empty($q)) {
                $where_q = [];
                if(strpos($q, ' ')!==false) {
                    $qs = explode(' ', $q);
                    foreach($qs as  $numResult => $q) {
                        $where_q[$numResult] = ['or' => ['Articles.name LIKE' => '%'.$q.'%',
                                                         'Articles.nota LIKE' => '%'.$q.'%']];
                    }
                }
                else {
                    $where_q = ['or' => ['Articles.name LIKE' => '%'.$q.'%',
                                          'Articles.nota LIKE' => '%'.$q.'%']];
                }
                $where['Articles'] = $where_q;
            }

            $options = [];
            $options['sort'] = $sort;
            $options['limit'] = $sql_limit;
            $options['page'] = $page;
            $results = $articlesOrdersTable->getCartsByUser($user, $organization_id, $user->id, $orderResults, $where, $options);
 
            if(!empty($results)) {
                $results = new ApiArticleOrderDecorator($user, $results, $orderResults);
                //$results = new ArticleDecorator($results);
                $results = $results->results;
            }
        }

        return $results;
    }   
}