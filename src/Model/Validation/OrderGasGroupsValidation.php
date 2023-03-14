<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderGasGroupsValidation extends Validation
{  
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    /*
     * ctrl l'ordine padre abbia articoli associati all'ordine
     */
    public static function totArticles($value, $context)
    {
        // debug($context);
        $organization_id = $context['data']['organization_id'];
        $parent_id = $context['data']['parent_id']; 

        $where = ['organization_id' => $organization_id,
                  'order_id' => $parent_id,
                  'stato !=' => 'N'];
             
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $totale = $articlesOrdersTable->find()
                        ->where($where)
                        ->count();
        if($totale==0)
            return false;
        else
            return true;  
    }

    public function dateFine($value, $context)
    { 
        // debug($context);  

        $operator = '<=';
    	$value = $context['data']['data_fine']; 
        $value2 = $context['data']['parent_data_fine']; // 15/03/2023
        
        if(empty($value2))
            return false;

        list($day, $month, $year) = explode('/', $value2);
          
        $value = $value['year'].$value['month'].$value['day'];
        $value2 = $year.$month.$day;
        if (!Validation::comparison($value, $operator, $value2))
            return false;
    
        return true;           
    }    
}