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

    public function pdf($debug=false) { 

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

        $results = [];
        array_push($results, $this->request->getParam('pass'));
        array_push($results, $parameters = $this->request->getAttribute('params'));

        $this->set('results', $results);
    }     
}