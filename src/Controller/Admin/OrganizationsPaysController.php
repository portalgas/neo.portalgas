<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

/**
 * OrganizationsPays Controller
 *
 * @property \App\Model\Table\OrganizationsPaysTable $OrganizationsPays
 *
 * @method \App\Model\Entity\OrganizationsPay[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsPaysController extends AppController
{
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Total');
        $this->loadComponent('OrganizationsPay');
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isRoot']) {
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
        $year_prev = (date('Y')-1);

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

        /*
         * gia' non associato $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');
         *
         * parent_id: per raggruppare i GAS
         */
        $organizations = $this->OrganizationsPays->Organizations->find()
                    ->where(['Organizations.stato' => 'Y',
                            'Organizations.type' => 'GAS',
                            // 'Organizations.id' => 28,
                            'Organizations.parent_id is null'])
                    ->order(['Organizations.name'])
                    ->all();

        foreach($organizations as $organization) {

            $child_tot_users = 0;
            $child_tot_orders = 0;
            $child_tot_suppliers_organizations = 0;
            $child_tot_articles = 0;
            $child_importo = 0;

            if($debug) debug($organization->name.' ('.$organization->id.')');

            /*
             * ctrl se ha GAS figli
             */
            $childOrganizations = $this->OrganizationsPays->Organizations->find()
                                    ->where(['Organizations.stato' => 'Y',
                                            'Organizations.type' => 'GAS',
                                            'Organizations.parent_id' => $organization->id])
                                    ->order(['Organizations.name'])
                                    ->all();
            if($childOrganizations->count()>0) {
                foreach($childOrganizations as $childOrganization) {

                    if($debug) debug('CHILD '.$childOrganization->name.' ('.$childOrganization->id.')');

                    /*
                    * totale users
                    */
                    $where = ['organization_id' => $childOrganization->id, 'block' => 0];
                    $_tot_users = $this->Total->totUsers($this->Authentication->getIdentity(), $where, $debug);

                    /*
                    * tolgo info@nomegas.portalgas.it
                    * eventuale dispensa@nomegas.portalgas.it
                    */
                    if(isset($childOrganization->paramsConfig['hasStoreroom']) && $childOrganization->paramsConfig['hasStoreroom']=='Y')
                        $users_default = 2;
                    else
                        $users_default = 1;
                    $_tot_users = ($_tot_users - $users_default);

                    /*
                    * totale ordini
                    */
                    $child_tot_orders += $this->Total->totOrdersByYear($this->Authentication->getIdentity(), $childOrganization->id, $year, [], $debug);

                    $child_tot_suppliers_organizations += $this->Total->totSuppliersOrganizations($this->Authentication->getIdentity(), $childOrganization->id, [], $debug);

                    $child_tot_articles += $this->Total->totArticlesOrganizations($this->Authentication->getIdentity(), $childOrganization->id, [], $debug);

                    /*
                     * importo
                     */
                    $child_importo += $this->OrganizationsPays->getImporto($this->Authentication->getIdentity(), $childOrganization->id, $year, $_tot_users, $debug);

                    $child_tot_users += $_tot_users;
                } // end foreach($childOrganizations as $childOrganization)
            }

            /*
             * dati anno precedente per beneficiario_pay / type_pay
             */
            $beneficiario_pay = '';
            $organization_prev = $this->OrganizationsPays
                                ->find()
                                ->where(['OrganizationsPays.organization_id' => $organization->id,
                                         'OrganizationsPays.year' => $year_prev])
                                ->first();
            if(!empty($organization_prev)) {
                $beneficiario_pay = $organization_prev->beneficiario_pay;
            }

            /*
             * $type_pay = $organization_prev->type_pay; non lo prendo + dall'anno precedente ma dalla config dell'organizzazione
             */
            $paramsPay = json_decode($organization->paramsPay, true);
            $type_pay = $paramsPay['payType'];
            if(empty($beneficiario_pay))
                $beneficiario_pay = strtoupper($this->OrganizationsPays::BENEFICIARIO_PAY_MARCO);
            if(empty($type_pay))
                $type_pay = strtoupper($this->OrganizationsPays::TYPE_PAY_RICEVUTA);

            /*
             * totale users
             */
            $where = ['organization_id' => $organization->id,
                      'block' => 0];
            $tot_users = $this->Total->totUsers($this->Authentication->getIdentity(), $where, $debug);

            // fractis anche su controller=OrganizationsPays&action=index
            if($organization->id==37)
                $tot_users = 21;

            /*
             * tolgo info@nomegas.portalgas.it
             * eventuale dispensa@nomegas.portalgas.it
             */
            if(isset($organization->paramsConfig['hasStoreroom']) && $organization->paramsConfig['hasStoreroom']=='Y')
                $users_default = 2;
            else
                $users_default = 1;
            $tot_users = ($tot_users - $users_default);
            if($debug) debug($organization->name.' tot_users '.$tot_users);

            /*
             * totale ordini
             */
            $tot_orders = $this->Total->totOrdersByYear($this->Authentication->getIdentity(), $organization->id, $year, [], $debug);
            $tot_orders = ($tot_orders + $child_tot_orders);
            if($debug) debug($organization->name.' tot_orders '.$tot_orders);

            $tot_suppliers_organizations = $this->Total->totSuppliersOrganizations($this->Authentication->getIdentity(), $organization->id, [], $debug);
            $tot_suppliers_organizations = ($tot_suppliers_organizations + $child_tot_suppliers_organizations);
            if($debug) debug($organization->name.' tot_suppliers_organizations '.$tot_suppliers_organizations);

            $tot_articles = $this->Total->totArticlesOrganizations($this->Authentication->getIdentity(), $organization->id, [], $debug);
            $tot_articles = ($tot_articles + $child_tot_articles);
            if($debug) debug($organization->name.' tot_articles '.$tot_articles);

            /*
             * importo
             */
            $importo = $this->OrganizationsPays->getImporto($this->Authentication->getIdentity(), $organization->id, $year, $tot_users, $debug);
            $importo = ($importo + $child_importo);

            /*
             * insert
             */
            $organizationsPay = $this->OrganizationsPays->newEntity();
            $data = [];
            $data['organization_id'] = $organization->id;
            $data['year'] = $year;
            $data['tot_users'] = ($tot_users + $child_tot_users);
            $data['tot_orders'] = $tot_orders;
            $data['tot_suppliers_organizations'] = $tot_suppliers_organizations;
            $data['tot_articles'] = $tot_articles;
            $data['importo'] = $importo;
            $data['import_additional_cost'] = 0;

            $data['data_pay'] = $this->convertDate(Configure::read('DB.field.date.empty'));
            $data['beneficiario_pay'] = $beneficiario_pay;
            $data['type_pay'] = $type_pay;
            if($debug) debug($data);
            $organizationsPay = $this->OrganizationsPays->patchEntity($organizationsPay, $data);
            if($debug) debug($organizationsPay);
            if (!$this->OrganizationsPays->save($organizationsPay)) {
                dd([$organizationsPay, $organizationsPay->getErrors()]);
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
        $q = $this->getStringToRequest($request, 'search_'); // se utilizzati i filtri sono in POST
        if(empty($q)) // dopo edit soon in querystring
            $q = $this->getStringToRequest($this->request->getQuery(), 'search_');
        $this->set(compact('q'));

        // debug($request);
        if(!empty($request['search_beneficiario_pay']) || !empty($this->request->getQuery('search_beneficiario_pay'))) {
            !empty($request['search_beneficiario_pay']) ? $search_beneficiario_pay = $request['search_beneficiario_pay']: $search_beneficiario_pay = $this->request->getQuery('search_beneficiario_pay');
        }
        else
            $search_beneficiario_pay = '';
        if(!empty($request['search_hasMsg']) || !empty($this->request->getQuery('search_hasMsg')))
            !empty($request['search_hasMsg']) ? $search_hasMsg = $request['search_hasMsg']: $search_hasMsg = $this->request->getQuery('search_hasMsg');
        else
            $search_hasMsg = '';
        if(!empty($request['search_hasPdf']) || !empty($this->request->getQuery('search_hasPdf')))
            !empty($request['search_hasPdf']) ? $search_hasPdf = $request['search_hasPdf']: $search_hasPdf = $this->request->getQuery('search_hasPdf');
        else
            $search_hasPdf = '';
        if(!empty($request['search_type_pay']) || !empty($this->request->getQuery('search_type_pay')))
            !empty($request['search_type_pay']) ? $search_type_pay = $request['search_type_pay']: $search_type_pay = $this->request->getQuery('search_type_pay');
        else
            $search_type_pay = '';
        if(!empty($request['search_organization_id']) || !empty($this->request->getQuery('search_organization_id')))
            !empty($request['search_organization_id']) ? $search_organization_id = $request['search_organization_id']: $search_organization_id = $this->request->getQuery('search_organization_id');
        else
            $search_organization_id = '';

        if(!empty($request['search_year']) || !empty($this->request->getQuery('search_year')))
            !empty($request['search_year']) ? $search_year = $request['search_year']: $search_year = $this->request->getQuery('search_year');
        else
            $search_year = date('Y');
        array_push($where, ['OrganizationsPays.year' => $search_year]);

        if(!empty($search_beneficiario_pay)) {
            array_push($where, ['OrganizationsPays.beneficiario_pay' => $search_beneficiario_pay]);
        }
        if(!empty($search_hasMsg)) {
            array_push($where, ['Organizations.hasMsg' => $search_hasMsg]);
        }
        if(!empty($search_type_pay)) {
            array_push($where, ['OrganizationsPays.type_pay' => $search_type_pay]);
        }
        if(!empty($search_organization_id)) {
            array_push($where, ['OrganizationsPays.organization_id' => $search_organization_id]);
        }
        // debug($where);
        $this->set(compact('search_year', 'search_beneficiario_pay', 'search_hasMsg', 'search_hasPdf', 'search_type_pay', 'search_organization_id'));

        // gia' non associato $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

        $this->paginate = [
            'conditions' => [$where],
            'contain' => ['Organizations' => [
                                'conditions' => ['Organizations.type' => 'GAS', 'Organizations.stato' => 'Y'],
                                'sort' => ['Organizations.name' => 'asc']]],
            'order' => ['OrganizationsPays.year' => 'desc', 'Organizations.name' => 'asc'],
            'limit' => 100
        ];
        $organizationsPays = $this->paginate($this->OrganizationsPays);

        foreach($organizationsPays as $numResult => $organizationsPay) {

            /*
             * mail per i pagamento
             */
            $organizationsPay->paramsPay = json_decode($organizationsPay->organization->paramsPay, true);

            /*
             * ctrl l'ultima lastVisitDate del manager / tesoriere
             */
            $where_groups = ['UserUsergroupMap.group_id IN ' =>
                            [Configure::read('group_id_manager'), Configure::read('group_id_referent_tesoriere')]];
            $where = ['Users.organization_id' => $organizationsPay->organization_id];
            $organizationsPay->lastVisitDate = $this->Total->getLastVisitDateByGroups($this->Authentication->getIdentity(), $where_groups, $where, $debug);

            $organizationsPay->isSaldato = $this->OrganizationsPays->isSaldato($this->Authentication->getIdentity(), $organizationsPay);

            /*
             * creazione path documento di pagamento
             */
            // debug($this->OrganizationsPay->getDocPath($this->Authentication->getIdentity(), $organizationsPay, $debug));
            if(!empty($this->OrganizationsPay->getDocPath($this->Authentication->getIdentity(), $organizationsPay, $debug))) {
                $organizationsPay->doc_url = $this->OrganizationsPay->getDocUrl($this->Authentication->getIdentity(), $organizationsPay, $debug);
            }
            else
                $organizationsPay->doc_url = '';

            // debug($organizationsPay->doc_url);

            if(($organizationsPay->importo + $organizationsPay->import_additional_cost) > 77.47)
                $organizationsPay->bollo = 2.00;
            else
                $organizationsPay->bollo = 0;

                $organizationsPay->importo_totale = ($organizationsPay->importo + $organizationsPay->import_additional_cost + $organizationsPay->bollo);
        } // foreach($organizationsPays as $organizationsPay)
        // debug($organizationsPays);

        $hasMsgs = ['Y' => __('Si'), 'N' => __('No')];
        $hasPdfs = ['Y' => __('Presente'), 'N' => __('Non presente')];
        $beneficiario_pays = $this->OrganizationsPays->enum('beneficiario_pay');
        $type_pays = $this->OrganizationsPays->enum('type_pay');
        $organizations = $this->OrganizationsPays->Organizations->find('list', [
                'conditions' => ['Organizations.stato' => 'Y', 'type' => 'GAS'],
                'order' => ['Organizations.name' => 'asc'],
                'limit' => 500]);

        $years = [];
        for($i=2014; $i<=date('Y'); $i++) {
            $years[$i] = $i;
        }

        $this->set(compact('organizationsPays', 'hasMsgs', 'hasPdfs', 'beneficiario_pays', 'type_pays', 'organizations', 'years'));
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
        // gia' non associato $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

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
        // gia' non associato $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

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

        $organizations = $this->OrganizationsPays->Organizations->find('list', [
                'conditions' => ['Organizations.stato' => 'Y', 'type' => 'GAS'],
                'order' => ['Organizations.name' => 'asc'],
                'limit' => 500]);
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
        // gia' non associato $this->OrganizationsPays->Organizations->removeBehavior('OrganizationsParams');

        $this->set('request', $this->request->getQuery());

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

                $request = $this->request->getData();
                $q = $this->getStringToRequest($request, 'search_');
                return $this->redirect(['action' => 'index', '?' => $q]);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Organizations Pay'));
        }

        $organizations = $this->OrganizationsPays->Organizations->find('list', [
                'conditions' => ['Organizations.stato' => 'Y', 'type' => 'GAS'],
                'order' => ['Organizations.name' => 'asc'],
                'limit' => 500]);
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
