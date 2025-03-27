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

    /*
     * elenco voci di menu
     */
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

        $menus = Cache::read('cms-menus');
        if ($menus !== false) {
            $results['results'] =  $menus;
        }
        else {
            $cmsMenuTable = TableRegistry::get('CmsMenus');

            $where = ['organization_id' => $organization->id, 'is_active' => true];
            if(empty($this->_user))
                $where = ['is_public' => true];

            $menus = $cmsMenuTable->find()
                                ->contain(['CmsMenuTypes', 'CmsDocs'])
                                ->where($where)
                                ->order(['sort' => 'asc'])
                                ->all();
            if($menus->count()>0) {
                $menus = $menus->toArray();
                Cache::write('cms-menus', $menus);
            }

            $results['results'] = $menus;
        }

        return $this->_response($results);
    }

    /*
     * contenuto pagina
     */
    public function page() {

        $debug = false;

        $content = '';

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
        else {
            $cmsMenuTable = TableRegistry::get('CmsMenus');

            $where = ['organization_id' => $organization->id, 'is_active' => true, 'slug' => $slug_page];
            if(empty($this->_user))
                $where = ['is_public' => true];

            $menu = $cmsMenuTable->find()
                ->contain(['CmsPages'])
                ->where($where)
                ->first();
            if(!empty($menu) && !empty($menu->cms_pages) && isset($menu->cms_pages[0])) {
                $content = $menu->cms_pages[0]->body;
            }
        }

        $results['results'] = ['organization' => $organization,
                               'content' => $content];

        return $this->_response($results);
    }

}
