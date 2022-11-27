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
    private $_type='GAS-GROUP';

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        /* 
         * gestisco solo le consegne dei gruppi ($type='GAS-GROUP')
         */
        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isGasGropusManagerDeliveries'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     *
     * gestisco solo le consegne dei gruppi ($type='GAS-GROUP')
     */
    public function index($type='GAS-GROUP')
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $where = [// 'Deliveries.type' => $this->_type, 
                  'Deliveries.organization_id' => $organization_id, 
                 ];
        $this->paginate = [
            'contain' => ['Orders'],
            'conditions' => $where,
            'order' => ['Deliveries.name']
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
        exit;
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
    public function add($type='GAS-GROUP')
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $delivery = $this->Deliveries->newEntity();
        if ($this->request->is('post')) {
            $delivery = $this->Deliveries->patchEntity($delivery, $this->request->getData());
            if ($this->Deliveries->save($delivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Delivery'));
        }
        $this->set(compact('delivery'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $type='GAS-GROUP')
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $delivery = $this->Deliveries->get([$organization_id, $id], [
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
        $this->set(compact('delivery'));
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
