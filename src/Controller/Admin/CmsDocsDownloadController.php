<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * CmsDocs Controller
 *
 * @property \App\Model\Table\CmsDocsTable $CmsDocs
 *
 * @method \App\Model\Entity\CmsDoc[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsDocsDownloadController extends AppController
{
    public function initialize()
    {
        parent::initialize();

    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['get']);
    }

    /*
     * senza auth perche' potrebbe essere pubblico
     */
    public function get($uuid)
    {
        $continue = true;

        /*
         * non filtro per organization_id perche' potrebbe essere un doc pubblico
         */
        $asset = $this->CmsDocs->find()->where(['uuid' => $uuid])->first();
        if(!empty($asset)) {
            $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $asset->organization_id);
            $filePath = $asset_path . '/' . $asset->path;
            if (file_exists($filePath)) {
                $response = $this->response->withFile($filePath, [
                    'download' => true,
                    'name' => $asset->name,
                ]);
                return $response;
            } else {
                $continue = false;
            }
        }
        else
            $continue = false;

        if(!$continue) {
            $this->Flash->error(__('File non trovato.'));
            return $this->redirect(['action' => 'index']);

        }
    }
}
