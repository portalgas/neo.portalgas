<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ExportsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isManager'] && !$this->_user->acl['isSuperReferente'] && !$this->_user->acl['isReferentGeneric']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }    

    public function deliveries()
    {   
        $deliveriesTable = TableRegistry::get('Deliveries');
        $deliveries = $deliveriesTable->gets($this->_user, $this->_organization->id);
        $deliveries = $deliveriesTable->getsList($deliveries);    
        
        $exports = [];
        $exports['toDeliveryBySuppliers'] = 'Doc. con acquisti della consegna raggruppati per produttore';
        $exports['toDeliveryByUsers'] = 'Doc. con acquisti della consegna raggruppati per gasista';
        
        $this->set(compact('deliveries', 'exports'));
    }
}