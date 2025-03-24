<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use Cake\Log\Log;

class CmsImagesController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Upload');
    }

    public function index() {

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $cmsImagesTable = TableRegistry::get('CmsImages');
        $assets = $cmsImagesTable->find()
            ->contain(['CmsPagesImages' => ['CmsPages']])
            ->where(['CmsImages.organization_id' => $this->_organization->id])
            ->all();

        if(!empty($assets))
            $assets = $assets->toArray();

        $results['results'] = $assets;
        return $this->_response($results);
    }

    public function img1Upload() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $request = $this->request->getData();
        if($debug) debug($request);

        $asset_path = ROOT . sprintf(Configure::read('Cms.img.paths'), $this->_organization->id);
        if($debug) debug('asset_path '.$asset_path);

        /*
        * upload del file
        */
        $config_upload = [] ;
        $config_upload['upload_path']    = $asset_path;
        $config_upload['allowed_types']  = ['jpeg', 'jpg', 'png'];
        $config_upload['max_size']       = 0;
        $config_upload['overwrite']      = true;
        $config_upload['encrypt_name']  = false;
        $config_upload['slug_name']  = true;
        $config_upload['remove_spaces'] = true;
        $this->Upload->init($config_upload);
        $upload_results = $this->Upload->upload('img1');
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
        */
        $asset_path = $asset_path.'/'.$file_name;
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

        /*
        * aggiorno db
        */
        $cms_image = $this->CmsImages->newEntity();

        $datas = [];
        $datas['organization_id'] = $this->_organization->id;
        $datas['name'] = $upload_results['file_name'];
        $datas['path'] = $upload_results['file_name'];
        $datas['ext'] = $upload_results['file_ext'];
        $datas['size'] = $upload_results['file_size'];

        $cms_image = $this->CmsImages->patchEntity($cms_image, $datas);
        if (!$this->CmsImages->save($cms_image)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $cms_image->getErrors();
            $results['results'] = [];
            return $this->_response($results);
        }

        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = [];
        return $this->_response($results);
    }
}
