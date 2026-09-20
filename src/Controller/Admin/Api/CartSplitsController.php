<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class CartSplitsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cart');
        $this->loadComponent('CartSplit');
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);
    }

    /*
     * url: /admin/api/cart-splits/storage
     */
    public function storage() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];

        $user_id = $this->_user->id;
        $organization_id = $this->_organization->id;

        $carts = $this->request->getData();
        if(!empty($carts)) {

            /*
            * trasport 
            */
            $order_id = $carts[0]['order_id'];
            $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');
            $summaryOrderTrasport = $summaryOrderTrasportsTable->getByUserByOrder($this->_user, $this->_organization->id, $this->_user->id, $order_id);
        
            $cartSplitsTable = TableRegistry::get('CartSplits');
            foreach($carts as $cart) {

                $article_organization_id = $cart['article_organization_id'];
                $article_id = $cart['article_id'];

                if(isset($cart['cart_splits'])) {
                    foreach($cart['cart_splits'] as $cart_split) {
                        
                        $datas = [];    
                        $cart_entity = null;

                        /*
                         * entity
                         * */
                        if(strpos('NEW-', $cart_split['id'])===false) {
                            $cart_entity = $cartSplitsTable->find()->where(['id' => $cart_split['id']])->first();
                        }
                        if(empty($cart_entity)) {
                            $cart_entity = $cartSplitsTable->newEntity();
                            $datas['id'] = null;
                        }

                       // debug('cart_split[qta] = '.$cart_split['qta']);
                        if($cart_split['qta']==0) {
                            /*
                             * delete
                             * */
                            if(!empty($cart_entity)) 
                                $cartSplitsTable->delete($cart_entity);
                        }
                        else {
                            /*
                             * insert
                             * */
                            $datas['organization_id'] = $organization_id;
                            $datas['order_id'] = $order_id;
                            $datas['user_id'] = $user_id;
                            $datas['article_organization_id'] = $article_organization_id;
                            $datas['article_id'] = $article_id;
                            $datas['name'] = $cart_split['name'];    
                            $datas['qta'] = $cart_split['qta'];    
                            $datas['trasport'] = $cart_split['trasport'];    
                            $cart_entity = $cartSplitsTable->patchEntity($cart_entity, $datas);
                            if (!$cartSplitsTable->save($cart_entity)) {
                                debug($datas);
                                dd($cart_entity->getErrors());
                                Log::write('error', $cart_entity->getErrors());
                            }
                        }

                    } // end foreach($carts as $cart)

                    $where = ['CartSplits.organization_id' => $organization_id,
                                'CartSplits.order_id' => $order_id,
                                'CartSplits.user_id' => $user_id,
                                'CartSplits.article_organization_id' => $article_organization_id,
                                'CartSplits.article_id' => $article_id];
                    $cartSplits = $cartSplitsTable->find()->where($where)->contain(['Carts', 'ArticlesOrders'])->all();        
                    $results = $this->_setTrasport($cartSplits, $summaryOrderTrasport);
            
                } // end if(isset($carts['cart_splits']))

            } // end foreach($carts as $cart)
        }

        return $this->_response(['results' => $results]);
    }

    /*
     * url: /admin/api/cart-splits/getByOrder
     */
    public function getByOrder() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');

        $results = $this->CartSplit->getByOrder($this->_user, $this->_organization->id, $order_id, $this->_user->id); 

        return $this->_response(['carts' => $results]);
    }

    private function _setTrasport($cart_splits, $summaryOrderTrasport) {

        $debug = false;

        if(empty($summaryOrderTrasport))
            return $results;

        if($debug) Log::debug('-------------------------------------------------------------');
        if($debug) Log::debug('trasporto, ho speso in tutto '. $summaryOrderTrasport->importo.' e di trasporto ho '.$summaryOrderTrasport->importo_trasport.' euro');

        $percentuale = (((float)$summaryOrderTrasport->importo_trasport / (float)$summaryOrderTrasport->importo) * 100);
        $percentuale = round($percentuale, 2);
        if($debug) Log::debug("quindi il {$percentuale}% è di trasporto");

        $cartSplitsTable = TableRegistry::get('CartSplits');

        foreach($cart_splits as $cart_split) {

            if($debug) Log::debug('');
            if($debug) Log::debug('per '.$cart_split->articles_order['name'].' prezzo '.$cart_split->articles_order['prezzo'].' euro');
            if($debug) Log::debug('');

            $importo_totale = (float)($cart_split->articles_order['prezzo'] * $cart_split['qta']);
            if($debug) Log::debug('ho speso (cart[prezzo] * cart_split[qta] '.$cart_split['qta'].') = '. $importo_totale.' euro');
            
            $sub_trasport = (($importo_totale * $percentuale) / 100);
            $sub_trasport = round($sub_trasport, 2);
            if($debug) Log::debug("{$percentuale}% di {$importo_totale} è {$sub_trasport} euro");

            //$results[$numResult]['cart_splits'][$numResult2]['importo_totale'] = $importo_totale;
            // $results[$numResult]['cart_splits'][$numResult2]['trasport'] = $sub_trasport;

            $cartSplit = $cartSplitsTable->get($cart_split['id']);
            
            $datas = [];
            $datas['trasport'] = $sub_trasport;    
            $cart_entity = $cartSplitsTable->patchEntity($cartSplit, $datas);
            // debug($cartSplit);
            if (!$cartSplitsTable->save($cartSplit)) {
                debug($cartSplit);
                dd($cartSplit->getErrors());
                Log::write('error', $cartSplit->getErrors());
            }

        } // end foreach($cart_splits as $cart_split)

        return $results;
    }
}