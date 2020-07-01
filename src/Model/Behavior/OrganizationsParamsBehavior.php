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
				if(!empty($result->paramsConfig))
					$result->paramsConfig = json_decode($result->paramsConfig, true);
				
				if(!empty($result->paramsFields))
					$result->paramsFields = json_decode($result->paramsFields, true);
				
				if(!empty($result->paramsPay))
					$result->paramsPay = json_decode($result->paramsPay, true);
			}
		}
    }

    public function beforeSave(Event $event, EntityInterface $entity) {
       
        //debug($entity);
        
        $entity->paramsConfig = json_encode($entity->paramsConfig, true);
        $entity->paramsFields = json_encode($entity->paramsFields, true);
        $entity->paramsPay = json_encode($entity->paramsPay, true);
        
        // debug($entity);
    }     
}