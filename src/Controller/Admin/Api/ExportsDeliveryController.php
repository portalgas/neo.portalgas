<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\Export\OrdersGasParentDecorator;
use App\Decorator\Export\OrdersGasParentGroupsToArticlesDecorator;
use App\Decorator\Export\OrdersGasParentGroupsToArticlesByGroupsDecorator;
use App\Decorator\Export\OrdersGasParentGroupsToUsersArticlesByGroupsDecorator;

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Http\CallbackStream; 


class ExportsDeliveryController extends AppController {
    
    use Traits\UtilTrait;

    private $_filename = '';

    public function initialize(): void
    {
        parent::initialize();

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
            case 'XLSX':
                $this->viewBuilder()->disableAutoLayout();
                $this->response = $this->response->withDownload($this->_filename);            
            break;
            case 'PDF':
                $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                    ->setTemplate('/Admin/Api/ExportsDelivery/pdf/'.$print_id) 
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
        $delivery_id = $this->request->getData('delivery_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        /*
         * opzioni di stampa
         */
        $opts = $this->request->getData('opts');
        $this->set(compact('opts'));

        $method = '_'.$print_id;
        $results = $this->{$method}($format, $delivery_id);

        $this->set(compact('delivery_id', 'format'));
        
        switch($format) {
            case 'HTML':
                $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
                $this->render('/Admin/Api/ExportsDelivery/pdf/'.$print_id);
            break;
            case 'XLSX':
                $this->render('/Admin/Api/ExportsDelivery/xlsx/'.$print_id);
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
    
    // Doc. con acquisti della consegna raggruppati per produttore
    private function _toDeliveryBySuppliers($format, $delivery_id, $debug=false) {
        
        /* 
        * dati consegna
        */
        $deliveriesTable = TableRegistry::get('Deliveries');
        $where = ['Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.id' => $delivery_id];
        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();
        foreach($delivery->orders as $numResult => $order) {

            if($numResult==0) {
                $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
                $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
            }

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');        
            $orders = $articlesOrdersTable->getCartsByOrder($this->_user, $this->_organization->id, $order, [], $options);
            $article_orders = new OrdersGasParentGroupsToArticlesDecorator($this->_user, $orders);
            
        } // foreach($delivery->orders as $order) 

        $this->set(compact('article_orders', 'order'));

        $this->_filename = 'acquisti-aggregati-articoli';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
            break;
        }

        return true;
    } 
    
    // Doc. con acquisti della consegna raggruppati per gasista
    private function _toDeliveryByUsers($format, $delivery_id, $debug=false) {
 
        /* 
        * dati consegna
        */
        $deliveriesTable = TableRegistry::get('Deliveries');
        $where = ['Deliveries.organization_id' => $this->_organization->id,
                    'Deliveries.id' => $delivery_id];
        $delivery = $deliveriesTable->find()
                                ->contain(['Orders' => [
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    ]])
                                ->where($where)
                                ->first();
        foreach($delivery->orders as $numResult => $order) {

            if($numResult==0) {
                $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
                $articlesOrdersTable = $articlesOrdersTable->factory($this->_user, $this->_organization->id, $order);
            }

            $options = [];
            $options['sort'] = [];
            $options['limit'] = Configure::read('sql.no.limit');        
            $orders = $articlesOrdersTable->getCartsByOrder($this->_user, $this->_organization->id, $order, [], $options);
            $article_orders = new OrdersGasParentGroupsToArticlesDecorator($this->_user, $orders);
                        
        } // foreach($delivery->orders as $numResult => $order) 
 
        $this->set(compact('order'));

        $this->_filename = 'acquisti-aggregati-articoli-per-gruppi';
        switch($format) {
            case 'XLSX':
                $this->_filename .= '.xlsx';
            break;
            case 'PDF':
                $this->response->header('filename', $this->_filename.'.pdf');
            break;
        }
       
        return true;
    }
}