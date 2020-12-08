<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ExportsController extends AppController {
    
    /*
     * se true non stampa il pdf ma lo vedo a video
     */ 
    private $_debug = false; 

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Order');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        if(!$this->_debug) 
            $this->viewBuilder()->setClassName('CakePdf.Pdf');
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function pdf($delivery_id, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }
        Configure::write('CakePdf', [
            'engine' => 'CakePdf.DomPdf', // 'CakePdf.WkHtmlToPdf',
            'margin' => [
                'bottom' => 15,
                'left' => 50,
                'right' => 30,
                'top' => 45
            ],
            'orientation' => 'landscape', // portrait
            'download' => true,
            'filename' => 'Invoice.pdf'
        ]);

        $debug = false;
        $results = [];
    
        $options = [];
        $options['sql_limit'] = Configure::read('sql.no.limit');

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $results = $this->Order->userCartGets($user, $organization_id, $delivery_id, $debug); 
        // debug($results); 

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($user, $organization_id, $delivery_id);
        $title = "Carrello della consegna ".$delivery->label;

        $this->set(compact('results', 'delivery', 'title'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/pdf');
        } 
        else {
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }     
}