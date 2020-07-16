<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * PriceTypes Controller
 *
 * @property \App\Model\Table\PriceTypesTable $PriceTypes
 *
 * @method \App\Model\Entity\PriceType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PriceTypesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auths->isRoot($this->user)) {
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
        $this->PriceTypes->Organizations->removeBehavior('OrganizationsParams');
        $this->paginate = [
            'contain' => ['Organizations', 'Orders'],
        ];
        $priceTypes = $this->paginate($this->PriceTypes);

        $this->set(compact('priceTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Price Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $priceType = $this->PriceTypes->get($id, [
            'contain' => ['Organizations', 'Orders'],
        ]);

        $this->set('priceType', $priceType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $priceType = $this->PriceTypes->newEntity();
        if ($this->request->is('post')) {

            $data = $this->request->getData();
            $data['organization_id'] = $this->user->organization->id;
            $priceType = $this->PriceTypes->patchEntity($priceType, $data);
            if ($this->PriceTypes->save($priceType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Price Type'));

                return $this->redirect(['action' => 'index']);
            }
            else {
                $this->Flash->error($priceType->getErrors());
            }
        }
        $organizations = $this->PriceTypes->Organizations->find('list', ['limit' => 200]);
        $orders = $this->PriceTypes->Orders->find('list', ['limit' => 200]);
        $this->set(compact('priceType', 'organizations', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Price Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $priceType = $this->PriceTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['organization_id'] = $this->user->organization->id; 
            $priceType = $this->PriceTypes->patchEntity($priceType, $data);
            if ($this->PriceTypes->save($priceType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Price Type'));

                return $this->redirect(['action' => 'index']);
            }
            else {
                $this->Flash->error($priceType->getErrors());
            }
        }
        $organizations = $this->PriceTypes->Organizations->find('list', ['limit' => 200]);
        $orders = $this->PriceTypes->Orders->find('list', ['limit' => 200]);
        $this->set(compact('priceType', 'organizations', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Price Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $priceType = $this->PriceTypes->get($id);
        if ($this->PriceTypes->delete($priceType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Price Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Price Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
