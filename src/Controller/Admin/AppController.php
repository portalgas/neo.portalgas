<?php
namespace App\Controller\Admin;

use Cake\Event\Event;
use Cake\Core\Configure;
use App\Traits;

class AppController extends \App\Controller\AppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize() {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }
}