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

        /* 
         * read file config CakePdf.php
         * debug(Configure::read('CakePdf'));   
         */
        define('DOMPDF_ENABLE_AUTOLOAD', false);
        define('DOMPDF_ENABLE_HTML5PARSER', true);
        define('DOMPDF_ENABLE_REMOTE', false);
        define('DEBUG_LAYOUT', true); 
        define("DOMPDF_ENABLE_CSS_FLOAT", true);
        define("DOMPDF_ENABLE_JAVASCRIPT", false);
        define("DEBUGPNG", true);
        define("DEBUGCSS", true);

        Configure::load('CakePdf', 'default');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        if(!$this->_debug) {
            $this->viewBuilder()->setClassName('CakePdf.Pdf');
            $this->viewBuilder()->setTheme('CakePdf'); 
        }
    }

    /*
     * https://dompdf.net/examples.php
     */
    public function userCart($delivery_id, $debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $storeroomResults = [];
        $title = '';

        $deliveriesTable = TableRegistry::get('Deliveries');
        $delivery = $deliveriesTable->getById($this->_user, $this->_organization->id, $delivery_id);
        if(!empty($delivery)) {
            
            $title = "Carrello della consegna ".$delivery->label.' di '.$this->_user->username;
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $options = [];
            $options['sql_limit'] = Configure::read('sql.no.limit');

            $results = $this->Order->userCartGets($this->_user, $this->_organization->id, $delivery_id, [], $debug); 
            // debug($results);

            /*
             * storerooms
             */
            if ($this->user->organization->paramsConfig['hasStoreroom'] == 'Y' && $this->_organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {
                $storeroomResults = $this->Storeroom->getArticlesByDeliveryId($this->_user, $this->_organization_id, $delivery_id, $options=[], $debug);            
            }

        } // end if(!empty($delivery))
        
        $this->set(compact('results', 'storeroomResults', 'delivery', 'title', 'user'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/user_cart');
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                                // Template/Admin/Api/Exports/pdf/user_cart.ctp 
                                ->setTemplate('/Admin/Api/Exports/pdf/user_cart') 
                                // Template/Layout/pdf/default.ctp
                                ->setLayout('../../Layout/pdf/default') 
                                // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
                                ->setClassName('CakePdf.Pdf'); 
                             
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }

    public function userPromotionCart($debug=false) { 

        if (!$this->Authentication->getResult()->isValid()) {
            return false;
        }

        $debug = false;
        $results = [];
        $storeroomResults = [];
        $title = '';

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $prod_gas_promotion_state_code = ['PRODGASPROMOTION-GAS-USERS-OPEN', 'PRODGASPROMOTION-GAS-USERS-CLOSE'];
        $prod_gas_promotion_organization_state_code = ['OPEN', 'CLOSE'];

        $results = $this->ProdGasPromotion->userCartGets($this->_user, $this->_organization_id, $this->_user->id, $prod_gas_promotion_state_code, $prod_gas_promotion_organization_state_code);

        if(!empty($results)) {

            $title = "Carrello delle promozioni";
            Configure::write('CakePdf.filename', $this->setFileName($title.'.pdf'));

            $results = $results['results'];
   
        } // end if(!empty($results))
        
        $this->set(compact('results', 'title', 'user'));

        if($this->_debug) {
            $this->set('img_path', Configure::read('DOMPDF_DEBUG_IMG_PATH'));
            $this->layout = 'pdf/default';
            $this->render('/Admin/Api/Exports/pdf/user_promotion_cart');
        } 
        else {
            $this->viewBuilder()->setOptions(Configure::read('CakePdf'))
                               ->setClassName('CakePdf.Pdf');            
            $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
        }
    }         
}