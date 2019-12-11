<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use Cake\Log\Log;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AuthsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();     

        $this->Authentication->allowUnauthenticated(['login']); 
        $this->Authorization->skipAuthorization(['login']);

        $this->loadModel('Users');
        /*
         * https://book.cakephp.org/3.0/en/controllers/components/csrf.html#disabling-the-csrf-component-for-specific-actions
         * 
         * non ho il token passato come campo hidden ma qui sono chiamate ajax
         *
         * lo faccio gia' in Application $csrf-whitelistCallback
         */ 
        // $this->getEventManager()->off($this->Csrf); 
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);   
   
    }
    
    public function login()
    {   Log::write('notice', 'login');
        if ($this->request->is('post')) {

            $cookieSessions = $this->getRequest()->getSession()->id();  // $this->getRequest()->getCookie('CAKEPHP'); in app.php
            Log::write('notice', 'CURL Cookie Sessions '.$cookieSessions);

            $result = $this->Authentication->getResult(); // o $result = $this->request->getAttribute('authentication')->getResult();
            // debug($result);
            if ($result->isValid()) {
           
                $identity = $this->Authentication->getIdentity()->getOriginalData(); // o $this->request->getAttribute('identity');
                Log::write('notice', $identity);

                $id = 0;
                if(isset($identity['sub'])) {
                    /*
                    passato il token, prima volta
                    alg => 'HS256'
                    iss => 'atlamo'
                    sub => '1' campo auth.user.key
                    iat => (int) 1569227412
                    exp => (int) 1569231012
                    */ 
                    $id = $identity['sub'];
                }
                else
                if(isset($identity['id'])) {
                    // gia autenticato
                    $id = $identity['id'];
                }
                else {
                    $this->set([
                        'esito' => false,
                        'code' => 500,
                        'msg' => 'No user found!',
                        'results' => $identity
                    ]);   
                }

                if(!empty($id)) {
              
                    $user = $this->Users->get($id, ['contain' => ['UserStates', 'Roles']]);
                    if(!empty($user)) {
                        Log::write('notice', $user);
                        // $this->Authentication->setIdentity($user);  crea un nuovo sessionId                     
                        // debug($this->Authentication->getIdentity()->getOriginalData());
                         $session = $this->getRequest()->getSession();                          
                        $session->write('Auth', $user);
                        
                        $this->set([
                            'esito' => true,
                            'code' => 200,
                            'msg' => '',
                            'results' => $user
                        ]);                   
                    }
                    else {
                        $this->set([
                            'esito' => false,
                            'code' => 500,
                            'msg' => 'No user found with id '.$id.'!',
                            'results' => $identity
                        ]);
                    }

                } // end if(!empty($id))
                else {
                    $this->set([
                        'esito' => false,
                        'code' => 500,
                        'msg' => 'identity or token not valid',
                        'results' => $identity
                    ]);                       
                }
            } else {
                Log::write('notice', $result->getStatus());
                Log::write('notice', $result->getErrors());

                $this->set([
                    'esito' => false,
                    'code' => $result->getStatus(),
                    'msg' => '',
                    'results' => $result->getErrors()
                ]);                 
            }

            $this->set('_serialize', ['esito', 'code', 'msg', 'results']); 
        }
    }
	
    public function createPwd($password)
    {
        $password = $this->User->createPwd($password);

        debug($password);
        exit; 

        $this->set([
            'password' => $password,
            '_serialize', ['password']]);
    }	
}