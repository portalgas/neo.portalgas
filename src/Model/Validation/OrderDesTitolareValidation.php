<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use Authentication\AuthenticationService;
use App\Traits;

class OrderDesTitolareValidation extends Validation
{    
    use Traits\SqlTrait;
    use Traits\UtilTrait;

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

    public function dateDeliverytoDataFineMaxOrderDes($value, $context)
    { 
        // dd($context);  
        $organization_id = $context['data']['organization_id'];
        $delivery_id = $context['data']['delivery_id']; 
        $data_fine_max = $context['data']['data_fine_max']; 
        
        $service = new AuthenticationService();
        $identify = $service->getIdentity();
        if(!empty($identify)) //se chiamato dal cron non e' valorizzato
            $user = $identify->getIdentifier();
        else 
            $user = null; // $this->createObjUser(); Using $this when not in object context 

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($user, $organization_id, $delivery_id);

        if($delivery->sys=='Y') // consegna Da definire
            return true;

        $operator = '>=';
        $value = self::dateFrozenToArray($data_fine_max); 
        $value = $value['year'].$value['month'].$value['day'];
        // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
        // $value2 = $results->data->i18nFormat('Ymd');        
        $value2 = $delivery->data->format('Ymd');
        // debug('dateComparisonToDelivery '.$value.' '.$operator.' '.$value2);
        // debug(Validation::comparison($value, $operator, $value2));
        if (!Validation::comparison($value, $operator, $value2))
            return false;
    
        return true;              
    }
}