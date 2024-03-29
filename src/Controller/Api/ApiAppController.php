<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Component\GuardianComponent;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;

/**
 * Class ApiAppController
 *
 * @property GuardianComponent Guardian
 */
class ApiAppController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void 
    {
        parent::initialize();

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
    public function beforeFilter(Event $event): void 
    {
        parent::beforeFilter($event);

        $unauthenticatedActions = ['login',
                                   'add', 'gets', 'getLists',
                                   'getsArticlesBySlug', 'cartArticleDetailsVariantItems', 'cartArticleDetails', 
                                   'checkUniqueField'];
                                   
        $this->Authentication->addUnauthenticatedActions($unauthenticatedActions);

        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }
    }

    public function beforeRender(Event $event): void 
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
        $this->response->withType('application/json'); 
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
