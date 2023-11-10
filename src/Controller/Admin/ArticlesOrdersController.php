<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * ArticlesOrders Controller
 *
 * @property \App\Model\Table\ArticlesOrdersTable $ArticlesOrders
 *
 * @method \App\Model\Entity\ArticlesOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $results = null;
        if(!empty($this->_user))
            $results = $this->Auths->ctrlRoute($this->_user, $this->request);
        
        if(!empty($results)) {
            $this->Flash->error($results['msg'], ['escape' => false]);
            if(!isset($results['redirect'])) 
                $results['redirect'] = Configure::read('routes_msg_stop');
            return $this->redirect($results['redirect']);
        }
    }

    public function index($order_type_id, $order_id)
    {
        $debug = false;

        $ordersTable = TableRegistry::get('Orders');    
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);

        $ArticlesOrders = [];
        $articles = [];
         
        $time = $this->request->getQuery('time');

        // ordine appena creato, ora associo gli articoli
        $previousOrder = [];
        if($time=='first') {
            // dati ordine precedente
            if(in_array($order_type_id, [Configure::read('Order.type.gas'), Configure::read('Order.type.gas_parent_groups')]))
                $previousOrder = $ordersTable->getPrevious($this->_user, $order);

        }
       
        $this->set(compact('order_type_id', 'order', 'previousOrder', 'ArticlesOrders', 'articles', 'time'));
    }
}