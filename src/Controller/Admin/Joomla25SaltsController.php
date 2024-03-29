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
    *
    * localhost nginx non gestisce .htaccess  


    /*
     * da neo.portalgas a portalgas cakephp 2.x
     *  da (portalgas cakephp 2.x => neo.portalgas /api/joomla25SaltsController
     *
     *
     * user_salt = 
     *  $user_id = $this->Authentication->getIdentity()->id;
     *  $user_organization_id = $this->Authentication->getIdentity()->organization_id;
     *  $organization_id = $this->Authentication->getIdentity()->organization->id; // gas scelto
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
        
        $date = date('Ymd');
        $user_salt = $this->encrypt($user, $date);

        /*
         * land page, controller / action
         */
        $scope = $this->request->getQuery('scope');
        if(empty($scope))
            $scope = 'FE';
        $c_to = $this->request->getQuery('c_to');
        if(empty($c_to) && $scope == 'BO')
            $c_to = 'Pages'; 
        $a_to = $this->request->getQuery('a_to');
        if(empty($a_to) && $scope == 'BO')
            $a_to = 'home'; 

        /*
         * parametri aggiuntivi
         */
        $q = '';
        $queries = $this->request->getQuery();
        if($debug) debug($queries);
        unset($queries['scope']);
        unset($queries['c_to']);
        unset($queries['a_to']);
        if(!empty($queries)) {
            foreach ($queries as $key => $value) {
                $q .= $key.'='.$value.'&';
            }
            if(!empty($q))  
               $q = substr($q, 0, (strlen($q)-1));
        }
        if($debug) debug($q);
        /*
         * https://www.portalgas.it/api/connect?u={salt}=&c_to=Pages&a_to=home
         *
         * localhost
         * http://portalgas.local/index.php/?option=com_cake&controller=Rests&action=connect&u={salt}=&c_to=Pages&a_to=home
         */
        $config = Configure::read('Config');

        switch (strtolower($this->application_env)) {
            case 'development':
                $url = $config['Portalgas.bo.url'].$config['Portalgas.bo.connect'].'&u='.$user_salt.'&scope='.$scope.'&c_to='.$c_to.'&a_to='.$a_to;
                break;
            default:
                $url = $config['Portalgas.bo.url'].$config['Portalgas.bo.connect'].'?u='.$user_salt.'&scope='.$scope.'&c_to='.$c_to.'&a_to='.$a_to;
                break;
        }
        
        if(!empty($q))
            $url .= '&'.$q;

        if($debug) debug($url); 
        if($debug) exit;
        // return $this->redirect($url);
                
        header("Location: $url");
        exit; 
    }
 }