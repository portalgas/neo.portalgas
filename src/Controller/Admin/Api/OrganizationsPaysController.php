<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class OrganizationsPaysController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('OrganizationsPay');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * setta il messaggio (Organizations.hasMsg) da far comparire al Manager / Tesoriere
     * 
     * codice dinamico come App\Controller\Admin\Api\AjaxsController::fieldUpdate 
     */
    public function setMsgText() {

        $debug = false;
        $results = [];

        $organization_pay_id = $this->request->getData('organization_pay_id');
        $value = $this->request->getData('value');

        $results = $this->OrganizationsPay->setMsgText($this->Authentication->getIdentity(), $organization_pay_id, $value);
        if($results['code']!=200) {
            $this->_respondWithValidationErrors();
        }

        // $this->set('_serialize', ['code', 'message', 'errors']);
        
        return $this->_response($results);
    }
}