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
use Authentication\AuthenticationService;
use App\Traits;

class OrdersBehavior extends Behavior
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

	private $config = [];
    private $_user; 

    public function initialize(array $config)
    {
        $this->config = $config;

        $service = new AuthenticationService();
        $identify = $service->getIdentity();
        if(!empty($identify)) //se chiamato dal cron non e' valorizzato
            $this->_user = $identify->getIdentifier();
        else 
            $this->_user = $this->createObjUser();
    }

    /*
     * https://book.cakephp.org/4/en/orm/saving-data.html#before-marshal
     * modify request data before it is converted into entities
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        // debug('OrdersBehavior beforeMarshal');

        $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');

        /*
         * valore di default
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
            $data['qta_massima_um'] = '';
            $data['send_mail_qta_massima'] = 'N';
        }
        else 
            $data['send_mail_qta_massima'] = 'Y';

        if (!isset($data['importo_massimo']) || empty($data['importo_massimo'])) {
            $data['importo_massimo'] = 0;
            $data['send_mail_importo_massimo'] = 'N';  
        }
        else 
            $data['send_mail_importo_massimo'] = 'Y'; 

        if (!isset($data['tesoriere_fattura_importo']) || empty($data['tesoriere_fattura_importo'])) {
            $data['tesoriere_fattura_importo'] = 0;
        }
        if (!isset($data['tesoriere_importo_pay']) || empty($data['tesoriere_importo_pay'])) {
            $data['tesoriere_importo_pay'] = 0;
        }

        if(isset($this->_user) && $this->_user->organization->paramsConfig['hasTrasport']=='N')
            $data['hasTrasport'] = 'N';
        else 
        if (!isset($data['hasTrasport']) || empty($data['hasTrasport'])) {
            $data['hasTrasport'] = 'N';
        }

        if(isset($this->_user) && $this->_user->organization->paramsConfig['hasCostMore']=='N')
            $data['hasCostMore'] = 'N';
        else         
        if (!isset($data['hasCostMore']) || empty($data['hasCostMore'])) {
            $data['hasCostMore'] = 'N';
        }

        if(isset($this->_user) && $this->_user->organization->paramsConfig['hasCostLess']=='N')
            $data['hasCostLess'] = 'N';
        else         
        if (!isset($data['hasCostLess']) || empty($data['hasCostLess'])) {
            $data['hasCostLess'] = 'N';
        }
        
        if (!isset($data['mail_open_send']) || empty($data['mail_open_send'])) {
            $ordersTable = TableRegistry::get('Orders');
            $data['mail_open_send'] = $ordersTable->setOrderMailOpenSend($data);
        }

        if (!isset($data['order_type_id']) || empty($data['order_type_id'])) {
            $data['order_type_id'] = $lifeCycleOrdersTable->getType($this->_user, $data);
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