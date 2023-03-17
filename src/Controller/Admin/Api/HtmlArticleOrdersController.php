<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;

/*
 * sostituito da ArticleOrdersController, l'html nella view non poteva contenere js
 */
class HtmlArticleOrdersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Cart');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }
    }

    /* 
     * front-end - dettaglio articolo associato ad un ordine   
     */
    public function get() {

        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $orderResults = [];
        $articlesOrdersResults = [];

        $results = [];
        $results['order'] = [];
        $results['articlesOrder'] = [];

        $order_id = $this->request->getData('order_id');
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = trim($this->request->getData('article_id'));

        $ordersTable = TableRegistry::get('Orders');
        
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, 0, $order_id);
        if($ordersTable===false) {
            return false;
        }

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);

        $ids = [];
        $ids['organization_id'] = $this->_organization->id;
        $ids['order_id'] = $order_id;
        $ids['article_organization_id'] = $article_organization_id;
        $ids['article_id'] = $article_id;
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $orderResults);

        if($articlesOrdersTable!==false) {
            $articlesOrdersResults = $articlesOrdersTable->getByIds($this->_user, $this->_organization->id, $ids, $debug);

            $results = new ApiArticleOrderDecorator($this->_user, $articlesOrdersResults, $orderResults);
            $articlesOrdersResults = $results->results;
        }

        $results['order'] = $orderResults;
        $results['articlesOrder'] = $articlesOrdersResults;
        $this->set(compact('results'));

        /*
         * nota per il referente
         */
        $hasFieldCartNote = $this->_user->organization->paramsFields['hasFieldCartNote'];
        $this->set('hasFieldCartNote', $hasFieldCartNote);
        
        if($hasFieldCartNote=='Y') {
            
            $nota = '';
            $cartsTable = TableRegistry::get('Carts');

            $where = ['Carts.organization_id' => $this->_organization->id,
                      'Carts.order_id' => $order_id,
                      'Carts.user_id' => $this->_user->id,
                      'Carts.article_organization_id' => $article_organization_id,
                      'Carts.article_id' => $article_id];
            // debug($where);

            $cartResults = $cartsTable->find()
                                        ->select(['nota'])
                                        ->where($where)
                                        ->first();
            if(!empty($cartResults))
                $nota = $cartResults['nota'];

            $this->set(compact('nota'));
        } // end if($hasFieldCartNote=='Y')

        $this->viewBuilder()->setLayout('ajax');
    } 

    /* 
     * dato un articolo estraggo tutti gli acquisti
     * associazione articoli a ordine
     */
    public function getCartsByArticles() {

        $debug = false;

        $organization_id = $this->request->getData('organization_id');
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');        
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = $this->request->getData('article_id');

        $ordersTable = TableRegistry::get('Orders');
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id, $debug);
        
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
        if($articlesOrdersTable===false) {
            return false;
        } 

        $where = [];
        $where['ArticlesOrders'] = ['article_organization_id' => $article_organization_id,
                                    'article_id' => $article_id];
        $articlesOrdersResults = $articlesOrdersTable->getCartsByArticles($this->_user, $this->_organization->id, $order, $where, $options=[], $debug);
        $results = new ApiArticleOrderDecorator($this->_user, $articlesOrdersResults, $order);
        $results = $results->results;

        $this->set(compact('results'));

        $this->viewBuilder()->setLayout('ajax');

        switch($order_type_id) {
            case Configure::read('Order.type.gas'):
            case Configure::read('Order.type.gas_groups'):
                $this->viewBuilder()->setTemplate('/Admin/Api/HtmlArticleOrders/carts_by_article_order_gas');
            break;
            case Configure::read('Order.type.gas_parent_groups'):
                $this->viewBuilder()->setTemplate('/Admin/Api/HtmlArticleOrders/carts_by_article_order_gas_parent_groups');
            break;    
        }    
    }
}