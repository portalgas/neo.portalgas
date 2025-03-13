<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * CmsPageImages Controller
 *
 * @property \App\Model\Table\CmsPageImagesTable $CmsPageImages
 *
 * @method \App\Model\Entity\CmsPageImage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsPageImagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['CmsPages'],
        ];
        $cmsPageImages = $this->paginate($this->CmsPageImages);

        $this->set(compact('cmsPageImages'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Page Image id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsPageImage = $this->CmsPageImages->get($id, [
            'contain' => ['CmsPages'],
        ]);

        $this->set('cmsPageImage', $cmsPageImage);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsPageImage = $this->CmsPageImages->newEntity();
        if ($this->request->is('post')) {
            $cmsPageImage = $this->CmsPageImages->patchEntity($cmsPageImage, $this->request->getData());
            if ($this->CmsPageImages->save($cmsPageImage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Page Image'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page Image'));
        }
        $cmsPages = $this->CmsPageImages->CmsPages->find('list', ['limit' => 200]);
        $this->set(compact('cmsPageImage', 'cmsPages'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cms Page Image id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsPageImage = $this->CmsPageImages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cmsPageImage = $this->CmsPageImages->patchEntity($cmsPageImage, $this->request->getData());
            if ($this->CmsPageImages->save($cmsPageImage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Page Image'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Page Image'));
        }
        $cmsPages = $this->CmsPageImages->CmsPages->find('list', ['limit' => 200]);
        $this->set(compact('cmsPageImage', 'cmsPages'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cms Page Image id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cmsPageImage = $this->CmsPageImages->get($id);
        if ($this->CmsPageImages->delete($cmsPageImage)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Cms Page Image'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Page Image'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
