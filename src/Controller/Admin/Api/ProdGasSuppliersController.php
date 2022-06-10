<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;

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
     * il listino non viene importato perchè prende quello del produttore
     * produttore che gestisce il listino articoli (Organizations.type = 'PRODGAS' o owner_organization_id)
     */
    public function import() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['esito'] = true;
        $results['msg'] = '';
        $results['msg_human'] = '';
        $results['datas'] = [];
    
        $supplier_id = $this->request->getData('supplier_id');
        Log::info("import produttore - supplier_id [".$supplier_id."] ", ['scope' => ['monitoring']]);
        if(empty($supplier_id)) {
            $continua = false;
            $results['esito'] = false;
            $results['msg'] = 'supplier_id required';
            $results['msg_human'] = 'Parametri passati non corretti';
            $results['datas'] = [];           
        }
        // debug($supplier_id);

        if($continua) {
            $user = $this->Authentication->getIdentity();
            Log::info("import produttore - organization_id [".$user->organization->id."] ", ['scope' => ['monitoring']]);
            // $organization_id = $user->organization->id; // gas scelto
            // debug($user);
            if(empty($user) || empty($user->organization)) {
                $continua = false;
                $results['esito'] = false;
                $results['msg'] = 'user o user->organization empty';
                $results['msg_human'] = 'Autenticazione non correta!';
                $results['datas'] = $user;           
            }                
        }

        if($continua) {
            $results = $this->ProdGasSupplier->import($user, $supplier_id, $debug);
        }

        return $this->_response($results);        
    }
}