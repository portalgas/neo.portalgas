<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class IsDefaultEndBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
    	
        if($entity->is_default_ini) {
        	
        	$conditions = ['id != ' => $entity->id];
        	$where = ['is_default_end' => 0];

            $table = TableRegistry::get($entity->source());
            $table->updateAll($where, $conditions);
        }
		
        return true;
    }    
}