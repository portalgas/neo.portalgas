<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

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
        $format = $this->request->getData('format');
        switch($format) {
            case 'HTML':
                $this->viewBuilder()->setLayout('ajax');
            break;
            case 'PDF':
                $this->viewBuilder()->setClassName('CakePdf.Pdf');
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
        $results = [];
        
        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        $method = '_'.$print_id;
        $results = $this->{$method}($order_type_id, $order_id);

        switch($format) {
            case 'HTML':
                $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
                // ordine e' viewBuilder / render
                $this->render('/Admin/Api/ExportsReferents/pdf/get');
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
    
    private function _toArticlesDetailsGas($order_type_id, $order_id, $debug=false) {
        
        $delivery_id = 10517;

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($this->_user, $this->_organization->id, $delivery_id);
        if(!empty($delivery)) {
            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

            $results = $this->Order->userCartGets($this->_user, $this->_organization->id, $delivery_id, $debug); 
            // debug($results);
        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'delivery', 'title', 'user'));
    }

    private function _toArticles($order_type_id, $order_id, $debug=false) {
        
        $delivery_id = 10517;

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($this->_user, $this->_organization->id, $delivery_id);
        if(!empty($delivery)) {
            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

            $results = $this->Order->userCartGets($this->_user, $this->_organization->id, $delivery_id, $debug); 
            // debug($results);
        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'delivery', 'title', 'user'));
    }    
}