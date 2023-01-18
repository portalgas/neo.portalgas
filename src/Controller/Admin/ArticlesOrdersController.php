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

        if(!isset($this->_user->acl)) { 
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

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
          
        $this->set(compact('order_type_id', 'order', 'ArticlesOrders', 'articles'));
    }
}
