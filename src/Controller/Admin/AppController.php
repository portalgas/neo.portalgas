<?php
namespace App\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;

class AppController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
}