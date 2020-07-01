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
        // debug($where);

        $results = $deliveriesTable->find()
                            ->select(['data', 'sys'])
                            ->where($where)
                            ->first();

        // debug($results);
		if($results->sys=='Y') // consegna Da definire
        	return true;

        $value = $value['year'].$value['month'].$value['day'];
        $value2 = $results->data->format('Ymd');
        if (!Validation::comparison($value, $operator, $value2))
            return false;

		return true; 	
    }

    /*
     * ctrl che il produttore abbia articoli validi da associare all'ordine
     */
    public static function totArticles($value, $context)
    {
       // debug($context); 	
        $organization_id = $context['data']['organization_id'];
        $supplier_organization_id = $context['data']['supplier_organization_id']; 
        
        // $user = $this->createObjUser(['organization_id' => $organization_id]);
        $user = new \stdClass();
        $user->organization = new \stdClass();
        $user->organization->id = $organization_id;

        $articlesTable = TableRegistry::get('Articles');
        $results = $articlesTable->getTotArticlesPresentiInArticlesOrder($user, $organization_id, $supplier_organization_id);

        // debug($results);
        if($results->count()==0)
            return false;
        else
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
         */
        $type_draws = ['SIMPLE', 'COMPLETE'];

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
}