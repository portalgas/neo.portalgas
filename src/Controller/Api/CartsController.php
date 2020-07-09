<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Authentication\AuthenticationService;

class CartsController extends AppController
{		
    public function initialize()
    {
        parent::initialize();
		
		$this->loadComponent('User');
    }

    public function beforeFilter(Event $event)
    {
        // parent::beforeFilter($event);

        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        $this->viewBuilder()->setClassName('Json'); 

        $this->Authentication->allowUnauthenticated(['gets']); 
    }

    /*
     * method: *
     * url: /api/carts/gets     
     */
    public function gets()
    {
        $debug = false;
        $esito = true;

        $results = [];
        $results[] = ['id' => 1,
                    'name' => 'libro',
                    'note' => 'lorem ipsum lorem ipsum',
                    'totArticleDetails' => 50,
                    'supplier' => [
                        'img1' => 'http://www.portalgas.it/images/organizations/contents/393.png',
                        'address' => 'via Roma 12',
                        'locality' => 'Torino',
                        'www' => 'www.it'
                      ]
                ];
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }
 }