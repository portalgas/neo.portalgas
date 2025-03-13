<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

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
            $data = $this->request->getData();
            if(!empty($datas['ids'])) {
                $error = false;
                foreach($data['ids'] as $numResult => $id) {
                    $cmsMenu = $this->CmsMenus->get($id);

                    $datas = [];
                    $datas['sort'] = $numResult;
                    $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $datas);
                    if (!$this->CmsMenus->save($cmsMenu)) {
                        $error = true;
                        $this->Flash->error($cmsMenu->getErrors());
                        break;
                    }
                }
            }
            if(!$error)
                $this->Flash->success("L'ordinamento delle voci di menù è stato salvato");
        }

        $cmsMenus = $this->CmsMenus->find()->where(['organization_id' => $this->_organization->id])
            ->contain(['CmsMenuTypes', 'CmsDocs', 'CmsPages'])
            ->order(['sort'])
            ->all();

        $this->set(compact('cmsMenus'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Menu id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsMenu = $this->CmsMenus->get($id, [
            'contain' => ['CmsMenuTypes', 'CmsDocs', 'CmsPages'],
        ]);

        $this->set('cmsMenu', $cmsMenu);
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
            $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $this->request->getData());
            if ($this->CmsMenus->save($cmsMenu)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Menu'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Menu'));
        }
        $cmsMenuTypes = $this->CmsMenus->CmsMenuTypes->find('list', ['limit' => 200]);
        $cmsDocs = $this->CmsMenus->CmsDocs->find('list', ['limit' => 200]);
        $cmsPages = $this->CmsMenus->CmsPages->find('list', ['limit' => 200]);
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
            $cmsMenu = $this->CmsMenus->patchEntity($cmsMenu, $this->request->getData());
            if ($this->CmsMenus->save($cmsMenu)) {
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
        $this->request->allowMethod(['post', 'delete']);
        $cmsMenu = $this->CmsMenus->get($id);
        if ($this->CmsMenus->delete($cmsMenu)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Cms Menu'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Menu'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
