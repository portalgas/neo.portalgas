<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * CmsMenuTypes Controller
 *
 * @property \App\Model\Table\CmsMenuTypesTable $CmsMenuTypes
 *
 * @method \App\Model\Entity\CmsMenuType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CmsMenuTypesController extends AppController
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
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $cmsMenuTypes = $this->paginate($this->CmsMenuTypes);

        $this->set(compact('cmsMenuTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Cms Menu Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cmsMenuType = $this->CmsMenuTypes->get($id, [
            'contain' => ['CmsMenus'],
        ]);

        $this->set('cmsMenuType', $cmsMenuType);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cmsMenuType = $this->CmsMenuTypes->newEntity();
        if ($this->request->is('post')) {
            $cmsMenuType = $this->CmsMenuTypes->patchEntity($cmsMenuType, $this->request->getData());
            if ($this->CmsMenuTypes->save($cmsMenuType)) {
                $this->Flash->success(__('The cms menu type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cms menu type could not be saved. Please, try again.'));
        }
        $this->set(compact('cmsMenuType'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cms Menu Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cmsMenuType = $this->CmsMenuTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cmsMenuType = $this->CmsMenuTypes->patchEntity($cmsMenuType, $this->request->getData());
            if ($this->CmsMenuTypes->save($cmsMenuType)) {
                $this->Flash->success(__('The cms menu type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cms menu type could not be saved. Please, try again.'));
        }
        $this->set(compact('cmsMenuType'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cms Menu Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cmsMenuType = $this->CmsMenuTypes->get($id);
        if ($this->CmsMenuTypes->delete($cmsMenuType)) {
            $this->Flash->success(__('The cms menu type has been deleted.'));
        } else {
            $this->Flash->error(__('The cms menu type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
