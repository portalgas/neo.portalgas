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
    private $_debug = false;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Order');
        $this->loadComponent('Storeroom');
        $this->loadComponent('ProdGasPromotion');
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
        $storeroomResults = [];
        $title = '';

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($user, $organization_id, $delivery_id);
        if(!empty($delivery)) {
            
            $title = "Carrello della consegna ".$delivery->label.' di '.$user->username;
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
            if ($user->organization->paramsConfig['hasStoreroom'] == 'Y' && $user->organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {
                $storeroomResults = $this->Storeroom->getArticlesByDeliveryId($user, $organization_id, $delivery_id, $options=[], $debug);            
            }

        } // end if(!empty($delivery))
        
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

    public function userPromotionCart($debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $debug = false;
        $results = [];
        $storeroomResults = [];
        $title = '';

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $organization_id = $this->Authentication->getIdentity()->organization->id;
        $user_id = $this->Authentication->getIdentity()->id;
        $user = $this->Authentication->getIdentity();

        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN', 'PRODGASPROMOTION-GAS-USERS-CLOSE'];
        $prod_gas_promotion_organization_state_code = ['OPEN', 'CLOSE'];

        $results = $this->ProdGasPromotion->userCartGets($user, $organization_id, $user_id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);

        if(!empty($results)) {

            $title = "Carrello delle promozioni";
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

            $results = $results['results'];
   
        } // end if(!empty($results))
        
        $this->set(compact('results', 'title', 'user'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/user_promotion_cart');
        } 
        else {
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }         
}