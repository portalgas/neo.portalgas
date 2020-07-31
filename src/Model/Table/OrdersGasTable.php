<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

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

        $this->entityClass('App\Model\Entity\Order');
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
    public function getSuppliersOrganizations($user, $order_id=0, $debug=false) {
        
    } 

    /*
     * implement
     */ 
    public function getDeliveries($user, $order_id=0, $debug=false) {

        $deliveriesTable = TableRegistry::get('Deliveries');
    
        $where = ['DATE(Deliveries.data) >= CURDATE()'];
        $deliveries = $deliveriesTable->getsList($user, $where);

        $sysDeliveries = $deliveriesTable->getDeliverySysList($user);

        $results = [];
        $results += $deliveries;
        $results += $sysDeliveries;

        return $results;
    }    
}