<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Sluggable\Utility\Slug;

/**
 * CmsMenus Controller
 *
 * @property \App\Model\Table\CmsMenusTable $CmsMenus
 *
 * @method \App\Model\Entity\CmsMenu[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsMenusController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            if(!empty($datas['ids'])) {
                $error = false;
                foreach($datas['ids'] as $numResult => $id) {
                    $cmsMenu = $this->CmsMenus->get($id);

                    $_datas = [];
                    $_datas['sort'] = $numResult;
                    $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $_datas);
                    if (!$this->CmsMenus->save($cmsMenu)) {
                        $error = true;
                        $this->Flash->error($cmsMenu->getErrors());
                        break;
                    }
                }

                Cache::delete('cms-menus');
            }
            if(!$error)
                $this->Flash->success("L'ordinamento delle voci di menù è stato salvato");
        }

        $cmsMenus = $this->CmsMenus->find()->where(['CmsMenus.organization_id' => $this->_organization->id])
            ->contain(['CmsMenuTypes', 'CmsDocs', 'CmsPages'])
            ->order(['sort'])
            ->all();

        $this->set(compact('cmsMenus'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsMenu = $this->CmsMenus->newEntity();
        if ($this->request->is('post')) {

            $datas = [];
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_organization->id;
            $datas['slug'] = Slug::generate($datas['name']);
            $datas['sort'] = $this->CmsMenus->getSort('CmsMenus', ['organization_id' => $this->_organization->id, 'is_active' => 1]);
            $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $datas);
            if ($this->CmsMenus->save($cmsMenu)) {

                switch ($cmsMenu->cms_menu_type_id) {
                    case 1: // PAGE
                        $cmsPagesTable = TableRegistry::get('CmsPages');
                        $cmsPage = $cmsPagesTable->get($datas['cms_page_id']);

                        $datas = [];
                        $datas['cms_menu_id'] = $cmsMenu->id;
                        $cmsPage = $cmsPagesTable->patchEntity($cmsPage, $datas);
                        $cmsPagesTable->save($cmsPage);
                        break;
                    case 2: // DOC
                        $cmsDocsTable = TableRegistry::get('CmsDocs');
                        $cmsDoc = $cmsDocsTable->get($datas['cms_doc_id']);

                        $datas = [];
                        $datas['cms_menu_id'] = $cmsMenu->id;
                        $cmsDoc = $cmsDocsTable->patchEntity($cmsDoc, $datas);
                        $cmsDocsTable->save($cmsDoc);

                        break;
                }

                Cache::delete('cms-menus');

                $this->Flash->success(__('The {0} has been saved.', 'Cms Menu'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Menu'));
        }
        $cmsMenuTypes = $this->CmsMenus->CmsMenuTypes->find('list', ['order' => 'name', 'limit' => 200]);

        $cmsDocsTable = TableRegistry::get('CmsDocs');
        $cmsDocs = $cmsDocsTable->find('list', ['conditions' => ['organization_id' => $this->_organization->id], 'order' => 'name', 'limit' => 200]);

        $cmsPagesTable = TableRegistry::get('CmsPages');
        $cmsPages = $cmsPagesTable->find('list', ['conditions' => ['organization_id' => $this->_organization->id], 'order' => 'name', 'limit' => 200]);

        $this->set(compact('cmsMenu', 'cmsDocs', 'cmsMenuTypes', 'cmsPages'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cms Menu id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsMenu = $this->CmsMenus->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_organization->id;
            $datas['slug'] = Slug::generate($datas['name']);
            $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $datas);
            if ($this->CmsMenus->save($cmsMenu)) {

                Cache::delete('cms-menus');

                $this->Flash->success(__('The {0} has been saved.', 'Cms Menu'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Menu'));
        }
        $organizations = $this->CmsMenus->Organizations->find('list', ['limit' => 200]);
        $cmsMenuTypes = $this->CmsMenus->CmsMenuTypes->find('list', ['limit' => 200]);
        $this->set(compact('cmsMenu', 'organizations', 'cmsMenuTypes'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cms Menu id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        // $this->request->allowMethod(['post', 'delete']);
        $cmsMenu = $this->CmsMenus->get($id);
        if ($this->CmsMenus->delete($cmsMenu)) {
            $this->Flash->success("La voce di menù è stata eliminata");
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Menu'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
