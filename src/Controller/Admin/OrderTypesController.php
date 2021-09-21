<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class OrderTypesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $orderTypes = $this->paginate($this->OrderTypes);

        $this->set(compact('orderTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Orders Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $orderType = $this->OrderTypes->get($id, [
            'contain' => [],
        ]);

        $this->set('orderType', $orderType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $orderType = $this->OrderTypes->newEntity();
        if ($this->request->is('post')) {
            $orderType = $this->OrderTypes->patchEntity($orderType, $this->request->getData());
            if ($this->OrderTypes->save($orderType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Orders Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Orders Type'));
        }
        $this->set(compact('orderType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Orders Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $orderType = $this->OrderTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $orderType = $this->OrderTypes->patchEntity($orderType, $this->request->getData());
            if ($this->OrderTypes->save($orderType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Orders Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Orders Type'));
        }
        $this->set(compact('orderType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Orders Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $orderType = $this->OrderTypes->get($id);
        if ($this->OrderTypes->delete($orderType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Orders Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Orders Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
