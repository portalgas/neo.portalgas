<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

class PingsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    public function index() {
        
        $results = [0 => 'session active'];
        
        return $this->_response($results);
    }        
}