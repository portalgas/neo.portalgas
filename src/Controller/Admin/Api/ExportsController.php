<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ExportsController extends AppController {
    
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
            'orientation' => 'landscape',
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

        $this->set('results', $results);
    }     
}