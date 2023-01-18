<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderPromotionValidation extends Validation
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
        $type_draws = ['PROMOTION'];

    	 $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.delivery_id' => $delivery_id,
                  'Orders.supplier_organization_id' => $supplier_organization_id,
                  'Orders.type_draw IN ' => $type_draws];

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

    public static function dateComparisonToParent($value, $context)
    {
        $operator = '<=';
 
        $organization_id = $context['data']['organization_id'];
        $parent_id = $context['data']['parent_id'];  

        $prodGasPromotionsTable = TableRegistry::get('ProdGasPromotions');
        $results = $prodGasPromotionsTable->get($parent_id);

        // debug($results);
        if(empty($results))
          return false;

        $value = $value['year'].$value['month'].$value['day'];
        // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
        // $value2 = $results->data->i18nFormat('Ymd');        
        $value2 = $results->data_fine->format('Ymd');
        // debug('dateComparisonToParent '.$value.' '.$operator.' '.$value2);
        // debug(Validation::comparison($value, $operator, $value2));
        if (!Validation::comparison($value, $operator, $value2))
            return false;

        return true;  
    }    
}