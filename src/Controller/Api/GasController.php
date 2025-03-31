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

        $this->Authentication->allowUnauthenticated(['organization', 'menu', 'page', 'download']);
    }

    /*
     * dati GAS
     */
    public function organization() {

        $debug = false;

        $content = '';

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $slug_gas = $this->request->getParam('slugGas');
        $organization = $this->Gas->getBySlug($slug_gas);
        $results['results'] = $organization;

        return $this->_response($results);
    }

    /*
     * elenco voci di menu
     */
    public function menu() {
       // debug($this->_user);
       // dd($this->_user->organization);
        // dd($this->_organization); gas scalto dopo il login
        $debug = false;

        $slug_gas = $this->request->getParam('slugGas');
        $organization = $this->Gas->getBySlug($slug_gas);

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $menus = Cache::read('cms-menus-'.$organization->id);
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
            if($menus->count()>0)
                $menus = $menus->toArray();
            else {
                /*
                 * veco di menu di default con la pagina home di default
                 */
                $menus = [];
                $menus[0] = [];
                $menus[0]['slug'] = 'home';
                $menus[0]['cms_menu_type'] = [];
                $menus[0]['cms_menu_type']['code'] = 'PAGE';
                $menus[0]['name'] = 'Home del G.A.S.';
            }
            Cache::write('cms-menus-'.$organization->id, $menus);

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
        $cmsMenuTable = TableRegistry::get('CmsMenus');

        $where = ['organization_id' => $organization->id, 'is_active' => true, 'slug' => $slug_page];
        if(empty($this->_user))
            $where += ['is_public' => true];

        $menu = $cmsMenuTable->find()
            ->contain(['CmsPages' => [
                'CmsPagesImages' => ['CmsImages'],
                'CmsPagesDocs' => ['CmsDocs']]])
            ->where($where)
            ->first();

        $content = '';
        $images = [];
        $docs = [];

        if(!empty($menu) && !empty($menu->cms_pages) && isset($menu->cms_pages[0])) {
            $content = $menu->cms_pages[0]->body;
            if(!empty($menu->cms_pages[0]->cms_pages_images))
                $images =  $menu->cms_pages[0]->cms_pages_images;
            if(!empty($menu->cms_pages[0]->cms_pages_docs))
                $docs =  $menu->cms_pages[0]->cms_pages_docs;
        }
        else {
            $content = $this->Gas->getHomeByContentId($organization);
            $images = [];
            $docs = [];
        }

        $results['results']['content'] = $content;
        $results['results']['images'] = $images;
        $results['results']['docs'] = $docs;

        return $this->_response($results);
    }
}
