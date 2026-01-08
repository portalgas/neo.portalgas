<?php
namespace App\Model\Table;


use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrdersGasTable extends OrdersTable implements OrderTableInterface 
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\Order');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        $validator->setProvider('orderGas', \App\Model\Validation\OrderGasValidation::class);

        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'totArticles' => [
                   'rule' => ['totArticles'],
                   'provider' => 'orderGas',
                   'message' => 'Il produttore scelto non ha articoli che si possono associare ad un ordine'
                ]
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {   
        // debug('OrdersGasTable buildRules');
        $rules = parent::buildRules($rules);

        return $rules;
    }

    /*
     * implement
     */ 
    public function getSuppliersOrganizations($user, $organization_id, $user_id, $where=[], $debug=false) {
        return parent::getSuppliersOrganizations($user, $organization_id, $user_id, $where, $debug);
    } 
 
    /*
     * implement
     * deliveriesTable->getsActiveGroup
     *      array['N'] elenco consegne attive per select
     *      array['Y'] consegna da definire       
     */ 
    public function getDeliveries($user, $organization_id, $where=[], $debug=false) {
        $results = [];
        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getsActiveGroup($user, $organization_id, $where);
        return $results;
    }    

    /*
     * implement
     * dati promozione / order des / gas_groups
     */   
    public function getParent($user, $organization_id, $parent_id, $where=[], $debug=false) {
      
       if(empty($parent_id))
        $results = '';

       return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */   
    public function afterAddWithRequest($user, $organization_id, $order, $request, $debug=false) {
        return parent::afterAddWithRequest($user, $organization_id, $order, $request, $debug);
    }
    
    /*
     * implement
     */   
    public function getById($user, $organization_id, $order_id, $debug=false) {
       return parent::getById($user, $organization_id, $order_id, $debug);
    }

    /*
     * implement
     */      
    public function gets($user, $organization_id, $where=[], $debug=false) {
       
        $results = [];
        $where_order = [];
        $where_delivery = [];

        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                              $this->getAlias().'.isVisibleBackOffice' => 'Y'],
                              $where_order);
        if($debug) debug($where_order); 

        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id], $where_delivery);
                          
        if($debug) debug($where_delivery);
        $results = $this->find()
                                ->where($where)
                                ->contain([
                                  'OrderTypes' => ['conditions' => ['code' => 'GAS']],
                                  'OrderStateCodes',
                                  'SuppliersOrganizations' => ['Suppliers'], 
                                  'Deliveries' => ['conditions' => $where_delivery]  
                                ])
                                ->order([$this->getAlias().'.data_inizio'])
                                ->all();
        // debug($results);
        
        return $results;
    }
    
    /*
     * implement
     */     
    public function getsList($user, $organization_id, $where=[], $debug=false) {
               
        $listResults = [];

        $results = $this->gets($user, $organization_id, $where);
        if(!empty($results)) {
            foreach($results as $result) {
                 /*
                  * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                  * key array non per id, nel json perde l'ordinamento della data
                  * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM yyyy');
                  */                  
                // debug($result);exit;
                $listResults[$result->id] = $result->suppliers_organization->name.' - '.$result->delivery->data->i18nFormat('eeee d MMMM').' - '.$result->delivery->luogo;
            }
        }

        // debug($listResults);
        return $listResults;
    }         
}