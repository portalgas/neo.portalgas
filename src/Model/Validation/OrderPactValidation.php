<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderPactValidation extends Validation
{    
    public static function orderDuplicate($value, $context)
    {
    	// debug($context); 	
    	$organization_id = $context['data']['organization_id'];
    	$delivery_id = $context['data']['delivery_id'];  
        $supplier_organization_id = $context['data']['supplier_organization_id']; 
        
        /*
         * se e' 'SIMPLE', 'COMPLETE' posso avere il medesimo ordine su una consegna 
         */
        $type_draws = ['SIMPLE', 'COMPLETE'];

    	$ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id,
                  'Orders.supplier_organization_id' => $supplier_organization_id,
                  'Orders.type_draw IN ' => $type_draws];
        // debug($where);

        /*
         * per edit: validator e' definito 'on' => ['create'] 
        if(isset($context['data']['id']))
            $where += ['Order.id !=' => $context['data']['id']];
         */

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