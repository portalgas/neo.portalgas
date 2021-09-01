<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ProdGasSuppliersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('ProdGasSupplier');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
    
    /*
     * dato un supplier_id, creo l'associazione con il GAS (suppliersOrganizations)
     * produttore che gestisce il listino articoli (Organizations.type = 'PRODGAS' o owner_organization_id)
     */
    public function import() {

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = '';
    
        $supplier_id = $this->request->getData('supplier_id');
        // debug($supplier_id);

        $user = $this->Authentication->getIdentity();
        // $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $this->ProdGasSupplier->import($user, $supplier_id, $debug);

        return $this->_response($results);        
    }
}