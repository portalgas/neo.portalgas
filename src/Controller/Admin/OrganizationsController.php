<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Organizations Controller
 *
 * @property \App\Model\Table\OrganizationsTable $Organizations
 *
 * @method \App\Model\Entity\KOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function settingParams()
    {
        $debug = true;
        $continua = true;
        $type_params = ['paramsConfig' => 'paramsConfig (parametri di configurazione)', 
                        'paramsFields' => 'paramsFields (parametri dei campi)',
                        'paramsPay' => 'paramsPay (parametri di pagamento)'];
        $types = ['GAS' => 'GAS', 'PRODGAS' => 'PRODGAS'];

        if ($this->request->is('post')) {

            $type_param = $this->request->getData('type_params'); 
            $type = $this->request->getData('types');
            $field = $this->request->getData('field');
            $value = $this->request->getData('value');
            if(empty($field) || empty($value)) {
                $this->Flash->error('Campo field e value obbligatori', ['escape' => false]);
                $continua = false;
            }
            
            if($continua) {
                $where = ['Organizations.type' => $type];
                if($debug) debug($where);

                $this->Organizations->addBehavior('OrganizationsParams');
                $organizations = $this->Organizations->find()
                                                    ->where($where)
                                                    ->order('Organizations.name')
                                                    ->all();

                /*
                 * in OrganizationsParamsBehavior::beforeSave arriva vuoto perche' la validazione non accetta un array()
                 */
                $this->Organizations->removeBehavior('OrganizationsParams');

                foreach($organizations as $numResult => $organization) {
                    
                    // if($debug) debug($organization->{$type_param});
                    if(isset($organization->{$type_param}[$field])) {
                        $organization->{$type_param}[$field] = $value;
                        $value_old = $value;
                    }
                    else {
                        $organization->{$type_param} += [$field => $value];
                        $value_old = '';
                    }
                    // if($debug) debug($organization->{$type_param});

                    if($debug) {
                        if(empty($value_old))
                            debug($numResult.') Tratto '.$organization->name.' ('.$organization->id.') INSERT in '.$type_param.' '.$field.':'.$value);
                        elseif($value_old==$value)
                            debug($numResult.') Tratto '.$organization->name.' ('.$organization->id.') SALTO in '.$type_param.' '.$field.' da '.$value_old.' a '.$value);
                        else
                            debug($numResult.') Tratto '.$organization->name.' ('.$organization->id.') UPDATE in '.$type_param.' '.$field.' da '.$value_old.' a '.$value);
                    }

                    if($value_old!=$value) {

                        $datas = [];
                        /*
                         * in OrganizationsParamsBehavior::beforeSave arriva vuoto perche' la validazione non accetta un array()
                         */
                        $datas[$type_param] = json_encode($organization->{$type_param}, true);
                        // if($debug) debug($datas);

                        $org = $this->Organizations->get($organization->id);
                        $org = $this->Organizations->patchEntity($org, $datas);
                        // if($debug) debug($org);
                        if (!$this->Organizations->save($org)) {
                            debug($org->getErrors());
                            exit;
                        }
                    } // end if($value_old!=$value)
    
                } // end foreach($organizations as $organization)               
            } // end if($continua) 
        }

        $this->set(compact('type_params', 'types'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->Organizations->addBehavior('OrganizationsParams');
        $this->paginate = [
            'contain' => ['Templates' /* , 'JPageCategories', 'Gcalendars' */],
        ];
        $organizations = $this->paginate($this->Organizations);

        $this->set(compact('organizations'));
    }

    /**
     * View method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Organizations->addBehavior('OrganizationsParams');
        $organization = $this->Organizations->get($id, [
            'contain' => ['Templates', 'JPageCategories', 'Gcalendars'],
        ]);

        $this->set('organization', $organization);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $organization = $this->Organizations->newEntity();
        if ($this->request->is('post')) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organization'));
        }
        $templates = $this->Organizations->Templates->find('list', ['limit' => 200]);
        $jPageCategories = $this->Organizations->JPageCategories->find('list', ['limit' => 200]);
        $gcalendars = $this->Organizations->Gcalendars->find('list', ['limit' => 200]);
        $this->set(compact('organization', 'templates', 'jPageCategories', 'gcalendars'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->Organizations->addBehavior('OrganizationsParams');
        $organization = $this->Organizations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Organization'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Organization'));
        }
        $templates = $this->Organizations->Templates->find('list', ['limit' => 200]);
        $jPageCategories = $this->Organizations->JPageCategories->find('list', ['limit' => 200]);
        $gcalendars = $this->Organizations->Gcalendars->find('list', ['limit' => 200]);
        $this->set(compact('organization', 'templates', 'jPageCategories', 'gcalendars'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $organization = $this->Organizations->get($id);
        if ($this->Organizations->delete($organization)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Organization'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Organization'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
