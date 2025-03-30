<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * CmsPages Controller
 *
 * @property \App\Model\Table\CmsPagesTable $CmsPages
 *
 * @method \App\Model\Entity\CmsPage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsPagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['CmsMenus', 'CmsPagesImages', 'CmsPagesDocs'],
            'conditions' => ['CmsPages.organization_id' => $this->_organization->id],
        ];
        $cmsPages = $this->paginate($this->CmsPages);

        $this->set(compact('cmsPages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsPage = $this->CmsPages->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_organization->id;

            $cmsPage = $this->CmsPages->patchEntity($cmsPage, $datas);
            if ($this->CmsPages->save($cmsPage)) {

                if(isset($datas['img_ids'])) {
                    $cmsPagesImagesTable = TableRegistry::get('CmsPagesImages');
                    $cmsPagesImagesTable->setImgs($this->_organization->id, $cmsPage->id, $datas['img_ids']);
                }
                if(isset($datas['doc_ids'])) {
                    $cmsPagesDocsTable = TableRegistry::get('CmsPagesDocs');
                    $cmsPagesDocsTable->setDocs($this->_organization->id, $cmsPage->id, $datas['doc_ids']);
                }

                $this->Flash->success(__('The {0} has been saved.', 'Cms Page'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page'));
        }
        /*
         * estraggo solo i link di tipo 'page'
         */
        $cmsMenusTable = TableRegistry::get('CmsMenus');
        $cmsMenus = $cmsMenusTable->getMenuToAssociateList($this->_organization->id);
        $this->set(compact('cmsPage', 'cmsMenus'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cms Page id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsPage = $this->CmsPages->get($id, [
            'contain' => ['CmsMenus'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_organization->id;

            if(isset($datas['img_ids'])) {
                $cmsPagesImagesTable = TableRegistry::get('CmsPagesImages');
                $cmsPagesImagesTable->setImgs($this->_organization->id, $cmsPage->id, $datas['img_ids']);
                unset($datas['img_ids']);
            }
            if(isset($datas['doc_ids'])) {
                $cmsPagesDocsTable = TableRegistry::get('CmsPagesDocs');
                $cmsPagesDocsTable->setDocs($this->_organization->id, $cmsPage->id, $datas['doc_ids']);
                unset($datas['doc_ids']);
            }

            $cmsPage = $this->CmsPages->patchEntity($cmsPage, $datas);
            if ($this->CmsPages->save($cmsPage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Page'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page'));
        }
        $cmsMenus = $this->CmsPages->CmsMenus->find('list', ['conditions' => ['organization_id' => $this->_organization->id], 'limit' => 200]);
        $this->set(compact('cmsPage', 'cmsMenus'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cms Page id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cmsPage = $this->CmsPages->get($id);
        if ($this->CmsPages->delete($cmsPage)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Cms Page'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Page'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
