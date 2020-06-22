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
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
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
            $this->Flash->error("OrganizationsPays già creato per l'anno ".$year, ['escape' => false]);
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
            
            $data['data_pay'] = $this->convertDate(Configure::read('DB.field.date.empty'));
            $data['beneficiario_pay'] = strtoupper($this->OrganizationsPays::BENEFICIARIO_PAY_MARCO);
            $data['type_pay'] = strtoupper($this->OrganizationsPays::TYPE_PAY_RICEVUTA);
            if($debug) debug($data);
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $data);
            if($debug) debug($organizationsPay);
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
        $where = [];
        $request = $this->request->getData();
        // debug($request);        
        $search_year = $this->request->getQuery('search_year');
        $search_beneficiario_pay = $this->request->getQuery('search_beneficiario_pay');
        $search_hasMsg = $this->request->getQuery('search_hasMsg');
        $search_type_pay = $this->request->getQuery('search_type_pay');       
        if(empty($search_year))
            $search_year = date('Y');
        if(!empty($search_year)) {
            array_push($where, ['OrganizationsPays.year' => $search_year]);
        } 

        if(!empty($request['search_beneficiario_pay'])) {
            $search_beneficiario_pay = $request['search_beneficiario_pay'];
            array_push($where, ['OrganizationsPays.beneficiario_pay' => $search_beneficiario_pay]);
        }
        if(!empty($request['search_hasMsg'])) {
            $search_hasMsg = $request['search_hasMsg'];
            array_push($where, ['Organizations.hasMsg' => $search_hasMsg]);
        }
        if(!empty($request['search_type_pay'])) {
            $search_type_pay = $request['search_type_pay'];
            array_push($where, ['OrganizationsPays.type_pay' => $search_type_pay]);
        }
        // debug($where);

        $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

        $this->paginate = [
            'conditions' => [$where],
            'contain' => ['Organizations'],
            'order' => ['OrganizationsPays.year' => 'desc'],
            'limit' => 100
        ];
        $organizationsPays = $this->paginate($this->OrganizationsPays);

        foreach($organizationsPays as $organizationsPay) {
            
            /*
             * ctrl l'ultima lastVisitDate del manager / tesoriere
             */

            $where_groups = ['UserUsergroupMap.group_id IN ' => 
                            [Configure::read('group_id_manager'), Configure::read('group_id_referent_tesoriere')]];
            $where = ['Users.organization_id' => $organizationsPay->organization_id];
            $organizationsPay->lastVisitDate = $this->Total->getLastVisitDateByGroups($this->user, $where_groups, $where, $debug);

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
            $requestData = $this->request->getData();

            if(empty($requestData['data_pay']))
                $requestData['data_pay'] = $this->convertDate(Configure::read('DB.field.date.empty'));
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
                $requestData['data_pay'] = $this->convertDate(Configure::read('DB.field.date.empty'));
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
