<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\Export\OrdersGasParentGroupsToArticlesDecorator;
use App\Decorator\Export\OrdersGasParentGroupsToArticlesByGroupsDecorator;

class ExportsReferentsController extends AppController {
    
    use Traits\UtilTrait;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Order');

        /*
         * non si puo' disabilitare nel controller
         * settarlo in .env export DEBUG="false" 
         * Configure::write('debug', 0);
         */
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    
        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');
        
        switch($format) {
            case 'HTML':
                $this->viewBuilder()->setLayout('ajax');
            break;
            case 'PDF':
             $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                    ->setTemplate('/Admin/Api/ExportsReferents/pdf/'.$print_id) 
                    ->setLayout('../../Layout/pdf/default') 
                    ->setClassName('CakePdf.Pdf');             
            break;
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function get($debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }
                
        $debug = false;
        
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        $method = '_'.$print_id;
        $results = $this->{$method}($order_type_id, $order_id);

        $this->set(compact('order_type_id', 'format'));

        switch($format) {
            case 'HTML':
                $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
                $this->render('/Admin/Api/ExportsReferents/pdf/'.$print_id);
            break;
            case 'PDF':
                Configure::write('CakePdf', [
                    'engine' => 'CakePdf.DomPdf', // 'CakePdf.WkHtmlToPdf',
                    'margin' => [
                        'bottom' => 15,
                        'left' => 50,
                        'right' => 30,
                        'top' => 45
                    ],
                    'orientation' => 'portrait', // landscape (orizzontale) portrait (verticale)
                    'download' => false, // This can be omitted if "filename" is specified.
                ]);
                $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
            break;
        }    
    }   
    
    // Doc. con gli articoli aggregati divisi per i Gruppi
    private function _toArticlesDetailsByGroups($order_type_id, $order_id, $debug=false) {
 
        /* 
        * dati ordine 
        */
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Orders.organization_id' => $this->_organization->id,
                    'Orders.order_type_id' => $order_type_id,
                    'Orders.id' => $order_id];
        $orderParent = $ordersTable->find()
                                ->where($where)
                                ->first();

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $orderParent);

        $orders = $articlesOrdersTable->getCartsByOrder($this->_user, $this->_organization->id, $orderParent);
        switch($order_type_id) {
            case Configure::read('Order.type.gas_parent_groups'):
                $orders = new OrdersGasParentGroupsToArticlesByGroupsDecorator($this->_user, $orders);
                $orders = $orders->results;
            break;
            default:
                $orders = [];
            break;
        }

        $this->set(compact('orders', 'orderParent'));

        $this->response->header('filename', 'toArticlesDetailsGas.pdf');

        return true;
    }
    
    // Doc. con gli articoli aggregati (per il produttore)
    private function _toArticles($order_type_id, $order_id, $debug=false) {
        
        /* 
        * dati ordine 
        */
        $ordersTable = TableRegistry::get('Orders');
        $where = ['Orders.organization_id' => $this->_organization->id,
                    'Orders.order_type_id' => $order_type_id,
                    'Orders.id' => $order_id];
        $orderParent = $ordersTable->find()
                                ->where($where)
                                ->first();

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $orderParent);

        $orders = $articlesOrdersTable->getCartsByOrder($this->_user, $this->_organization->id, $orderParent);
        switch($order_type_id) {
            case Configure::read('Order.type.gas_parent_groups'):
                $article_orders = new OrdersGasParentGroupsToArticlesDecorator($this->_user, $orders);
                $article_orders = $article_orders->results;
            break;
            default:
                $article_orders = [];
            break;
        }

        $this->set(compact('article_orders', 'orderParent'));

        $this->response->header('filename', 'toArticles.pdf');

        return true;
    }    
}