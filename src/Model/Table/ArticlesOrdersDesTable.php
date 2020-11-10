<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticlesOrdersDesTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
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
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug=false) {
        
        App::import('Model', 'DesOrdersOrganization');
        $DesOrdersOrganization = new DesOrdersOrganization();

        $DesOrdersOrganization->unbindModel(['belongsTo' => ['Order', 'Organization', 'De']]);
        
        $options = [];
        $options['conditions'] = ['DesOrdersOrganization.des_order_id' => $results['Order']['des_order_id']];
        $options['fields'] = ['DesOrder.des_id','DesOrder.des_supplier_id','DesOrdersOrganization.organization_id','DesOrdersOrganization.order_id'];
        $options['recursive'] = 1;
        $desOrdersOrganizationsResults = $DesOrdersOrganization->find('all', $options);

        if($debug) debug("Trovati ".count($desOrdersOrganizationsResults)." ordini associati all'ordine DES");
        if($debug) debug($desOrdersOrganizationsResults);
        
        if(!empty($desOrdersOrganizationsResults)) {
            
            /*
             * calcolo la Somma di Cart.qta per tutti i GAS dell'ordine DES
             */
            $qta_cart_new = 0; 
            foreach ($desOrdersOrganizationsResults as $desOrdersOrganizationsResult) {
                $organization_id = $desOrdersOrganizationsResult['DesOrdersOrganization']['organization_id'];
                $order_id = $desOrdersOrganizationsResult['DesOrdersOrganization']['order_id'];

                $cartsTable = TableRegistry::get('Carts');
                $qta_cart_new += $cartsTable->getQtaCartByArticle($organization_id, $order_id, $article_organization_id, $article_id, $debug);
            }

            if($debug) debug("Per tutti i GAS dell'ordine DES aggiornero' ArticlesOrder.qta_cart con la somma di tutti gli acquisti dei GAS ".$qta_cart_new);
                            
            /* 
             * aggiorno tutti gli ordini del DES
             */
            foreach ($desOrdersOrganizationsResults as $desOrdersOrganizationsResult) {
                
                $results['ArticlesOrder']['organization_id'] = $desOrdersOrganizationsResult['DesOrdersOrganization']['organization_id'];
                $results['ArticlesOrder']['order_id'] = $desOrdersOrganizationsResult['DesOrdersOrganization']['order_id'];
                
                $results['ArticlesOrder']['qta_cart'] = $qta_cart_new;
                $this->_updateArticlesOrderQtaCart_StatoQtaMax($results, $debug);
            }
            if($debug) debug($desSupplierResults);
          
        } // end if(!empty($desOrdersOrganizationsResults))
    }

    /*
     * implement
     */
    public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {
        return parent::getCarts($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
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
