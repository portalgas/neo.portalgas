<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class ExportsController extends AppController {
    
    use Traits\UtilTrait;

    /*
     * se true non stampa il pdf ma lo vedo a video
     */ 
    private $_debug = true; 

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Order');
        $this->loadComponent('Storeroom');
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
    public function userCart($delivery_id, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $debug = false;
        $results = [];
    
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($user, $organization_id, $delivery_id);
        $title = "Carrello della consegna ".$delivery->label;
        $filename = $this->setFileName($title.'.pdf');

        Configure::write('CakePdf', [
            'engine' => 'CakePdf.DomPdf', // 'CakePdf.WkHtmlToPdf',
            'margin' => [
                'bottom' => 15,
                'left' => 50,
                'right' => 30,
                'top' => 45
            ],
            'orientation' => 'portrait', // landscape (orizzontale) portrait (verticale)
            'download' => true,
            'filename' => $filename
        ]);

        $options = [];
        $options['sql_limit'] = Configure::read('sql.no.limit');

        $results = $this->Order->userCartGets($user, $organization_id, $delivery_id, $debug); 
        // debug($results); 

        /*
         * storerooms
         */
        $storeroomResults = [];
        if ($user->organization->paramsConfig['hasStoreroom'] == 'Y' && $user->organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {
            $storeroomResults = $this->Storeroom->getArticlesByDeliveryId($user, $organization_id, $delivery_id, $options=[], $debug);            
        }

        $this->set(compact('results', 'storeroomResults', 'delivery', 'title', 'user'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/user_cart');
        } 
        else {
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }     
}