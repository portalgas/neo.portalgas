<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Client; // https://book.cakephp.org/3/en/core-libraries/httpclient.html

class BridgesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || !isset($this->Authentication->getIdentity()->acl)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $url = 'http://portalgas.local/administrator/index.php?option=com_cake&controller=ExportDocs&action=exportToReferent&delivery_id=10012&order_id=33032&doc_options=to-articles&doc_formato=PREVIEW&a=N&b=Y&c=&d=&e=&f=&g=&h=&format=notmpl';
        
     //   $url = 'https://www.portalgas.it';
     $url = 'http://portalgas.local';
        
        $http = new Client(
/*
[
  'host' => 'api.example.com',
  'scheme' => 'https',
  'auth' => ['username' => 'mark', 'password' => 'testing']
]
*/


        );
        // $http->addCookie(new Cookie('session', 'abc123'));
        $response = $http->get($url/*, ['q' => 'widget'], [
            'headers' => ['X-Requested-With' => 'XMLHttpRequest']
          ]
        */);
        $response->isOk();

        // Was the response a 30x
        $response->isRedirect();
        
        // Get the status code
        $response->getStatusCode();
       // debug($response->getHeaders());
       // $response = $response->getStringBody();
        // debug($response->body);
        $stream = $response->getBody();
        while (!$stream->eof()) {
            echo $stream->read(100);
        }        
        exit;

    }

}