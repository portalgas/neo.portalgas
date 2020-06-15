<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * StatDeliveries Controller
 *
 * @property \App\Model\Table\StatDeliveriesTable $StatDeliveries
 *
 * @method \App\Model\Entity\StatDelivery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatDeliveriesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
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
        $this->paginate = [
            'contain' => ['Organizations'],
        ];
        $statDeliveries = $this->paginate($this->StatDeliveries);

        $this->set(compact('statDeliveries'));
    }

    /**
     * View method
     *
     * @param string|null $id K Stat Delivery id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $statDelivery = $this->StatDeliveries->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('statDelivery', $statDelivery);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $statDelivery = $this->StatDeliveries->newEntity();
        if ($this->request->is('post')) {
            $statDelivery = $this->StatDeliveries->patchEntity($statDelivery, $this->request->getData());
            if ($this->StatDeliveries->save($statDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Delivery'));
        }
        $organizations = $this->StatDeliveries->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('statDelivery', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Stat Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $statDelivery = $this->StatDeliveries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $statDelivery = $this->StatDeliveries->patchEntity($statDelivery, $this->request->getData());
            if ($this->StatDeliveries->save($statDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Stat Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Stat Delivery'));
        }
        $organizations = $this->StatDeliveries->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('statDelivery', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Stat Delivery id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $statDelivery = $this->StatDeliveries->get($id);
        if ($this->StatDeliveries->delete($statDelivery)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Stat Delivery'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Stat Delivery'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
