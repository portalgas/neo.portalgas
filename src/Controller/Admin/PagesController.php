<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function msgStop() {
        
    }    

    public function msgNotOrderState() {

    }
}