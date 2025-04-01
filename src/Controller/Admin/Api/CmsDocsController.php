<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use Cake\Log\Log;

class CmsDocsController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Upload');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function index($cms_page_id=0)
    {

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $i = 0;
        $doc_ids = [];
        $assets = [];
        if (!empty($cms_page_id)) {
            /*
             * estraggo prima gli assets associati alla pagina
             */
            $cmsPagesDocsTable = TableRegistry::get('CmsPagesDocs');
            $tmp_assets = $cmsPagesDocsTable->find()
                ->contain(['CmsPages', 'CmsDocs' => ['CmsMenus']])
                ->where(['CmsPagesDocs.organization_id' => $this->_organization->id,
                    'CmsPagesDocs.cms_page_id' => $cms_page_id])
                ->order(['CmsPagesDocs.sort'])
                ->all();
            if ($tmp_assets->count() > 0) {
                foreach ($tmp_assets as $tmp_asset) {
                    $doc_ids[] = $tmp_asset->cms_doc_id;

                    $assets[$i] = $tmp_asset->cms_doc;
                    $assets[$i]['cms_page'] = $tmp_asset->cms_page;
                    $assets[$i]['cms_menu'] = $tmp_asset->cms_doc->cms_menu;

                    $i++;
                }
            }
        } // emd if(!empty($cms_page_id))

        $where = ['CmsDocs.organization_id' => $this->_organization->id];
        if (!empty($doc_ids))
            $where += ['CmsDocs.id not in ' => $doc_ids];

        $cmsDocsTable = TableRegistry::get('CmsDocs');
        $tmp_assets = $cmsDocsTable->find()
            ->contain(['CmsMenus', 'CmsPagesDocs' => ['CmsPages']])
            ->where($where)
            ->all();
        if ($tmp_assets->count() > 0) {
            foreach ($tmp_assets as $tmp_asset) {
                $assets[$i] = $tmp_asset;
                $assets[$i]['cms_page'] = null;

                $i++;
            }
        }

        /*
         * controllo se il file esiste fisicamente
         */
        if (!empty($assets))
            foreach ($assets as $numResult => $asset) {
                $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $asset->organization_id) . '/' . $asset->path;
                if (!file_exists($asset_path)) {
                    $assets[$numResult]->file_exists = false;
                } else
                    $assets[$numResult]->file_exists = true;
            }


        $results['results'] = $assets;
        return $this->_response($results);
    }

    public function doc1Upload() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $request = $this->request->getData();
        if($debug) debug($request);

        $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $this->_organization->id);
        if($debug) debug('asset_path '.$asset_path);

        /*
        * upload del file
        */
        $config_upload = [] ;
        $config_upload['upload_path']    = $asset_path;
        $config_upload['allowed_types']  = ['pdf'];
        $config_upload['max_size']       = 0;
        $config_upload['overwrite']      = true;
        $config_upload['encrypt_name']  = false;
        $config_upload['slug_name']  = true;
        $config_upload['remove_spaces'] = true;
        $this->Upload->init($config_upload);
        $upload_results = $this->Upload->upload('doc1');
        if ($upload_results===false){
            $errors = $this->Upload->errors();
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $errors;
            $results['results'] = [];
            if($debug) debug($errors);
            return $this->_response($results);
        }
        if($debug) debug($this->Upload->output());
        $upload_results = $this->Upload->output();
        $file_name = $upload_results['file_name'];
        if(!isset($upload_results['file_name']) || empty($upload_results['file_name'])) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Errore di sistema!";
            $results['results'] = [];
            return $this->_response($results);
        }

        /*
        * ridimensiono img originale

        $asset_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.path.full'), $this->_organization->id, $file_name);
        $docOperations = [
            'thumbnail' => [
                'height' => Configure::read('App.web.img.upload.width.article'),
                'width' => Configure::read('App.web.img.upload.width.article')
            ]];
            $this->Articles->processDoc(
                $asset_path,
                $asset_path,
            [],
            $docOperations);
        */

        /*
        * aggiorno db
        */
        $cms_doc = $this->CmsDocs->newEntity();

        $datas = [];
        $datas['organization_id'] = $this->_organization->id;
        $datas['uuid'] = uniqid();
        $datas['name'] = $upload_results['file_name'];
        $datas['path'] = $upload_results['file_name'];
        $datas['ext'] = $upload_results['file_ext'];
        $datas['size'] = $upload_results['file_size'];

        $cms_doc = $this->CmsDocs->patchEntity($cms_doc, $datas);
        if (!$this->CmsDocs->save($cms_doc)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $cms_doc->getErrors();
            $results['results'] = [];
            return $this->_response($results);
        }

        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = [];
        return $this->_response($results);
    }

    public function doc1Delete() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        if($debug) debug('organization_id passato al metodo ['.$this->_organization->id.'] user ['.$this->_organization->id.']');

        if($this->_organization->id!=$this->_organization->id) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "L'articolo non è gestito da te!";
            $results['results'] = [];
            return $this->_response($results);
        }

        $where = ['organization_id' => $this->_organization->id,
                  'id' => $article_id];
        $article = $this->Articles->find()
                    ->where($where)
                    ->first();
        if(empty($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Articolo non trovato! [".json_encode($where)."]";
            $results['results'] = [];
            return $this->_response($results);
        }

        if(!empty($article->doc1)) {
            $config = Configure::read('Config');
            $asset_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.path.full'), $this->_organization->id, $article->doc1);
            if($debug) debug('img_path '.$asset_path);

            // elimino file
            unlink($asset_path);
        } // end if(!empty($article->doc1))

        $datas = [];
        $datas['doc1'] = '';
        $article = $this->Articles->patchEntity($article, $datas);
        if (!$this->Articles->save($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $article->getErrors();
            $results['results'] = [];
            return $this->_response($results);
        }

        $results['code'] = 200;
        $results['message'] = '';
        $results['errors'] = '';
        $results['results'] = [];
        return $this->_response($results);
    }
}
