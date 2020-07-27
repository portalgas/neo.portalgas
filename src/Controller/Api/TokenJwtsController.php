<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

use App\Model\Table\UsersTable;
use Cake\Cache\Cache;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use App\Response\Token;
use App\Traits;
use App\Decorator\ApiUserDecorator;
use Authentication\AuthenticationService;

/* 
 * testare token https://jwt.io
 */
class TokenJwtsController extends ApiAppController
{
    use Traits\ResponseTrait;

    private $uuid = '';
    protected $_responseToken = '';
    protected $_factory = '';
    private $_field_sub = '';
    private $_authorizationHeader = '';
    private $_issuer = '';
    private $_accessible = [
        'username',
        'name',
        'mail',
        'data_activation',
        'data_last_login',
        'license'
    ]; 

    public function initialize(): void 
    {
        parent::initialize();
        
        $this->_factory = 'jwt';

        $this->uuid = $this->_getUuid();

        $this->loadComponent('Log'); 
        $this->loadModel('Users');
        $this->loadModel('Tokens');   
        
        $this->_field_sub = 'username'; // $this->Authentication->_config['token']['field_sub'];  // valore che identifica lo user (username) da restituire nel token
        $this->_authorizationHeader = 'Authorization'; // $this->Authentication->_config['token']['authorizationHeader'];  
        $this->_issuer = 'portalgas'; // $this->Authentication->_config['token']['issuer'];             
    }

    public function beforeFilter(Event $event): void {
        parent::beforeFilter($event);

        $this->Authentication->addUnauthenticatedActions(['login']);
        $this->Authentication->allowUnauthenticated(['login']);
    }

    public function beforeRender(Event $event): void  {
   
        parent::beforeFilter($event);
    }
    
    private function _getUuid() {
        return uniqid();
    }

    /*
     * method: post
     * url: /api/tokens/login
     * header: ['X-Requested-With' => 'XMLHttpRequest', 
                'Content-Type' => 'application/x-www-form-urlencoded']; 
     * x-www-form-urlencoded: ['username' => '', 'password' => '']
     */
    public function login()
    {
        $esito = true;
        if (!$this->request->is('post')) {
            $this->_respondWithMethodNotAllowed();
            return;
        }        

        $this->_responseToken = new Token();
        $token = '';
 
        // cosi' Authentication non li legge $json_data = $this->request->input('json_decode');
        $this->Log->logging($this->uuid, 'Request', $this->request->getData());
        $this->Log->logging($this->uuid, 'Factory', $this->_factory);

        if($esito) {
            $result = $this->Authentication->getResult(); 
            // debug($result); 
            if ($result->isValid()) {
                $user = $this->Authentication->getIdentity()->getOriginalData(); // Authentication.Form perche' passati i campi in application/x-www-form-urlencoded
                // debug($user);
                $this->Log->logging($this->uuid, 'user', $user);
                // Generate auth token.
                switch (strtolower($this->_factory)) {
                    case 'jwt':
                        $token = JWT::encode([
                            'alg' => 'HS256',
                            'iss' => $this->_issuer, // who created and signed this token
                            'sub' => $user[$this->_field_sub],  // subject
                            'iat' => time(),
                            'exp' => time() + 3600, // (expiration time 3600 1 hour - 86400 1 gg
                        ], Security::getSalt()); 
                        break;
                    case 'database':
                        $token = $this->Tokens->generateUniqueToken($user);
                        // persisto in database
                        $this->Tokens->create($user, $token); 
                        // debug($this->TokenDatabase->user('token')); 
                    break;
                    default:
                        die("TokensController $this->_factory non previsto");
                    break;
                }
                // debug($token);

                $this->response = $this->response->withHeader($this->_authorizationHeader, 'Bearer '.$token);

                $this->_responseToken->set('esito', true);
                $this->_responseToken->set('code', Token::CODE_OK);
                $this->_responseToken->set('token', $token); 

                $user = new ApiUserDecorator($user);
                $user = $user->results;                
                $this->_responseToken->set('user', $user); 

            } else {
                $this->_responseToken->set('esito', false);
                $this->_responseToken->set('code', Token::CODE_KO);
                $this->_responseToken->set('msg', __('Username or password is incorrect')); 

                $this->Log->logging($this->uuid, 'Authentication result', $result, 'ERROR');
                $this->Log->logging($this->uuid, 'Authentication status', $result->getStatus(), 'ERROR');
                $this->Log->logging($this->uuid, 'Authentication errors', $result->getErrors(), 'ERROR');
            }
        } // end if($esito)

        $this->Log->logging($this->uuid, '_responseToken', $this->_responseToken);

        return $this->setJsonResponse($this->_responseToken);
    }
 
    /*
     * dato un token restituisco lo user
     *
     * method: post
     * url: /admin/api/tokens/getUser
     * header: ['X-Requested-With' => 'XMLHttpRequest', 
                'Content-Type' => 'application/x-www-form-urlencoded'
                'Authorization' => 'Bearer {token}'];
     */
    public function getUser() {
  
        if (!$this->request->is('post')) {
            $this->_respondWithMethodNotAllowed();
            return;
        }        

        $this->_responseToken = new Token();

        $result = $this->Authentication->getResult(); 
        // debug($result);
        if ($result->isValid()) {
            $user = $this->Authentication->getIdentity()->getOriginalData(); 
            $this->_responseToken->set('esito', true);
            $this->_responseToken->set('code', Token::CODE_OK);
            $this->_responseToken->set('msg', '');             
            $this->_responseToken->set('results', $user);             
        } else {
            $this->_responseToken->set('esito', false);
            $this->_responseToken->set('code', Token::CODE_KO);
            $this->_responseToken->set('msg', $result->getErrors()); 

            debug($result->getStatus());
            debug($result->getErrors());
        }
    }
}