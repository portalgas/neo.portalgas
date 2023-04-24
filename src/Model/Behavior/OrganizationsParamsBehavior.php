<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class OrganizationsParamsBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * se va in conflitto, ex $organizationsTable->removeBehavior('OrganizationsParams');
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)  {
        
        // debug($event);exit;
        $results = $query->all();
		
        foreach ($results as $key => $result) {
        	if(isset($result->id)) {
				if(!empty($result->paramsConfig)) {
					$result->paramsConfig = json_decode($result->paramsConfig, true);
                    /*
                     * gestione parametri non valorizzati, ex x GAS SOCILAMARKET non c'e' ['hasDes']
                     */
                    if(!isset($result->paramsConfig['hasDes'])) {
                        $result->paramsConfig['hasDes']='N';
                    }
                }
				
                /*
                * configurazione preso dal template
                */
                if(!empty($result->template)) {
                    $result->paramsConfig['payToDelivery'] = $result->template['payToDelivery'];
                    $result->paramsConfig['orderForceClose'] = $result->template['orderForceClose'];
                    $result->paramsConfig['orderUserPaid'] = $result->template['orderUserPaid'];
                    $result->paramsConfig['orderSupplierPaid'] = $result->template['orderSupplierPaid'];
                    // non + ora dall'organization $paramsConfig['ggArchiveStatics'] = $organization->template['ggArchiveStatics'];
                    if(!isset($result->paramsConfig['ggArchiveStatics']))
                        $result->paramsConfig['ggArchiveStatics'] = $result->template['ggArchiveStatics'];
        
                } // end if(!empty($result->template))

				if(!empty($result->paramsFields))
					$result->paramsFields = json_decode($result->paramsFields, true);
				
				if(!empty($result->paramsPay))
					$result->paramsPay = json_decode($result->paramsPay, true);
			}
		}
    }

    /*
     * arriva vuoto perche' la validazione non accetta un array()
     */
    public function beforeSave(Event $event, EntityInterface $entity) {
       
        // debug($entity);
        
        if(!empty($entity->paramsConfig))
            $entity->paramsConfig = json_encode($entity->paramsConfig, true);
        if(!empty($entity->paramsFields))
            $entity->paramsFields = json_encode($entity->paramsFields, true);
        if(!empty($entity->paramsPay))
            $entity->paramsPay = json_encode($entity->paramsPay, true);
        
        // debug($entity);
    }     
}