<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderGasGroupsValidation extends Validation
{  
    public function __construct()
    {
        parent::__construct();
    }
  
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
}