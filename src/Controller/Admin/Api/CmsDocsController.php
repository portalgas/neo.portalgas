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

    public function index() {

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $cmsDocsTable = TableRegistry::get('CmsDocs');
        $assets = $cmsDocsTable->find()
            ->contain(['CmsMenus'])
            ->where(['CmsDocs.organization_id' => $this->_organization->id])
            ->order(['CmsDocs.name' => 'asc'])
            ->all();

        if(!empty($assets))
            $assets = $assets->toArray();

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
        $imageOperations = [
            'thumbnail' => [
                'height' => Configure::read('App.web.img.upload.width.article'),
                'width' => Configure::read('App.web.img.upload.width.article')
            ]];
            $this->Articles->processImage(
                $asset_path,
                $asset_path,
            [],
            $imageOperations);
        */

        /*
        * aggiorno db
        */
        $cms_doc = $this->CmsDocs->newEntity();

        $datas = [];
        $datas['organization_id'] = $this->_organization->id;
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
