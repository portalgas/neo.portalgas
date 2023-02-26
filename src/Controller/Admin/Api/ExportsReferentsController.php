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
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function get($debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $delivery_id = 10517;
        $debug = false;
        $results = [];
        $title = '';

        $order_type_id = $this->request->getData('order_type_id');
        $order_id = $this->request->getData('order_id');
        $print_id = $this->request->getData('print_id');
        $format = $this->request->getData('format');

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($user, $organization_id, $delivery_id);
        if(!empty($delivery)) {
            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

            $results = $this->Order->userCartGets($user, $organization_id, $delivery_id, $debug); 
            // debug($results);
        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'delivery', 'title', 'user'));

        switch($format) {
            case 'HTML':
                $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
                // ordine e' viewBuilder / render
                $this->viewBuilder()->setLayout('ajax');
                $this->render('/Admin/Api/ExportsReferents/pdf/get');
            break;
            case 'PDF':
               // $title = "Carrello della consegna ".$delivery->label.' di '.$user->username;
               // $filename = $this->setFileName($title.'.pdf');
    
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
                    // 'filename' => $filename // This can be omitted if you want file name based on URL.
                ]);
                $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
                
                // ordine e' render / viewBuilder
                // $this->layout = 'pdf/default';
                // $this->render('/Admin/Api/ExportsReferents/pdf/get');
                // $this->viewBuilder()->setClassName('CakePdf.Pdf');
                
            break;
        }            
    }         
}