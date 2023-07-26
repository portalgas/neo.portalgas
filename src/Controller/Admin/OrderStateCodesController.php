<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class OrderStateCodesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $this->paginate = [
            // 'conditions' => ['OrderStateCodes.code' => 'OPEN'],
            'order' => ['OrderStateCodes.sort'],
            'contain' => ['Orders'] 
        ];        
        $orderStateCodes = $this->paginate($this->OrderStateCodes);
        // debug($orderStateCodes);

        $this->set(compact('orderStateCodes'));
    }

    /**
     * View method
     *
     * @param string|null $id Order State Code id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $orderStateCode = $this->OrderStateCodes->get($id, [
            'contain' => [],
        ]);

        $this->set('orderStateCode', $orderStateCode);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $orderStateCode = $this->OrderStateCodes->newEntity();
        if ($this->request->is('post')) {
            $orderStateCode = $this->OrderStateCodes->patchEntity($orderStateCode, $this->request->getData());
            if ($this->OrderStateCodes->save($orderStateCode)) {
                $this->Flash->success(__('The {0} has been saved.', 'Order State Code'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Order State Code'));
        }
        $this->set(compact('orderStateCode'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Order State Code id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $orderStateCode = $this->OrderStateCodes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $orderStateCode = $this->OrderStateCodes->patchEntity($orderStateCode, $this->request->getData());
            if ($this->OrderStateCodes->save($orderStateCode)) {
                $this->Flash->success(__('The {0} has been saved.', 'Order State Code'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Order State Code'));
        }
        $this->set(compact('orderStateCode'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Order State Code id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $orderStateCode = $this->OrderStateCodes->get($id);
        if ($this->OrderStateCodes->delete($orderStateCode)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Order State Code'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Order State Code'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
