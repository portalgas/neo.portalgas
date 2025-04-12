<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class GasController extends ApiAppController
{
    private $_has_cache = false;
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

        $debug = false;
        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $slug_gas = $this->request->getParam('slugGas');
        $organization = $this->Gas->getBySlug($slug_gas);
        if(empty($organization))
            return $this->_response($results);

        $menus = false;
        if($this->_has_cache) {
            if(empty($this->_user))
                $menus = Cache::read('cms-menus-'.$organization->id);
            else
                $menus = Cache::read('cms-menus-auth-'.$organization->id);
        }

        if ($menus !== false) {
            $results['results'] =  $menus;
        }
        else {
            $cmsMenuTable = TableRegistry::get('CmsMenus');

            $where = ['organization_id' => $organization->id, 'is_active' => true];
            if(empty($this->_user))
                $where += ['is_public' => true];

            $menus = $cmsMenuTable->find()
                                ->contain(['CmsMenuTypes', 'CmsDocs'])
                                ->where($where)
                                ->order(['sort' => 'asc'])
                                ->all();
            if($menus->count()>0)
                $menus = $menus->toArray();
            else {
                $i=0;
                $menus = [];
                $menus[$i] = [];
                $menus[$i]['slug'] = '';
                $menus[$i]['cms_menu_type'] = [];
                $menus[$i]['cms_menu_type']['code'] = 'LINK_INT';
                $menus[$i]['name'] = 'Home del G.A.S.';
            }

            /*
             * voci di menu di default con la pagina home di default
             */
            $i = count($menus);
            if(!isset($organization->paramsConfig['hasGasGroups']) || $organization->paramsConfig['hasGasGroups']=='N') {
                $i++;
                $menus[$i]['slug'] = '';
                $menus[$i]['url'] = '/admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/consegne-'.$organization->j_seo;
                $menus[$i]['cms_menu_type'] = [];
                $menus[$i]['cms_menu_type']['code'] = 'LINK_INT';
                $menus[$i]['name'] = __('Deliveries');
            }

            $i++;
            $menus[$i]['slug'] = '';
            $menus[$i]['url'] = '/admin/joomla25Salts?scope=FE&c_to=/home-' . $organization->j_seo . '/gmaps-produttori';
            $menus[$i]['cms_menu_type'] = [];
            $menus[$i]['cms_menu_type']['code'] = 'LINK_INT';
            $menus[$i]['name'] = 'Produttori del G.A.S.';
            if(!empty($this->_user) && $organization->id==$this->_user->organization->id) {
                $i++;
                $menus[$i]['slug'] = '';
                $menus[$i]['url'] = '/admin/joomla25Salts?scope=FE&c_to=/home-' . $organization->j_seo . '/gmaps';
                $menus[$i]['cms_menu_type'] = [];
                $menus[$i]['cms_menu_type']['code'] = 'LINK_INT';
                $menus[$i]['name'] = 'Gasisti';
            }

            if($this->_has_cache) {
                if(empty($this->_user))
                    Cache::write('cms-menus-'.$organization->id, $menus);
                else
                    Cache::write('cms-menus-auth-'.$organization->id, $menus);
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
            if(!empty($menu->cms_pages[0]->cms_pages_docs)) {
                $docs =  $menu->cms_pages[0]->cms_pages_docs;

                /*
                 * controllo se il file esiste fisicamente
                 */
                $new_docs = [];
                if (!empty($docs)) {
                    foreach ($docs as $numResult => $doc) {
                        $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $doc['cms_doc']['organization_id']) . '/' . $doc['cms_doc']['path'];
                        if (file_exists($asset_path))
                            $new_docs[] = $doc;
                    }
                    $docs = $new_docs;
                }
            }
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
