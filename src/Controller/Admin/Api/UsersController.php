<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;


class UsersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event)
    {
     //   parent::beforeFilter($event);

        if (!$this->request->is('ajax')) {
        //    throw new BadRequestException();
        }
    }

    /* 
     * front-end - dettaglio articolo associato ad un ordine   
     */
    public function cashCtrlLimit() {

        $debug = false;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        $results = [];

        $cashesUsersTable = TableRegistry::get('CashesUsers');
        
        $results = $cashesUsersTable->getUserData($user);

         /*
          * totale importo acquisti
          */
        $results['user_tot_importo_acquistato'] = $cashesUsersTable->getTotImportoAcquistato($user, $organization_id, $user->id);
        
        $cashesUserResults = [];
        $cashesUserResults['limit_type'] = $results['user_limit_type'];
        $cashesUserResults['limit_after'] = $results['user_limit_after'];
        
        $results['ctrl_limit'] = $cashesUsersTable->ctrlLimit($user, $results['organization_cash_limit'], $results['organization_limit_cash_after'], $cashesUserResults, $results['user_cash'], $results['user_tot_importo_acquistato'], $debug);

        return $this->_response($results);
    } 
}