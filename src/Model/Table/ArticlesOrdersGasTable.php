<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticlesOrdersGasTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->entityClass('App\Model\Entity\ArticlesOrder');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }

    /*
     * front-end - estrae gli articoli associati ad un ordine ed evenuuali acquisti per user  
     *  ArticlesOrders.article_id              = Articles.id
     *  ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {

        $order_id = $where['order_id'];

        $order_state_code = $orderResults->state_code;

        if(!isset($where['ArticlesOrders']))
           $where['ArticlesOrders'] = [];
        $where['ArticlesOrders'] = array_merge([$this->alias().'.organization_id' => $organization_id,
                              $this->alias().'.order_id' => $order_id,
                              $this->alias().'.stato != ' => 'N'], 
                              $where['ArticlesOrders']);

        switch ($order_state_code) {
            case 'RI-OPEN-VALIDATE':
                $where['ArticlesOrders'] += [$this->alias().'.pezzi_confezione > ' => 1];
                $results = $this->getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug); 
            break;
            default:
                $results = $this->gets($user, $organization_id, $orderResults, $where, $options, $debug);
            break;
        }          
        if($debug) debug($results);

        /*
         * estraggo eventuali acquisti
         */ 
        if($results) {
            
            $cartsTable = TableRegistry::get('Carts');
            foreach($results as $numResult => $result) {

                $results[$numResult]['order'] = $orderResults;

                /*
                 * Carts
                 */
                $where_cart = ['Carts.organization_id' => $result['organization_id'],
                          'Carts.order_id' => $result['order_id'],
                          'Carts.article_organization_id' => $result['article_organization_id'],
                          'Carts.article_id' => $result['article_id'],
                          'Carts.user_id' => $user_id, 
                          'Carts.deleteToReferent' => 'N',
                          'Carts.stato' => 'Y'];
                $cartResults = $cartsTable->find()
                            ->where($where_cart)
                            ->first();
                if($debug) debug($where_cart);
                if($debug) debug($cartResults);

                $results[$numResult]['cart'] = [];
                if(!empty($cartResults)) {
                    $results[$numResult]['cart'] = $cartResults;
                    $results[$numResult]['cart']['qty'] = $cartResults->qta;
                    $results[$numResult]['cart']['qty_new'] = $cartResults->qta;  // nuovo valore da FE
                } 
                else {
                    $results[$numResult]['cart']['organization_id'] = $result['organization_id'];
                    $results[$numResult]['cart']['user_id'] = $result['user_id'];
                    $results[$numResult]['cart']['order_id'] = $result['order_id'];
                    $results[$numResult]['cart']['article_organization_id'] = $result['article_organization_id'];
                    $results[$numResult]['cart']['article_id'] = $result['article_id'];                  
                    $results[$numResult]['cart']['qty'] = 0;
                    $results[$numResult]['cart']['qty_new'] = 0;  // nuovo valore da FE
                }
            }
        } // if($results)

        if($debug) debug($results);

        return $results;
    }   

    /*
     * da Orders chi gestisce listino articoli
     * order_type_id' => (int) 4,
     * owner_articles' => 'REFERENT',
     * owner_organization_id
     * owner_supplier_organization_id
     */
    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {    
       
        $this->_getOptions($options); // setta sort / limit / page

        $results = $this->find()
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();

        return $results;
    }    

    public function getRiOpenValidate($user, $organization_id, $orderResults, $where, $options, $debug=false) {

        $this->_getOptions($options); // setta sort / limit / page

        $results = [];
        $resultsArticlesOrders = $this->find()
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->where($where['ArticlesOrders'])
                        ->order($this->_sort)
                        ->limit($this->_limit)
                        ->page($this->_page)
                        ->all()
                        ->toArray();

        /*
         * estraggo eventuali acquisti
         */ 
        if($resultsArticlesOrders) {
            $i=0;
            foreach($resultsArticlesOrders as $numResult => $resultsArticlesOrder) {

                $qta_cart = $resultsArticlesOrder['qta_cart'];

                // se DES non prendo ArticlesOrder.qta_cart perche' e' la somma di tutti i GAS ma lo ricalcolo
                if($orderResults->order_type_id==Configure::read('Order.type.des') ||
                   $orderResults->order_type_id==Configure::read('Order.type.des-titolare')) {
                    
                    $cartsTable = TableRegistry::get('Carts');
                    $qta_cart = $cartsTable->getQtaCartByArticle($user, $organization_id, $orderResults->id, $resultsArticlesOrder['article_organization_id'], $resultsArticlesOrder['article_id'], $debug);
                }
                if($qta_cart!==false) {
                    $resultsArticlesOrder['qta_cart'] = $qta_cart;
                }

                $differenza_da_ordinare = ($qta_cart % $resultsArticlesOrder['pezzi_confezione']);
                
                if($differenza_da_ordinare>0) {
                    $differenza_da_ordinare = ($resultsArticlesOrder['pezzi_confezione'] - $differenza_da_ordinare);
                    $differenza_importo = ($differenza_da_ordinare * $resultsArticlesOrder['prezzo']);
                    
                    $results[$i] = $resultsArticlesOrder;
                    $results[$i]['riopen'] = [];
                    $results[$i]['riopen']['differenza_da_ordinare'] = $differenza_da_ordinare;
                    $results[$i]['riopen']['differenza_importo'] = $differenza_importo;
                    $i++;
                }
                else 
                    unset($resultsArticlesOrders[$numResult]);
            }
        }

        return $results;
    }    
}
