<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;

class ReferentDocsExportController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $results = $this->Auths->ctrlRoute($this->_user, $this->request);
        if(!empty($results)) {
            $this->Flash->error($results['msg'], ['escape' => false]);
            if(!isset($results['redirect'])) 
                $results['redirect'] = Configure::read('routes_msg_stop');
            Log::error($this->request->params['controller'].'->'.$this->request->params['action'].' '.__('routes_msg_stop'));    
            return $this->redirect($results['redirect']);
        }
    }

    public function index($order_type_id, $order_id, $parent_id=0)
    {
        $ordersTable = TableRegistry::get('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id);

        $i = 0;
        $exports = [];
        switch($order_type_id) {
            case Configure::read('Order.type.gas_parent_groups'):
                $exports['toArticles'] = 'Doc. con gli articoli aggregati (per il produttore)';
                $exports['toArticlesDetailsGas'] = 'Doc. con gli articoli aggregati divisi per G.A.S.';
            break;
        }
        
        $this->set(compact('order', 'exports'));
    }
}