<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleOrderDecorator;
use App\Decorator\ApiSupplierDecorator;

/*
 * sostituisce htmlArticleOrdersController, l'html nella view non poteva contenere js
 */
class ArticleOrdersController extends ApiAppController
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

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $datas = [];
        $datas['order'] = [];
        $datas['articlesOrder'] = [];
        $datas['cart'] = [];

        $orderResults = [];
        $articlesOrdersResults = [];

        $order_id = $this->request->getData('order_id');
        $article_organization_id = $this->request->getData('article_organization_id');
        $article_id = trim($this->request->getData('article_id'));

        $ordersTable = TableRegistry::get('Orders');
        
        $ordersTable = $ordersTable->factory($user, $organization_id, 0, $order_id);

        $ordersTable->addBehavior('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);
        if(!empty($orderResults)) {
            $supplier = $orderResults['suppliers_organization']['supplier'];
            $supplier = new ApiSupplierDecorator($user, $supplier);
            $orderResults['suppliers_organization']['supplier'] = $supplier->results;
        }

        $ids = [];
        $ids['organization_id'] = $organization_id;
        $ids['order_id'] = $order_id;
        $ids['article_organization_id'] = $article_organization_id;
        $ids['article_id'] = $article_id;
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($user, $organization_id, $orderResults);

        if($articlesOrdersTable!==false) {
            $articlesOrdersResults = $articlesOrdersTable->getByIds($user, $organization_id, $ids, $debug);

            $articlesOrdersResults2 = new ApiArticleOrderDecorator($user, $articlesOrdersResults, $orderResults);
            $articlesOrdersResults = $articlesOrdersResults2->results;
        }

        $datas['order'] = $orderResults;
        $datas['articlesOrder'] = $articlesOrdersResults;

        /*
         * nota per il referente
         */
        $hasFieldCartNote = $user->organization->paramsFields['hasFieldCartNote'];
        
        if($hasFieldCartNote=='Y') {
            
            $nota = '';
            $cartsTable = TableRegistry::get('Carts');

            $where = ['Carts.organization_id' => $organization_id,
                      'Carts.order_id' => $order_id,
                      'Carts.user_id' => $user->id,
                      'Carts.article_organization_id' => $article_organization_id,
                      'Carts.article_id' => $article_id];
            // debug($where);

            $cartResults = $cartsTable->find()
                                        ->select(['nota'])
                                        ->where($where)
                                        ->first();
            if(!empty($cartResults))
                $datas['cart']['nota'] = $cartResults['nota'];
            else {
                /*
                 * l'articolo dev'essere prima acquitato
                 */
                $hasFieldCartNote = 'N';
                $datas['cart']['nota'] = '';
            }

        } // end if($hasFieldCartNote=='Y')
        $datas['cart']['hasFieldCartNote'] = $hasFieldCartNote;

        $results['results'] = $datas;
        
        return $this->_response($results); 
    } 
}