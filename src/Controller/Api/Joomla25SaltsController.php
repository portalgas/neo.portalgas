<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Authentication\AuthenticationService;

class Joomla25SaltsController extends AppController
{		
    public function initialize(): void 
    {
        parent::initialize();		
    }

    public function beforeFilter(Event $event): void 
    {
        // parent::beforeFilter($event);

        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $this->viewBuilder()->setClassName('Json'); 

        $this->Authentication->allowUnauthenticated(['login']); 
    }

    /*
     * da portalgas cakephp 2.x => neo.portalgas
     *  da (neo.portalgas a portalgas cakephp 2.x /admin/joomla25SaltsController
     *
     * method: get
     * url: /api/joomla25Salts/login?u=user_salt
     *
     * Autenticazione \src\Auth\Joomla25Authenticate.php
     *
     * user_salt = 
     *  $user_id = $this->Authentication->getIdentity()->id;
     *  $user_organization_id = $this->Authentication->getIdentity()->organization_id;
     *  $organization_id = $this->Authentication->getIdentity()->organization->id; // gas scelto
     */
    public function login()
    {
        $debug = false;

        if (!$this->request->is('get')) {
            return;
        }     

        $queries = $this->request->getQuery();
        if($debug) debug($queries);

        /*
         * land page, controller / action
         * 'prefix' => false se no prende api
         */ 
        $redirects = [];       
        $scope = 'FE';      
        $c_to = ''; 
        $a_to = '';
        if(isset($queries['scope']))
            $scope = $queries['scope'];
        if(isset($queries['c_to']))
            $c_to = $queries['c_to']; 
        if(isset($queries['a_to']))
            $a_to = $queries['a_to']; 
        if(!empty($queries) && !empty($c_to)) {
            $redirects = ['controller' => $c_to, 'action' => $a_to, 'prefix' => false];
        }
        else
            $redirects = ['controller' => 'admin/Dashboards', 'action' => 'index', 'prefix' => false];

        /*
         * parametri aggiuntivi
         */
        $q = [];
        if(isset($queries['scope']))
            unset($queries['scope']);
        if(isset($queries['u']))
            unset($queries['u']);
        if(isset($queries['c_to']))
            unset($queries['c_to']);
        if(isset($queries['a_to']))
            unset($queries['a_to']);
        if(!empty($queries)) {
            foreach ($queries as $key => $value) {
                $q[$key] = $value;
            }
            if(!empty($q)) {
                $redirects += $q;
            }
        }

        if($debug) debug($this->Authentication->getIdentity());

        if($debug) debug($redirects); 
        
        if($debug) exit;

		return $this->redirect($redirects); 
    }
 }