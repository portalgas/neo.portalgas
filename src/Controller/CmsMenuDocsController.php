<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CmsMenuDocs Controller
 *
 * @property \App\Model\Table\CmsDocsTable $CmsMenuDocs
 *
 * @method \App\Model\Entity\CmsDoc[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsMenuDocsController extends AppController
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
        $cmsMenuDocs = $this->paginate($this->CmsMenuDocs);

        $this->set(compact('cmsMenuDocs'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Menu Doc id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsMenuDoc = $this->CmsMenuDocs->get($id, [
            'contain' => ['Organizations', 'CmsMenus'],
        ]);

        $this->set('cmsMenuDoc', $cmsMenuDoc);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsMenuDoc = $this->CmsMenuDocs->newEntity();
        if ($this->request->is('post')) {
            $cmsMenuDoc = $this->CmsMenuDocs->patchEntity($cmsMenuDoc, $this->request->getData());
            if ($this->CmsMenuDocs->save($cmsMenuDoc)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Menu Doc'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Menu Doc'));
        }
        $organizations = $this->CmsMenuDocs->Organizations->find('list', ['limit' => 200]);
        $cmsMenus = $this->CmsMenuDocs->CmsMenus->find('list', ['limit' => 200]);
        $this->set(compact('cmsMenuDoc', 'organizations', 'cmsMenus'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cms Menu Doc id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsMenuDoc = $this->CmsMenuDocs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cmsMenuDoc = $this->CmsMenuDocs->patchEntity($cmsMenuDoc, $this->request->getData());
            if ($this->CmsMenuDocs->save($cmsMenuDoc)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Menu Doc'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Menu Doc'));
        }
        $organizations = $this->CmsMenuDocs->Organizations->find('list', ['limit' => 200]);
        $cmsMenus = $this->CmsMenuDocs->CmsMenus->find('list', ['limit' => 200]);
        $this->set(compact('cmsMenuDoc', 'organizations', 'cmsMenus'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cms Menu Doc id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cmsMenuDoc = $this->CmsMenuDocs->get($id);
        if ($this->CmsMenuDocs->delete($cmsMenuDoc)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Cms Menu Doc'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Menu Doc'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
