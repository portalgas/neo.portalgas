<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * GasGroupDeliveries Controller
 *
 * @property \App\Model\Table\GasGroupDeliveriesTable $GasGroupDeliveries
 *
 * @method \App\Model\Entity\GasGroupDelivery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupDeliveriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations', 'Deliveries'],
        ];
        $gasGroupDeliveries = $this->paginate($this->GasGroupDeliveries);

        $this->set(compact('gasGroupDeliveries'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => ['Organizations', 'Deliveries'],
        ]);

        $this->set('gasGroupDelivery', $gasGroupDelivery);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->newEntity();
        if ($this->request->is('post')) {
            $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $this->request->getData());
            if ($this->GasGroupDeliveries->save($gasGroupDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group Delivery'));
        }
        $organizations = $this->GasGroupDeliveries->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupDelivery', 'organizations', 'deliveries'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $this->request->getData());
            if ($this->GasGroupDeliveries->save($gasGroupDelivery)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group Delivery'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group Delivery'));
        }
        $organizations = $this->GasGroupDeliveries->Organizations->find('list', ['limit' => 200]);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
       $this->set(compact('gasGroupDelivery', 'organizations', 'deliveries'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group Delivery id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id);
        if ($this->GasGroupDeliveries->delete($gasGroupDelivery)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Gas Group Delivery'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Gas Group Delivery'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
