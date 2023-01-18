<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderGasValidation extends Validation
{    
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
        $results = $articlesTable->getsToArticleOrders($user, $organization_id, $supplier_organization_id);

        // debug($results);
        if($results->count()==0)
            return false;
        else
        return true;  
    }  
}