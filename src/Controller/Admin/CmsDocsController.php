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
class CmsDocsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if($this->_organization->paramsConfig['hasDocuments']=='N') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

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

    public function delete($id = null)
    {
        // $this->request->allowMethod(['post', 'delete']);
        $cmsDoc = $this->CmsDocs->get($id);

        if ($this->CmsDocs->delete($cmsDoc)) {

            /*
             * elimino fisicamente il file
             */

            $asset_path = ROOT . sprintf(Configure::read('Cms.doc.paths'), $this->_organization->id);
            $filePath = $asset_path . '/' . $cmsDoc->path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->Flash->success("Il documento è stato eliminato");
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Page'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
