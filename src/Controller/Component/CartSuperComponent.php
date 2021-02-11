<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CartSuperComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    /*
     * $action = INSERT
     * $action = UPDATE-DELETE
     */
    protected function _ctrlValidita($user, $articles_order, $qta_new, $qta, $action, $debug=false) {

        $results = [];
        $esito = true;
        $msg = '';

        /*
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $where = ['ArticlesOrders.organization_id' => $organization_id,
                  'ArticlesOrders.order_id' => $order_id,
                  'ArticlesOrders.article_organization_id' => $article_organization_id,
                  'ArticlesOrders.article_id' => $article_id];
        // debug($where);

        $articlesOrders = $articlesOrdersTable->find()
                        ->contain('Carts')
                        ->where($where)
                        ->first(); 
        //debug($articlesOrders);
        */

        if(Configure::read('Logs.cart')) Log::write('debug', '_ctrlValidita ArticlesOrder.stato '.$articles_order['stato']);
        if(Configure::read('Logs.cart')) Log::write('debug', '_ctrlValidita ArticlesOrder.qta_minima '.$articles_order['qta_minima']);
        if(Configure::read('Logs.cart')) Log::write('debug', '_ctrlValidita ArticlesOrder.qta_massima '.$articles_order['qta_massima']);
        if(Configure::read('Logs.cart')) Log::write('debug', '_ctrlValidita ArticlesOrder.qta_massima_order '.$articles_order['qta_massima_order']);

        if($articles_order['stato']=='N') {
            $msg = __('cart_msg_stato_N');
            $esito = false;
        }  
            
        if($esito && isset($articles_order['carts']) && isset($articles_order['carts']['stato']) && $articles_order['carts']['stato']=='N') {
            $msg = __('cart_msg_stato_N');
            $esito = false;
        }  

        if($esito && $action!='INSERT') {
            if($articles_order['stato']=='QTAMAXORDER' && ($qta_new > $qta)) {
                $msg = sprintf(__('cart_msg_qtamax_order_stop'), $articles_order['qta_massima_order']);
                $esito = false;
            }
            else
            if($articles_order['stato']=='LOCK' && ($qta_new > $qta)) {
                $msg = __('cart_msg_block_stop'); 
                $esito = false; 
            }
        }

        if($esito) {

            if($qta_new>0 && ($qta_new < (int)$articles_order['qta_minima'])) {
                $msg = sprintf(__('cart_msg_qtamin'), $articles_order['qta_minima'], $qta_new);
                $esito = false;
            }
            else          
            if((int)$articles_order['qta_massima'] > 0) {
                /*
                 * Q T A - M A X
                 */                  
                if($qta_new>0 && ($qta_new > $articles_order['qta_massima'])) {  // ctrl qta massima riferita all'acquisto del singolo gasista
                    $msg = sprintf(__('cart_msg_qtamax'), $articles_order['qta_massima'], $qta_new);
                    $esito = false;
                }       
            }
            else    
            /*
             * Q T A - M A X - O R D E R 
             * */
            if((int)$articles_order['qta_massima_order'] > 0) {
                
                if($qta_new > $qta) { // ctrl che l'utente non abbia diminuito la qta

                    // qta_massima_order superata: ricalcolo la qta e articlesOrder.stato = QTAMAXORDER
                    if(((int)$articles_order['qta_cart'] - $qta + $qta_new) > $articles_order['qta_massima_order']) {
                    
                        $qta_label = ((int)$articles_order['qta_massima_order'] - (int)$articles_order['qta_cart'] + $qta); // la ricalcolo
                    
                        $msg = sprintf(__('cart_msg_qtamax_order'), $articles_order['qta_massima_order'], $qta_label);
                        $esito = false;
                    }
                    else  // qta massima raggiunta articlesOrder.stato = QTAMAXORDER
                    if(((int)$articles_order['qta_cart'] - (int)$qta + $qta_new) == (int)$articles_order['qta_massima_order']) {
                        // qta massima raggiunta: articlesOrder.stato = QTAMAXORDER
                    }

                }
            }
        } // end if($esito)
   
        $results['esito'] = $esito;
        $results['msg'] = $msg;

        return $results;
    }
}