<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class DataStateBehavior extends Behavior
{
    private $config = [];

    public function initialize(array $config)
    {
        $this->config = $config;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        $this->data($entity);
    }  
    
    public function data(EntityInterface $entity)
    {
        // if(!isset($entity->id)) {
        if($entity->isNew()) {
            /*
             * insert NEW
             */
            $entity->set('data_state', date('Y-m-d'));
        }
        else {
            /*
             * update solo se e' cambiato {model}_state_id
             */
            $table = TableRegistry::get($entity->source());
            $oldRecord = $table->get($entity->id);

            $field = '';
            switch ($entity->source()) {
                case 'OfferDetails':
                    $field = 'offer_detail_state_id';
                    break;
                case 'QuoteDetailCalendars':
                    $field = 'quote_detail_calendar_state_id';
                    break;
            }

            if(!empty($field) && $oldRecord->{$field} != $entity->{$field}) {
                $entity->set('data_state', date('Y-m-d'));
            }
        }
    }
}