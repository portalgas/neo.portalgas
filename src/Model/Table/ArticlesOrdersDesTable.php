<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ArticlesOrdersDesTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\ArticlesOrder');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }    

    /*
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article_order, $debug=false) {
        
        // debug($order);
        $organization_id = $article_order['ids']['organization_id'];
        $order_id = $article_order['ids']['order_id'];
        $article_organization_id = $article_order['ids']['article_organization_id'];
        $article_id = $article_order['ids']['article_id'];

        $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');
        
        $where = ['DesOrdersOrganizations.des_order_id' => $order['des_order_id']];
        $desOrdersOrganizationsResults = $desOrdersOrganizationsTable->find('all')
                                                   // ->contain(['DesOrders'])
                                                    ->where($where)
                                                    ->all();

        if($debug) debug("Trovati ".$desOrdersOrganizationsResults->count()." ordini associati all'ordine DES");
        if($debug) debug($desOrdersOrganizationsResults);
        
        if($desOrdersOrganizationsResults->count()>0) {
            
            /*
             * calcolo la Somma di Cart.qta per tutti i GAS dell'ordine DES
             */
            $qta_cart_new = 0; 
            foreach ($desOrdersOrganizationsResults as $desOrdersOrganizationsResult) {

                // debug($desOrdersOrganizationsResult);
                
                $organization_id = $desOrdersOrganizationsResult->organization_id;
                $order_id = $desOrdersOrganizationsResult->order_id;

                $cartsTable = TableRegistry::get('Carts');
                $qta_cart_new += $cartsTable->getQtaCartByArticle($user, $organization_id, $order_id, $article_organization_id, $article_id, $debug);
            }

            if($debug) debug("Per tutti i GAS dell'ordine DES aggiornero' ArticlesOrder.qta_cart con la somma di tutti gli acquisti dei GAS ".$qta_cart_new);
                            
            /* 
             * aggiorno tutti gli ordini del DES
             */
            foreach ($desOrdersOrganizationsResults as $desOrdersOrganizationsResult) {
     
                /*
                 * override con l'ordine del GAS del ciclo
                 */      
                $organization_id = $desOrdersOrganizationsResult->organization_id;
                $article_order['ids']['organization_id'] = $desOrdersOrganizationsResult->organization_id;
                $article_order['ids']['order_id'] = $desOrdersOrganizationsResult->order_id;
                
                $article_order['qta_cart'] = $qta_cart_new;
                $this->_updateArticlesOrderQtaCart_StatoQtaMax($user, $organization_id, $article_order, $debug);
            }
          
        } // end if(!empty($desOrdersOrganizationsResults))
    }

    /*
     * implement
     */
    public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {
        return parent::getCartsByUser($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {
        $this->_getOptions($options); // setta sort
        return parent::getCartsByArticles($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {    
       return parent::gets($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */    
    public function getByIds($user, $organization_id, $ids, $debug=false) {    
       return parent::getByIds($user, $organization_id, $ids, $debug);
    } 
}
