<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class GasGroupDeliveriesController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        
        if(!$this->_user->acl['isGasGroupsManagerParentOrders'] ||
           !$this->_user->acl['isGasGroupsManagerOrders']) {
            $this->_respondWithUnauthorized();
        }         

        if($this->_organization->paramsConfig['hasGasGroups']=='N') { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            $this->_respondWithUnauthorized();
        }        
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);        
    }
  
    /* 
     * elenco consegne del gruppo
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];

        $gas_group_id = $this->request->getData('gas_group_id');

        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
        $results = $gasGroupDeliveriesTable->getsActiveList($this->_user, $this->_organization->id, $gas_group_id);
        
        return $this->_response($results);
    }     
}