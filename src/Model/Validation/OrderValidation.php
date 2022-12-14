<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderValidation extends Validation
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function dateComparison($value, $operator, $field2, $context)
    {
    	// debug($context); 	
    	$value2 = $context['data'][$field2]; 
    	
        if(empty($value2))
            return true;
            
        $value = $value['year'].$value['month'].$value['day'];
        $value2 = $value2['year'].$value2['month'].$value2['day'];
        if (!Validation::comparison($value, $operator, $value2))
            return false;
    
        return true;
    }

    public static function dateComparisonToDelivery($value, $operator, $context)
    {
        // debug($context);     
        $organization_id = $context['data']['organization_id'];
        $delivery_id = $context['data']['delivery_id'];  

        $deliveriesTable = TableRegistry::get('Deliveries');

        $where = ['Deliveries.organization_id' => $organization_id,
                  'Deliveries.id' => $delivery_id];
        // debug($context['data']);

        $results = $deliveriesTable->find()
                            ->select(['data', 'sys'])
                            ->where($where)
                            ->first();
        if(empty($results))
            return false;

        if($results->sys=='Y') // consegna Da definire
            return true;

        $value = $value['year'].$value['month'].$value['day'];
        // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
        // $value2 = $results->data->i18nFormat('Ymd');        
        $value2 = $results->data->format('Ymd');
        // debug('dateComparisonToDelivery '.$value.' '.$operator.' '.$value2);
        // debug(Validation::comparison($value, $operator, $value2));
        if (!Validation::comparison($value, $operator, $value2))
            return false;

        return true;    
    }

    public static function orderDuplicate($value, $context)
    { 
    	// debug($context); 	
    	$organization_id = $context['data']['organization_id'];
    	$delivery_id = $context['data']['delivery_id'];  
        $supplier_organization_id = $context['data']['supplier_organization_id']; 
        
        /*
         * se e' PROMOTION posso avere il medesimo ordine su una consegna 
         * se e' ordine GasGroup posso avere il medesimo ordine (GasGroupParent) su una consegna 
         */
        $type_draws = ['SIMPLE', 'COMPLETE'];

    	$ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id,
                  'Orders.supplier_organization_id' => $supplier_organization_id,
                  'Orders.order_type_id != ' => Configure::read('Order.type.gas_parent_groups'),
                  'Orders.type_draw IN ' => $type_draws];

        /*
        * workaround => per edit: validator e' definito 'on' => ['create'] non va
        */
        if(!$context['newRecord']) {
            $where += ['Orders.id !=' => $context['data']['id']];
        }

        // debug($where);
        $results = $ordersTable->find()
                            ->where($where)
                            ->first();

        // debug($results);
        if(!empty($results))
            return false;
        else
    		return true; 	
    }
}