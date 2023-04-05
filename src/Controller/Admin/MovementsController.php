<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Decorator\MovementDecorator;

/**
 * Movements Controller
 *
 * @property \App\Model\Table\MovementsTable $Movements
 *
 * @method \App\Model\Entity\Movement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MovementsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Movement');
        $this->loadComponent('SuppliersOrganization');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->_user->acl['isCassiere']) {        
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // fa l'ovveride di AppController $this->viewBuilder()->setClassName('AdminLTE.AdminLTE');
        if($this->request->getParam('action')=='print') {
            $this->viewBuilder()->setClassName('CakePdf.Pdf');             
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $where = [];
        $where += ['Movements.organization_id' => $this->_organization->id];
        $sorts = ['Movements.date desc'];
                  
        /* 
         * filters
         */
        $request = $this->request->getQuery();
        $search_movement_type_id = '';
        $search_year = '';

        if(!empty($request['search_year'])) 
            $search_year = $request['search_year'];
        else 
            $search_year = date('Y');
        $where += ['Movements.year' => $search_year];

        if(!empty($request['search_movement_type_id'])) {
            $search_movement_type_id = $request['search_movement_type_id'];
            $where += ['Movements.movement_type_id' => $search_movement_type_id];
        } 
        $this->set(compact('search_movement_type_id', 'search_year'));

        /* 
         * popola i movimenti con l'importo di cassa dell'anno passato
         */
        $this->Movement->populateByCashes($this->_user, $this->_organization->id, $search_year);
        
        $this->paginate = [
            'contain' => ['MovementTypes', 'Users', 'SuppliersOrganizations', 
                    'Orders', 'StatOrders'],
            'conditions' => $where,
            'order' => $sorts
        ];

        $movement_types = $this->Movements->MovementTypes->getList(['is_active in ' => [0, 1]]);
        $years = $this->Movements->getYears($this->_user, $this->_organization->id);
        $this->set(compact('movement_types', 'years'));

        $movements = $this->paginate($this->Movements);
        $movements = new MovementDecorator($this->_user, $movements);
        $movements = $movements->results;

        $this->set('payment_types', $this->Movements->enum('payment_type'));
        $this->set(compact('movements'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $movement = $this->Movements->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $datas['organization_id'] = $this->_organization->id;
            $datas = $this->Movements->decorateMovementType($datas); 
            // debug($datas);
            $movement = $this->Movements->patchEntity($movement, $datas);
            if (!$this->Movements->save($movement)) {
                $this->Flash->error($movement->getErrors());
            }
            else {
                $this->Flash->success(__('The {0} has been saved.', __('Movement')));

                return $this->redirect(['action' => 'index']);
            }
            
        }
        $this->set('payment_types', $this->Movements->enum('payment_type'));
        
        $movementTypes = $this->Movements->MovementTypes->getList();
        
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->getList($this->_user, $this->_organization->id);

        $order_type_id = Configure::read('Order.type.gas');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id);
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

        $this->set(compact('movement', 'movementTypes', 'users', 'suppliersOrganizations', 'order_type_id'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Movement id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $movement = $this->Movements->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas = $this->request->getData();
            $datas = $this->Movements->decorateMovementType($datas); 
            // debug($datas); 
            $movement = $this->Movements->patchEntity($movement, $datas);
            if (!$this->Movements->save($movement)) {
                $this->Flash->error($movement->getErrors());
            }
            else {            
                $this->Flash->success(__('The {0} has been saved.', __('Movement')));

                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set('payment_types', $this->Movements->enum('payment_type'));
        $movementTypes = $this->Movements->MovementTypes->getList();

        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->getList($this->_user, $this->_organization->id);

        $order_type_id = Configure::read('Order.type.gas');
        
        $ordersTable = TableRegistry::get('Orders');
        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id);
        $suppliersOrganizations = $ordersTable->getSuppliersOrganizations($this->_user, $this->_organization->id, $this->_user->id);                      
        $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);

        $movement = new MovementDecorator($this->_user, $movement);
        $movement = $movement->results;

        $this->set(compact('movement', 'movementTypes', 'users', 'suppliersOrganizations', 'order_type_id'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Movement id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $movement = $this->Movements->get($id);
        if (!$this->Movements->delete($movement)) {
            $this->Flash->error($movement->getErrors());
        }
        else {
            $this->Flash->success(__('The record has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /*
     * https://dompdf.net/examples.php
     */
    public function print($debug=false) { 

        $results = [];
        $title = '';

        $where = [];
        $sorts = ['Movements.date desc'];
        
        /* 
         * filters
         */
        $request = $this->request->getQuery();
        $search_movement_type_id = '';
        $search_year = '';

        if(!empty($request['search_year'])) 
            $search_year = $request['search_year'];
        else 
            $search_year = date('Y');
        $where += ['Movements.year' => $search_year];

        if(!empty($request['search_movement_type_id'])) {
            $search_movement_type_id = $request['search_movement_type_id'];
            $where += ['Movements.movement_type_id' => $search_movement_type_id];
        } 
        $this->set(compact('search_movement_type_id', 'search_year'));

        $movements = $this->Movements->find()
                        ->contain(['MovementTypes', 'Users', 'SuppliersOrganizations', 
                            'Orders', 'StatOrders'])
                        ->where($where)
                        ->order($sorts)
                        ->all();

        $this->set('payment_types', $this->Movements->enum('payment_type'));
        $this->set(compact('movements'));

        $title = "Movimenti anno ".$search_year;
        $filename = $this->setFileName($title.'.pdf');

        Configure::write('CakePdf', [
            'engine' => 'CakePdf.DomPdf', // 'CakePdf.WkHtmlToPdf',
            'margin' => [
                'bottom' => 15,
                'left' => 50,
                'right' => 30,
                'top' => 45
            ],
            'orientation' => 'portrait', // landscape (orizzontale) portrait (verticale)
            'download' => true,
            'filename' => $filename
        ]);

        $this->set('img_path', Configure::read('DOMPDF_IMG_PATH'));
    }    
}
