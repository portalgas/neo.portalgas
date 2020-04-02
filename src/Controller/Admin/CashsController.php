<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CashsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    /* 
     * elenco produttori per gestione chi e' escluso dal prepagato
     */
    public function supplierOrganizationFilter()
    {   
        /* 
        // fractis
        $this->user->organization->paramsConfig['hasCashFilterSupplier']!='Y' || 
        */
        if(!$this->Auth->isManager($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }   

        $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');
        $results = $supplierOrganizationCashExcludedsTable->gets($this->user);

        $this->set(compact('results'));
    }
}