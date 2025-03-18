<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;

/**
 * CmsDocs Controller
 *
 * @property \App\Model\Table\CmsDocsTable $CmsDocs
 *
 * @method \App\Model\Entity\CmsDoc[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsDocsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {

    }

    public function download($id)
    {
        $asset = $this->CmsDocs->get($id);
        $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $this->_organization->id);
        $filePath = $asset_path . '/' . $asset->path;
        if (file_exists($filePath)) {
            $response = $this->response->withFile($filePath, [
                'download' => true,
                'name' => $asset->name,
            ]);
            return $response;
        } else {
            $this->Flash->error(__('File non trovato.'));
            return $this->redirect(['action' => 'index']);
        }
    }
}
