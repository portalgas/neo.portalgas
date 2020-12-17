<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;

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

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $results = [];
        $results['order'] = [];
        $results['articlesOrder'] = [];

        $order_id = $this->request->getData('order_id');
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = trim($this->request->getData('article_id'));

        $ordersTable = TableRegistry::get('Orders');
        
        $ordersTable = $ordersTable->factory($user, $organization_id, 0, $order_id);

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        $ids = [];
        $ids['organization_id'] = $organization_id;
        $ids['order_id'] = $order_id;
        $ids['article_organization_id'] = $article_organization_id;
        $ids['article_id'] = $article_id;
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersResults = $articlesOrdersTable->getByIds($user, $organization_id, $ids, $debug);

        $results = new ApiArticleOrderDecorator($user, $articlesOrdersResults, $orderResults);
        $articlesOrdersResults = $results->results;

        $results['order'] = $orderResults;
        $results['articlesOrder'] = $articlesOrdersResults;
        $this->set(compact('results'));

        $this->viewBuilder()->setLayout('ajax');
    } 
}