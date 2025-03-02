<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class GasController extends ApiAppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Gas');
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets', 'page']);
    }

    public function gets() {
       // debug($this->_user);
       // dd($this->_user->organization);
        $debug = false;

        $slug_gas = $this->request->getParam('slugGas');
        $organization = $this->Gas->getBySlug($slug_gas);

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $menus = Cache::read('menus');
        if ($menus !== false) {
            $results['results'] =  $menus;
        }
        else {
            /*
            $menuTable = TableRegistry::get('Menus');

            $menus = $menuTable->find()
                                ->order(['sort' => 'asc'])
                                ->all();

            Cache::write('menus', $menus);

            $results['results'] = $menus;
            */
        }

        $results['results'] = [
            ['id' => 1, 'slug' => 'home', 'label' => 'Home'],
            ['id' => 2, 'slug' => 'regolamento', 'label' => 'Regolamento']
        ];

        return $this->_response($results);
    }

    public function page() {
        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $slug_gas = $this->request->getParam('slugGas');
        $organization = $this->Gas->getBySlug($slug_gas);

        $slug_page = $this->request->getParam('slugPage');
        if($slug_page=='home')
            $content = $this->Gas->getHomeByContentId($organization);
        else
            $content = 'todo';

        $results['results'] = [
            'organization' => $organization,
            'content' => $content];

        return $this->_response($results);
    }

}
