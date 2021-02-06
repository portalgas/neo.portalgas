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

        $deliveriesTable = TableRegistry::get('Deliveries');
                    
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                                'Deliveries.stato_elaborazione' => 'OPEN',
                                'Deliveries.sys' => 'N',
                                'DATE(Deliveries.data) >= CURDATE()'];
        $deliveries = $deliveriesTable->getsList($user, $organization_id, $where);

        $sysDeliveries = $deliveriesTable->getDeliverySysList($user, $organization_id);

        $results = [];
        $results += $deliveries;
        $results += $sysDeliveries;

        return $results;
    }    

    /*
     * implement
     */   
    public function getInfoParent($user, $organization_id, $parent_id, $where=[], $debug=false) {
      
       if(empty($parent_id))
        $results = '';

       return $results;
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
                // debug($result);exit;
                $listResults[$result->id] = $result->suppliers_organization->name.' - '.$result->delivery->luogo.' '.$result->delivery->data;
            }
        }

        // debug($listResults);
        return $listResults;
    }         
}