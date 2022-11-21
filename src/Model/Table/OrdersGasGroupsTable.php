<?php
namespace App\Model\Table;


use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrdersGasGroupsTable extends OrdersTable implements OrderTableInterface 
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
                   'provider' => 'order',
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
    public function getSuppliersOrganizations($user, $organization_id, $where=[], $debug=false) {

        $results = [];

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
    
        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
                  'SuppliersOrganizations.stato' => 'Y',
                  'SuppliersOrganizations.owner_articles' => 'REFERENT'];

        $results = $suppliersOrganizationsTable->find()
                                ->where($where)
                                ->contain(['Suppliers', 'CategoriesSuppliers'])
                                ->order(['SuppliersOrganizations.name'])
                                ->all();
        return $results;
    } 

    /*
     * implement
     */ 
    public function getDeliveries($user, $organization_id, $where=[], $debug=false) {

        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
                    
        $where['Deliveries'] = ['DATE(GasGroup.data) >= CURDATE()'];
        $deliveries = $gasGroupDeliveriesTable->getsList($user, $organization_id, $where);

        $results = [];
        $results += $deliveries;

        return $results;
    }    

    /*
     * implement
     * dati promozione / order des
     */   
    public function getParent($user, $organization_id, $parent_id, $where=[], $debug=false) {
      
       if(empty($parent_id))
        $results = '';

        $ordersGasTable = TableRegistry::get('OrdersGas');

        $where = ['OrdersGas.organization_id' => $organization_id,
                  'OrdersGas.id' => $parent_id];

        $results = $ordersGasTable->find()
                                    ->where($where)
                                    ->contain(['Deliveries', 'SuppliersOrganizations' => ['Suppliers']])
                                    ->first();

        return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */   
    public function afterSaveWithRequest($user, $organization_id, $request, $debug=false) {

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
                                  'OrderTypes' => ['conditions' => ['code' => 'GASGROUP']],
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
                  * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM Y');
                  */                  
                // debug($result);exit;
                $listResults[$result->id] = $result->suppliers_organization->name.' - '.$result->delivery->data->i18nFormat('eeee d MMMM').' - '.$result->delivery->luogo;
            }
        }

        // debug($listResults);
        return $listResults;
    }         
}