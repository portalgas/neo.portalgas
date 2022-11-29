<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Traits;
/**
 * GasGroupOrders Controller
 *
 * @property \App\Model\Table\GasGroupOrdersTable $GasGroupOrders
 *
 * @method \App\Model\Entity\GasGroupOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupDeliveriesController extends AppController
{
    use Traits\SqlTrait;

    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();
        $organization = $user->organization; // gas scelto
      
        if(!isset($user->acl) ||
            !isset($organization->paramsConfig['hasGasGroups']) || 
            $organization->paramsConfig['hasGasGroups']=='N' || 
             !$user->acl['isGasGropusManagerDeliveries']
            ) { 
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
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $where = ['Deliveries.organization_id' => $organization_id];

        $this->paginate = [
            'contain' => ['GasGroups' => ['GasGroupUsers'], 
                'Deliveries' => [
                    'conditions' => ['type' => 'GAS-GROUP'],
                    'Orders']],
            'conditions' => $where,
            'order' => ['Deliveries.data']
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
        exit;
        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => ['Organizations', 'GasGroups', 'Deliveries'],
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
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $deliveriesTable = TableRegistry::get('Deliveries');

        $gasGroupDelivery = $this->GasGroupDeliveries->newEntity();
        if ($this->request->is('post')) {

            $datas = $this->request->getData();
            $gas_group_id = $datas['gas_group_id'];

            /*
             * creo la consegna
             * */
            $delivery = $deliveriesTable->newEntity();
            $datas['organization_id'] = $organization_id;
            $datas['isToStoreroom'] = 'N';
            $datas['isToStoreroomPay'] = 'N';
            $datas['stato_elaborazione'] = 'OPEN';
            $datas['type'] = 'GAS-GROUP';
            $datas['data'] = $this->convertDate($datas['data']);
            $delivery = $deliveriesTable->patchEntity($delivery, $datas);
            if (!$deliveriesTable->save($delivery)) {
                $this->Flash->error($delivery->getErrors());
            } 
            else {

                $datas = [];
                $datas['delivery_id'] = $delivery->id;
                $datas['organization_id'] = $organization_id;
                $datas['gas_group_id'] = $gas_group_id;

                /* 
                 * l'associo al gruppo
                 */  
                $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $datas);
                if (!$this->GasGroupDeliveries->save($gasGroupDelivery)) {
                    $this->Flash->error($gasGroupDelivery->getErrors());
                }
                else {
                    $this->Flash->success(__('The {0} has been saved.', __('Gas Group Delivery')));
                    return $this->redirect(['action' => 'index']);
                }
            }                    
        } // end post

        $this->set('nota_evidenzas', $deliveriesTable->enum('nota_evidenza'));

        $gasGroups = $this->GasGroupDeliveries->GasGroups->findMyLists($user, $organization_id, $user->id);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupDelivery', 'gasGroups', 'deliveries'));
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
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization_id;

        $deliveriesTable = TableRegistry::get('Deliveries');

        $gasGroupDelivery = $this->GasGroupDeliveries->get($id, [
            'contain' => ['Deliveries']
        ]);
       
        if ($this->request->is(['patch', 'post', 'put'])) {

            $datas = $this->request->getData();
            $gas_group_id = $datas['gas_group_id'];

            /*
             * creo la consegna
             * */
            $delivery = $deliveriesTable->newEntity();
            $datas['organization_id'] = $organization_id;
            $datas['isToStoreroom'] = 'N';
            $datas['isToStoreroomPay'] = 'N';
            $datas['stato_elaborazione'] = 'OPEN';
            $datas['type'] = 'GAS-GROUP';
            $datas['data'] = $this->convertDate($datas['data']);
            $delivery = $deliveriesTable->patchEntity($delivery, $datas);
            if (!$deliveriesTable->save($delivery)) {
                $this->Flash->error($delivery->getErrors());
            } 
            else {       
                $gasGroupDelivery = $this->GasGroupDeliveries->patchEntity($gasGroupDelivery, $datas);
                if (!$this->GasGroupDeliveries->save($gasGroupDelivery)) {
                    $this->Flash->error($gasGroupDelivery->getErrors());
                }
                else {            
                    $this->Flash->success(__('The {0} has been saved.', __('Gas Group Delivery')));

                    return $this->redirect(['action' => 'index']);
                }
            } 
        } // end post

        $this->set('nota_evidenzas', $deliveriesTable->enum('nota_evidenza'));

        $gasGroups = $this->GasGroupDeliveries->GasGroups->findMyLists($user, $organization_id, $user->id);
        $deliveries = $this->GasGroupDeliveries->Deliveries->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupDelivery', 'gasGroups', 'deliveries'));
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
        if (!$this->GasGroupDeliveries->delete($gasGroupDelivery)) {
            $this->Flash->error($gasGroupDelivery->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
