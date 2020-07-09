<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Authentication\AuthenticationService;

class TokensController extends AppController
{		
    public function initialize()
    {
        parent::initialize();
		
		$this->loadComponent('CryptDecrypt');
		$this->loadComponent('User');
    }

    public function beforeFilter(Event $event)
    {
        // parent::beforeFilter($event);

        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $this->viewBuilder()->setClassName('Json'); 

        $this->Authentication->allowUnauthenticated(['login']); 
    }

    private function _getUuid() {
        return uniqid();
    }

    /*
     * method: get
     * url: /api/tokens/login?u=user_salt
     *
     * user_salt = 
     *  $user_id = $this->user->id;
     *  $user_organization_id = $this->user->organization_id;
     *  $organization_id = $this->user->organization->id; // gas scelto
     */
    public function login()
    {
        $debug = false;
        $esito = true;
        if (!$this->request->is('get')) {
            return;
        }        

        $user_salt = $this->request->getQuery('u');
		if(empty($user_salt)) {
			return;
		}
        if($debug) debug($user_salt);

		$user = $this->CryptDecrypt->decrypt($user_salt);
		$user = unserialize($user);
		if($debug) debug($user);
		
		$user = $this->User->createUser($user['user_organization_id'], $user['user_id'], $user['organization_id'], $debug);
		if($debug) debug($user); 
		$session = $this->request->getSession();
		$session->write('Auth', $user);
		
        /*
         * land page, controller / action
         * 'prefix' => false se no prende api
         */ 
        $redirects = [];       
        $c_to = $this->request->query['c_to']; 
        $a_to = $this->request->query['a_to']; 
        if(!empty($c_to) && !empty($a_to)) {
            $redirects = ['controller' => $c_to, 'action' => $a_to, 'prefix' => false];
        }
        else
            $redirects = ['controller' => 'admin/Dashboards', 'action' => 'index', 'prefix' => false];

        /*
         * parametri aggiuntivi
         */
        $q = [];
        unset($this->request->query['u']);
        unset($this->request->query['c_to']);
        unset($this->request->query['a_to']);
        if(!empty($this->request->query)) {
            foreach ($this->request->query as $key => $value) {
                array_push($q, $value);
            }
            if(!empty($q)) {
                $redirects += $q;
            }
        }

        if($debug) debug($redirects); 
        
        if($debug) exit;

		return $this->redirect($redirects); 
    }
 }