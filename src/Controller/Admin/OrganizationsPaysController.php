<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * OrganizationsPays Controller
 *
 * @property \App\Model\Table\OrganizationsPaysTable $OrganizationsPays
 *
 * @method \App\Model\Entity\OrganizationsPay[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsPaysController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth');
        $this->loadComponent('Total');
        $this->loadComponent('OrganizationsPay');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!$this->Auth->isRoot($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
       
    /*
     * genera i pagamenti per l'anno corrente
     */
    public function generate()
    {
        $debug = false;
        $continue = true;
        $year = date('Y');

        /*
         * ctrl se per l'anno corrente esiste gia'
         */
        $organizationsPays = $this->OrganizationsPays->find()
                    ->where(['OrganizationsPays.year' => date('Y')])
                    ->first();
        if(!empty($organizationsPays)) {
            $this->Flash->error("OrganizationsPays già creato per l'anno ".$year, ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        // $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');
        $organizations = $this->OrganizationsPays->Organizations->find()
                    ->where(['Organizations.stato' => 'Y', 'Organizations.type' => 'GAS'])
                    ->order(['Organizations.name'])
                    ->all();

        
        foreach($organizations as $organization) {
            
            if($debug) debug($organization->name.' ('.$organization->id.')');

            $where = ['organization_id' => $organization->id,
                      'block' => 0];
            $tot_users = $this->Total->totUsers($this->user, $where, $debug);

            // fractis
            if($organization->id==37)
                $tot_users = 24;

            /*
             * tolgo info@nomegas.portalgas.it
             * eventuale dispensa@nomegas.portalgas.it
             */
            if($organization->paramsConfig['hasStoreroom']=='Y') 
                $users_default = 2;
            else
                $users_default = 1;
            $tot_users = ($tot_users - $users_default);
            if($debug) debug($organization->name.' tot_users '.$tot_users);

            /*
             * totale ordini
             */         
            $tot_orders = $this->Total->totOrdersByYear($this->user, $organization->id, $year, [], $debug);
            if($debug) debug($organization->name.' tot_orders '.$tot_users);

            $tot_suppliers_organizations = $this->Total->totSuppliersOrganizations($this->user, $organization->id, [], $debug);
            if($debug) debug($organization->name.' tot_suppliers_organizations '.$tot_suppliers_organizations);

            $tot_articles = $this->Total->totArticlesOrganizations($this->user, $organization->id, [], $debug); 
            if($debug) debug($organization->name.' tot_articles '.$tot_articles);           

            /*
             * insert
             */
            $organizationsPay = $this->OrganizationsPays->newEntity();
            $data = [];
            $data['organization_id'] = $organization->id;
            $data['year'] = $year;
            $data['tot_users'] = $tot_users;
            $data['tot_orders'] = $tot_orders;
            $data['tot_suppliers_organizations'] = $tot_suppliers_organizations;
            $data['tot_articles'] = $tot_articles;
            $data['importo'] = $this->OrganizationsPays->getImporto($this->user, $organization->id, $year, $tot_users, $debug);
            $data['import_additional_cost'] = 0;
            
            $data['data_pay'] = Configure::read('DB.field.date.empty');
            $data['beneficiario_pay'] = strtoupper($this->OrganizationsPays::BENEFICIARIO_PAY_MARCO);
            $data['type_pay'] = strtoupper($this->OrganizationsPays::TYPE_PAY_RICEVUTA);
            $data = $this->convertRequestDateToDatabase($data);
            if($debug) debug($data);
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $data);
            if (!$this->OrganizationsPays->save($organizationsPay)) {
                debug($organizationsPay->getErrors());
                $continue=false;
            }

        } // end foreach($organizations as $organization)

        if(!$continue)
            exit;

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $debug = false;

        /*
         * da ricerca
         */
        $searchConditions = [];
        $filter_year = $this->request->getQuery('filter_year');
        if(empty($filter_year))
            $filter_year = date('Y'); 
        if(!empty($filter_year)) {
            $searchConditions = ['OrganizationsPays.year' => $filter_year];
        }
        // debug($searchConditions);

        $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

        $this->paginate = [
            'conditions' => [$searchConditions],
            'contain' => ['Organizations'],
            'order' => ['OrganizationsPays.year' => 'desc'],
            'limit' => 100
        ];
        $organizationsPays = $this->paginate($this->OrganizationsPays);

        foreach($organizationsPays as $organizationsPay) {
            
            $organizationsPay->isSaldato = $this->OrganizationsPays->isSaldato($this->user, $organizationsPay);

            /*
             * creazione path documento di pagamento 
             */
            // debug($this->OrganizationsPay->getDocPath($this->user, $organizationsPay, $debug));
            if(!empty($this->OrganizationsPay->getDocPath($this->user, $organizationsPay, $debug))) {
                $organizationsPay->doc_url = $this->OrganizationsPay->getDocUrl($this->user, $organizationsPay, $debug);
            }
            else 
                $organizationsPay->doc_url = '';

            // debug($organizationsPay->doc_url);

        } // foreach($organizationsPays as $organizationsPay)

        $hasMsgs = ['Y' => __('Si'), 'N' => __('No')];
        $beneficiario_pays = $this->OrganizationsPays->enum('beneficiario_pay');
        $type_pays = $this->OrganizationsPays->enum('type_pay');

        $this->set(compact('organizationsPays', 'hasMsgs', 'beneficiario_pays', 'type_pays'));
    }

    /**
     * View method
     *
     * @param string|null $id Organizations Pay id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');
        
        $organizationsPay = $this->OrganizationsPays->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('organizationsPay', $organizationsPay);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');
        
        $organizationsPay = $this->OrganizationsPays->newEntity();
        if ($this->request->is('post')) {
            $requestData = $this->convertRequestDateToDatabase($this->request->getData());
            if(empty($requestData['data_pay']))
                $requestData['data_pay'] = Configure::read('DB.field.date.empty');
            // debug($requestData);
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $requestData);
            if ($this->OrganizationsPays->save($organizationsPay)) {
                $this->Flash->success(__('The {0} has been saved.', 'Organizations Pay'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Organizations Pay'));
        }

        $organizations = $this->OrganizationsPays->Organizations->find('list', ['limit' => 200]);
        $beneficiario_pays = $this->OrganizationsPays->enum('beneficiario_pay');
        $type_pays = $this->OrganizationsPays->enum('type_pay');
        
        $this->set(compact('organizationsPay', 'organizations', 'beneficiario_pays', 'type_pays'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Organizations Pay id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');
        
        $organizationsPay = $this->OrganizationsPays->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $requestData = $this->request->getData();
            if(empty($requestData['data_pay']))
                $requestData['data_pay'] = Configure::read('DB.field.date.empty');
            $requestData = $this->convertRequestDateToDatabase($requestData);
            // debug($requestData);            
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $requestData);          
            if ($this->OrganizationsPays->save($organizationsPay)) {
                $this->Flash->success(__('The {0} has been saved.', 'Organizations Pay'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Organizations Pay'));
        }

        $organizations = $this->OrganizationsPays->Organizations->find('list', ['limit' => 200]);
        $beneficiario_pays = $this->OrganizationsPays->enum('beneficiario_pay');
        $type_pays = $this->OrganizationsPays->enum('type_pay'); 

        if($organizationsPay->data_pay->format('Y-m-d')==Configure::read('DB.field.date.empty'))
            $organizationsPay->data_pay = '';
        $this->set(compact('organizationsPay', 'organizations', 'beneficiario_pays', 'type_pays'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Organizations Pay id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $organizationsPay = $this->OrganizationsPays->get($id);
        if ($this->OrganizationsPays->delete($organizationsPay)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Organizations Pay'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Organizations Pay'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
