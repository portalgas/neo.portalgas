<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class UserProfilesBehavior extends Behavior
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
		
        /*
         * tolgo da profile_value ""
         */
        foreach ($results as $key => $result) {
            $value = substr($result->profile_value, 1, (strlen($result->profile_value)-2));
            $result->profile_value = $value;
		}
    }
     
}