<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class StoreroomsController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Storeroom');
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();
        if ($user->organization->paramsConfig['hasStoreroom'] != 'Y' || $user->organization->paramsConfig['hasStoreroomFrontEnd'] != 'Y') {
            $this->_respondWithUnauthorized();
        }
    }
  
    /* 
     * front-end - estrae la dispensa di una consegna legato al carrello dell'user
     * api mai chiamata, solo nell'export del carrello visualizzo la dispensa
     */
    public function userCartGets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $debug = false;

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $delivery_id = $this->request->getData('delivery_id');

        $results = $this->Storeroom->getArticlesByDeliveryId($user, $organization_id, $delivery_id, $options=[], $debug);

        return $this->_response($results); 
    }    
}