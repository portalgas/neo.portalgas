<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use App\Traits;

class joomla25SaltsController extends AppController
{		
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();		
    }

    public function beforeFilter(Event $event): void 
    {
        parent::beforeFilter($event);
    }

   /*
    * da cakephp a joomla25
    *  
    * chiamando /api/connect?u={salt}&format=notmpl .htaccess
    * Rests::connect()    
    */
    public function index()
    {
        $debug = false;

        $user_id = $this->Authentication->getIdentity()->id;
        $user_organization_id =  $this->Authentication->getIdentity()->organization_id;
        if(!empty($this->Authentication->getIdentity()->organization))
            $organization_id = $this->Authentication->getIdentity()->organization->id; // gas scelto o gas dello user
        else
            $organization_id = 0;

        $user = ['user_id' => $user_id, 'user_organization_id' => $user_organization_id, 'organization_id' => $organization_id];
        // debug($user);
        $user = serialize($user);
        
        $user_salt = $this->encrypt($user);

        /*
         * land page, controller / action
         */
        
        $c_to = $this->request->getQuery('c_to');
        if(empty($c_to))
            $c_to = 'Pages'; 
        $a_to = $this->request->getQuery('a_to');
        if(empty($a_to))
            $a_to = 'home'; 

        /*
         * parametri aggiuntivi
         */
        $q = '';
        $queries = $this->request->getQuery();
        unset($queries['c_to']);
        unset($queries['a_to']);
        if(!empty($queries)) {
            foreach ($queries as $key => $value) {
                $q = $key.'='.$value;
            }
        }

        // http://www.portalgas.it/api/connect?u={salt}=&c_to=Pages&a_to=home
        $config = Configure::read('Config');

        $url = $config['Portalgas.bo.url'].$config['Portalgas.bo.connect'].'?u='.$user_salt.'&c_to='.$c_to.'&a_to='.$a_to;

        if(!empty($q))
            $url .= '&'.$q;

        if($debug) debug($url);
        // return $this->redirect($url);
                
        header("Location: $url");
        exit; 
    }
 }