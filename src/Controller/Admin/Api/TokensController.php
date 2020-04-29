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
     * url: /admin/api/tokens/login?u=user_salt
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
		
		$user = $this->User->createUser($user['organization_id'], $user['username']);
		if($debug) debug($user); 
		$session = $this->request->getSession();
		$session->write('Auth', $user);
		
        if($debug) exit;
        
		return $this->redirect(['controller' => 'admin/Dashboards', 'action' => 'index', 'prefix' => false]); 
    }
 }