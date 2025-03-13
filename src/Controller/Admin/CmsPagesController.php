<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

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
            'contain' => ['Organizations', 'CmsMenus'],
        ];
        $cmsPages = $this->paginate($this->CmsPages);

        $this->set(compact('cmsPages'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Page id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsPage = $this->CmsPages->get($id, [
            'contain' => ['Organizations', 'CmsMenus', 'CmsPageImages'],
        ]);

        $this->set('cmsPage', $cmsPage);
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
            $cmsPage = $this->CmsPages->patchEntity($cmsPage, $this->request->getData());
            if ($this->CmsPages->save($cmsPage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Page'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page'));
        }
        $organizations = $this->CmsPages->Organizations->find('list', ['limit' => 200]);
        $cmsMenus = $this->CmsPages->CmsMenus->find('list', ['limit' => 200]);
        $this->set(compact('cmsPage', 'organizations', 'cmsMenus'));
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
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cmsPage = $this->CmsPages->patchEntity($cmsPage, $this->request->getData());
            if ($this->CmsPages->save($cmsPage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Page'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page'));
        }
        $organizations = $this->CmsPages->Organizations->find('list', ['limit' => 200]);
        $cmsMenus = $this->CmsPages->CmsMenus->find('list', ['limit' => 200]);
        $this->set(compact('cmsPage', 'organizations', 'cmsMenus'));
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
