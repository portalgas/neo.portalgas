<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * CmsImages Controller
 *
 * @property \App\Model\Table\CmsImagesTable $CmsImages
 *
 * @method \App\Model\Entity\CmsImage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsImagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations'],
        ];
        $cmsImages = $this->paginate($this->CmsImages);

        $this->set(compact('cmsImages'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Image id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsImage = $this->CmsImages->get($id, [
            'contain' => ['Organizations', 'CmsPagesImages'],
        ]);

        $this->set('cmsImage', $cmsImage);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsImage = $this->CmsImages->newEntity();
        if ($this->request->is('post')) {
            $cmsImage = $this->CmsImages->patchEntity($cmsImage, $this->request->getData());
            if ($this->CmsImages->save($cmsImage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Image'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Image'));
        }
        $organizations = $this->CmsImages->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('cmsImage', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cms Image id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsImage = $this->CmsImages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cmsImage = $this->CmsImages->patchEntity($cmsImage, $this->request->getData());
            if ($this->CmsImages->save($cmsImage)) {
                $this->Flash->success(__('The {0} has been saved.', 'Cms Image'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Cms Image'));
        }
        $organizations = $this->CmsImages->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('cmsImage', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cms Image id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cmsImage = $this->CmsImages->get($id);
        if ($this->CmsImages->delete($cmsImage)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Cms Image'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Cms Image'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
