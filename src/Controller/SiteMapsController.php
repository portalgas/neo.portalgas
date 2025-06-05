<?php
namespace App\Controller;

use App\Controller\Users\Controller;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Routing\Router;

/*
 * definitito in webroot/robots.txt
 * per avvisare google
 *  https://www.google.com/ping?sitemap=https://neo.portalgas.it/sitemap.xml
 * per monitorare http://www.google.com/webmasters/tools/
 */
class SiteMapsController extends AppController
{
    private $_fullbaseUrl = null;
    private $_changefreq = null;

    public function initialize()
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['index']);

        $this->_fullbaseUrl = Router::fullbaseUrl();
        $this->_changefreq = 'weekly';
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);
    }

    public function index() {

       $this->viewBuilder()->setLayout('sitemap');
       $this->RequestHandler->respondAs('xml');

       $suppliersTable = TableRegistry::get('Suppliers');

       $where = ['Suppliers.stato' => 'Y'];
       $results = $suppliersTable->find()
                                ->select(['Suppliers.slug', 'Suppliers.name'])
                                ->where($where)
                                ->all();

       $this->set(compact('results'));

       $this->set('fullbaseUrl', $this->_fullbaseUrl);
       $this->set('changefreq', $this->_changefreq);
    }
}
