<?php
namespace App\Controller;

use App\Controller\Users\Controller;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use App\Model\Entity\User;
use App\Model\Table\UsersTable;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['login']);
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
    
    public function login() {
            
        if ($this->request->is('post')) {

        $result = $this->Authentication->getResult();
        // debug($result);
        if ($result->isValid()) {
            
            $user = $this->Authentication->getIdentity()->getOriginalData();
            // debug($user);
            $usersTable = TableRegistry::get('Users');
            $user = $usersTable->findLogin($user->organization_id, $user->id, $user->organization_id); 
            if(!empty($user)) {

                // debug($user);
                $this->Authentication->setIdentity($user);           
                // $target = $this->Authentication->getLoginRedirect() ?? '/home';
                $url = ['controller' => 'admin/Dashboards', 'action' => 'index']; 
                $url = ['controller' => 'Pages', 'action' => 'index'];                
                $redirect = $this->request->getQuery('redirect', $url);
                // debug($redirect);exit;
                return $this->redirect($redirect);
            }
            else {
                $this->Flash->error(__('Username or password is incorrect'));
            } // end if(!empty($user)) 
        }
        else {
            debug($result->getStatus());
            debug($result->getErrors());
            // exit;
            $this->Flash->error(__('Username or password is incorrect'));
        }
        } // end if ($this->request->is('post'))
    }
    
    public function logout()
    {
        $user = $this->Authentication->getIdentity();
        debug($user);

        ob_start();
        $this->Authentication->logout();
        debug($user);
exit;
        return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
    }
}