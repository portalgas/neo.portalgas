<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class OrdersBehavior extends TreeBehavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    public function beforeSave(Event $event, EntityInterface $entity) {
       
        //debug($entity);
    
        if($entity->id) {
            /*
             * update
             */
        } else {
            /*
             * insert
             *
             * riporto SuppliersOrganization owner_articles / owner_organization_id / owner_supplier_organization_id 
             * cosi' se vengono cambiati rimangono legati all'ordine
             */
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $where = ['SuppliersOrganizations.organization_id' => $entity->organization_id,
                      'SuppliersOrganizations.id' => $entity->supplier_organization_id];
            // debug($where);
            $results = $this->find()
                            ->select(['SuppliersOrganizations.owner_articles', 'SuppliersOrganizations.owner_organization_id', 'SuppliersOrganizations.owner_supplier_organization_id'])
                            ->where($where)
                            ->first();

            $entity->owner_articles = $results->owner_articles;
            $entity->owner_organization_id = $results->owner_organization_id;
            $entity->owner_supplier_organization_id = $results->owner_supplier_organization_id;
        }
        
        // debug($entity);
    }     
}