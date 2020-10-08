<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticleDecorator;
use App\Decorator\ApiArticleDecorator;

class OrdersPromotionController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Cart');
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * front-end - estrae gli articoli associati ad un ordine in promozione ed evenuali acquisti per user  
     */
    public function getCarts() {

        $debug = false;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        $results = [];
   
        /*
         * ottengo l'ordine in promozione
         */
        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');
        $where_promotion = ['ProdGasPromotionsOrganizations.organization_id' => $user->organization->id,
                  'ProdGasPromotionsOrganizations.state_code' => 'OPEN'];
        $prodGasPromotionsOrganizationsResults = $prodGasPromotionsOrganizationsTable->find()
                                    ->where($where_promotion)
                                    ->first();
        if($debug) debug($where_promotion);
        if($debug) debug($prodGasPromotionsOrganizationsResults);

        if(!empty($prodGasPromotionsOrganizationsResults)) {
            $order_id = $prodGasPromotionsOrganizationsResults->order_id;

            $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
            $articlesOrdersTable = $articlesOrdersTable->factory($user, $user->organization->id, $order_id);

            if($articlesOrdersTable!==false) {
                $where['order_id'] = $order_id;
                $order = [];
                $results = $articlesOrdersTable->getCarts($user, $user->organization->id, $user->id, $where, $order);
            
                if(!empty($results)) {
                    $results = new ApiArticleDecorator($results);
                    //$results = new ArticleDecorator($results);
                    $results = $results->results;
                }
            }

        } // if(!empty($prodGasPromotionsOrganizationsResults))

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 

    public function managementCart() {
        
        $debug = true;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();

        // debug($article);

        $results = [];
   
        $article = $this->request->getData('article');
        $results = $this->Cart->managementCart($user, $user->organization->id, $article, $debug);
        
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}