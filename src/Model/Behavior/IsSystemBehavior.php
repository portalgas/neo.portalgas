<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class IsSystemBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options) {

        if($entity->is_system) {
            $event->stopPropagation();
            return false;
        }
        
        return true;
    }  
}