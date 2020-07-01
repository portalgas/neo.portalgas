<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class OrdersBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
        $this->config = $config;
    }

    /*
     * https://book.cakephp.org/4/en/orm/saving-data.html#before-marshal
     * modify request data before it is converted into entities
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        // debug('OrdersBehavior beforeMarshal');

        /*
         * valor di default
         */
        if (!isset($data['state_code']) || empty($data['state_code'])) {
            $data['state_code'] = 'CREATE-INCOMPLETE';
        }
        if (!isset($data['mail_open_testo']) || empty($data['mail_open_testo'])) {
            $data['mail_open_testo'] = '';
        }
        if (!isset($data['tot_importo']) || empty($data['tot_importo'])) {
            $data['tot_importo'] = 0;
        }
        if (!isset($data['qta_massima']) || empty($data['qta_massima'])) {
            $data['qta_massima'] = 0;
        }
        if (!isset($data['importo_massimo']) || empty($data['importo_massimo'])) {
            $data['importo_massimo'] = 0;
        }
        if (!isset($data['tesoriere_fattura_importo']) || empty($data['tesoriere_fattura_importo'])) {
            $data['tesoriere_fattura_importo'] = 0;
        }
        if (!isset($data['tesoriere_importo_pay']) || empty($data['tesoriere_importo_pay'])) {
            $data['tesoriere_importo_pay'] = 0;
        }

        // debug($data);
    }

    public function beforeSave(Event $event, EntityInterface $entity) {

        // debug('OrdersBehavior beforeSave');
        // debug($entity);
    
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
            $results = $suppliersOrganizationsTable->find()
                            ->select(['SuppliersOrganizations.owner_articles', 
                                      'SuppliersOrganizations.owner_organization_id', 
                                      'SuppliersOrganizations.owner_supplier_organization_id'])
                            ->where($where)
                            ->first();

            $entity->owner_articles = $results->owner_articles;
            $entity->owner_organization_id = $results->owner_organization_id;
            $entity->owner_supplier_organization_id = $results->owner_supplier_organization_id;           
        }
        
        // debug($entity);
    }     
}