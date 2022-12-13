<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use App\Controller\Component\GuardianComponent;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;

/**
 * Class ApiAppController
 *
 * @property GuardianComponent Guardian
 */
class ApiAppController extends AppController
{
    protected $_user = null;
    protected $_organization = null;
    
    /**
     * Initialization hook method.
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        if(Configure::read('Config')['IpFilter']['isActive']) {
            $this->loadComponent('Tyrellsys/CakePHP3IpFilter.IpFilter', [
                'trustProxy' => true,
                'whitelist' => Configure::read('Config')['IpFilter']['whitelist']
            ]);            
        }

        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
        $this->response = $this->response->withType('application/json');     
    }

    /**
     * beforeFilter callback
     *
     * - ensure the incoming request is an ajax request
     * - check if a valid user token is provided
     * - if not log out the user
     *
     * @param Event $event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }
                
        $this->_user = $this->Authentication->getIdentity();
        if(empty($this->_user)) {
            /*
             * per evitare Error: [Cake\Http\Exception\ForbiddenException] Forbidden Request URL: /admin/api/pings
            throw new ForbiddenException();
            */
            die("user empty!");
        }
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $this->viewBuilder()->setClassName('Json');       
    }
    
    protected function _response($results) {

        // https://book.cakephp.org/3/en/controllers/request-response.html#setting-the-body
        // corretto ma non restituisce nulla 
        // $this->response->withStringBody(json_encode($results));

        // Notice: Deprecated (16384): Response::body() is deprecated. Mutable response methods are deprecated. Use `withBody()` and `getBody()` instead
        // $this->response->body(json_encode($results));

        $body = $this->response->getBody();
        $body->write(json_encode($results));        
        $this->response->withBody($body);  

        return $this->response;      
    }

    /**
     * Respond with the given status $code and the specified $reason.
     *
     * @param int $code
     * @param string $reason
     * @return void
     */
    protected function _respondWith($code = 200, $reason = '')
    {
        $this->set([
            'esito' => false,
            'code' => $code,
            'msg' => $reason
        ]);

        $this->set('_serialize', ['esito', 'code', 'msg']);

        $this->response = $this->response->withStatus($code, $reason);
    }

    protected function _respondWithBadRequest()
    {
        $this->_respondWith(400, 'BAD REQUEST');
    }

    protected function _respondWithUnauthorized()
    {
        $this->_respondWith(401, 'UNAUTHORIZED');
    }

    protected function _respondForbidden()
    {
        $this->_respondWith(403, 'FORBIDDEN');
    }

    protected function _respondWithNotFound()
    {
        $this->_respondWith(404, 'NOT FOUND');
    }

    protected function _respondWithMethodNotAllowed()
    {
        $this->_respondWith(405, 'METHOD NOT ALLOWED');
    }

    protected function _respondWithValidationErrors()
    {
        $this->_respondWith(422, 'UNPROCESSABLE ENTITY');
    }

    protected function _respondWithConflict()
    {
        $this->_respondWith(409, 'CONFLICT');
    }

    protected function _respondWithError()
    {
        $this->_respondWith(500, 'INTERNAL SERVER ERRROR');
    }
}
