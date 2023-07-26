<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Des Controller
 *
 * @property \App\Model\Table\DesTable $Des
 *
 * @method \App\Model\Entity\KDe[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $des = $this->paginate($this->Des);

        $this->set(compact('des'));
    }

    /**
     * View method
     *
     * @param string|null $id K De id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $kDe = $this->Des->get($id, [
            'contain' => [],
        ]);

        $this->set('kDe', $kDe);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $kDe = $this->Des->newEntity();
        if ($this->request->is('post')) {
            $kDe = $this->Des->patchEntity($kDe, $this->request->getData());
            if ($this->Des->save($kDe)) {
                $this->Flash->success(__('The {0} has been saved.', 'K De'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K De'));
        }
        $this->set(compact('kDe'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K De id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $kDe = $this->Des->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $kDe = $this->Des->patchEntity($kDe, $this->request->getData());
            if ($this->Des->save($kDe)) {
                $this->Flash->success(__('The {0} has been saved.', 'K De'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K De'));
        }
        $this->set(compact('kDe'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K De id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $kDe = $this->Des->get($id);
        if ($this->Des->delete($kDe)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K De'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K De'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
