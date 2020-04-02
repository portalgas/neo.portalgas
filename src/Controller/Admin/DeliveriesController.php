<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Deliveries Controller
 *
 * @property \App\Model\Table\DeliveriesTable $Deliveries
 *
 * @method \App\Model\Entity\Delivery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DeliveriesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Auth->isRoot($this->user)) {
            die("not root");
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
            'contain' => ['Organizations', 'GcalendarEvents'],
        ];
        $deliveries = $this->paginate($this->Deliveries);

        $this->set(compact('deliveries'));
    }

    /**
     * View method
     *
     * @param string|null $id K Delivery id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $delivery = $this->Deliveries->get($id, [
            'contain' => ['Organizations', 'GcalendarEvents'],
        ]);

        $this->set('delivery', $delivery);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $delivery = $this->Deliveries->newEntity();
        if ($this->request->is('post')) {
            $delivery = $this->Deliveries->patchEntity($delivery, $this->request->getData());
            if ($this->Deliveries->save($delivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Delivery'));
        }
        $organizations = $this->Deliveries->Organizations->find('list', ['limit' => 200]);
        $gcalendarEvents = $this->Deliveries->GcalendarEvents->find('list', ['limit' => 200]);
        $this->set(compact('delivery', 'organizations', 'gcalendarEvents'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $delivery = $this->Deliveries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $delivery = $this->Deliveries->patchEntity($delivery, $this->request->getData());
            if ($this->Deliveries->save($delivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Delivery'));
        }
        $organizations = $this->Deliveries->Organizations->find('list', ['limit' => 200]);
        $gcalendarEvents = $this->Deliveries->GcalendarEvents->find('list', ['limit' => 200]);
        $this->set(compact('delivery', 'organizations', 'gcalendarEvents'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Delivery id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $delivery = $this->Deliveries->get($id);
        if ($this->Deliveries->delete($delivery)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Delivery'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Delivery'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
