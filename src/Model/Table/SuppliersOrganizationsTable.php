<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class SuppliersOrganizationsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_suppliers_organizations');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CategoriesSuppliers', [
            'foreignKey' => 'category_supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('OwnerOrganizations', [
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('OwnerSupplierOrganizations', [
            'foreignKey' => 'owner_supplier_organization_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Articles', [
            'foreignKey' => ['organization_id', 'supplier_organization_id']
        ]); 
        $this->hasMany('SuppliersOrganizationsReferents', [
            'foreignKey' => ['organization_id', 'supplier_organization_id']
        ]);          
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 225)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('frequenza')
            ->maxLength('frequenza', 50)
            ->allowEmptyString('frequenza');

        $validator
            ->scalar('owner_articles')
            ->notEmptyString('owner_articles');

        $validator
            ->scalar('can_view_orders')
            ->notEmptyString('can_view_orders');

        $validator
            ->scalar('can_view_orders_users')
            ->notEmptyString('can_view_orders_users');

        $validator
            ->scalar('can_promotions')
            ->notEmptyString('can_promotions');

        $validator
            ->scalar('mail_order_open')
            ->notEmptyString('mail_order_open');

        $validator
            ->scalar('mail_order_close')
            ->notEmptyString('mail_order_close');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['supplier_id'], 'Suppliers'));
        /*
         * disabilita perche' al primo insert e' 0
         * poi con AfterSave lo aggiorno
        $rules->add($rules->existsIn(['category_supplier_id'], 'CategoriesSuppliers')); 
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));
        $rules->add($rules->existsIn(['owner_supplier_organization_id'], 'OwnerSupplierOrganizations'));
        */
        return $rules;
    }

    /*
     * di default l'owner (proprietario del listino) e' il REFERENT, se no $data_override
     */
    public function create($organization_id, $supplier, $data_override=[], $debug = false)
    {
        $results = [];
        $results['esito'] = true;
        $results['code'] = '200';
        $results['msg'] = '';
        $results['msg_human'] = '';
        $results['datas'] = []; 

        // debug($supplier);

        $data = [];
        $data['organization_id'] = $organization_id;
        $data['supplier_id'] = $supplier->id;
        $data['name'] = $supplier->name;
        $data['category_supplier_id'] = 0;
        $data['frequenza'] = '';
        $data['stato'] = Configure::read('SupplierOrganizationStatoIni');
        $data['mail_order_open'] = Configure::read('SupplierOrganizationMailOrderOpenIni');
        $data['mail_order_close'] = Configure::read('SupplierOrganizationMailOrderCloseIni');
        if(!empty($data_override) && isset($data_override['owner_articles']))
            $data['owner_articles'] = $data_override['owner_articles'];
        else 
            $data['owner_articles'] = Configure::read('SupplierOrganizationOwnerArticlesIni'); 
        $data['can_view_orders'] = Configure::read('SupplierOrganizationCanViewOrdersIni');
        $data['can_view_orders_users'] = Configure::read('SupplierOrganizationCanViewOrdersUserIni');
        $data['can_promotions'] = Configure::read('SupplierOrganizationCanPromotionsIni');
        
        /*
         * dopo il salvataggio recupero SupplierOrganization.id e aggiorno
         */
        if(!empty($data_override) && isset($data_override['owner_supplier_organization_id']))
            $data['owner_supplier_organization_id'] = $data_override['owner_supplier_organization_id'];
        else
            $data['owner_supplier_organization_id'] = 0;
        if(!empty($data_override) && isset($data_override['owner_organization_id']))
            $data['owner_organization_id'] = $data_override['owner_organization_id'];
        else
            $data['owner_organization_id'] = 0;
        if($debug) debug($data);
        $entity = $this->newEntity();
        $entity = $this->patchEntity($entity, $data);
        // debug($entity);
        if (!$this->save($entity)) {
            $results['esito'] = false;
            $results['code'] = '500';
            $results['msg'] = $entity->getErrors();
            $results['msg_human'] = "Errore nell'inserimento del porduttore";
            $results['datas'] = $entity->getErrors();              
        }
        else {
            if(empty($data['owner_supplier_organization_id']) || empty($data['owner_organization_id'])) {

                if(empty($data['owner_supplier_organization_id']))
                    $data['owner_supplier_organization_id'] = $entity->id;
                if(empty($data['owner_organization_id']))
                    $data['owner_organization_id'] = $entity->organization_id;
                if($debug) debug($data);

                $entity = $this->patchEntity($entity, $data);
                if (!$this->save($entity)) {
                    $results['esito'] = false;
                    $results['code'] = '500';
                    $results['msg'] = $entity->getErrors();
                    $results['msg_human'] = "Errore nell'aggiornamento del porduttore";
                    $results['datas'] = $entity->getErrors();   
                } 
                else 
                    $results['datas'] = $entity;
            }  // end if(empty($data['owner_supplier_organization_id']) || empty($data['owner_organization_id']))              
            else
                $results['datas'] = $entity;
        }

        return $results; 
    }   

    public function get($user, $where = []) {

        $where = array_merge(['SuppliersOrganizations.organization_id' => $user->organization->id], $where);
        // debug($where);
        $results = $this->find()
                                ->where($where)
                                ->contain(['Suppliers', 'CategoriesSuppliers'])
                                ->order(['SuppliersOrganizations.name'])
                                ->first();
        if(!empty($results)) {

            $config = Configure::read('Config');
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $url = $portalgas_fe_url.Configure::read('Supplier.img.path.full');
                        
            $results['img1'] = sprintf($url, $results['supplier']['img1']); 
        }

        // debug($results);
        return $results;
    }

    public function gets($user, $where = []) {
   
        $where = array_merge(['SuppliersOrganizations.organization_id' => $user->organization->id], $where);
        // debug($where);
        $results = $this->find()
                                ->where($where)
                                ->contain(['Suppliers', 'CategoriesSuppliers'])
                                ->order(['SuppliersOrganizations.name'])
                                ->all();

        // debug($results);
        return $results;
    } 

    public function getsList($user, $where = []) {

        $where = array_merge(['SuppliersOrganizations.organization_id' => $user->organization->id], $where);
        // debug($where);
        $results = $this->find('list', ['keyField' => 'id', // perche' ha la doppia key
                                        'valueField' => 'name'])        
                        ->where($where)
                        ->order(['SuppliersOrganizations.name']);

        // debug($results);
        return $results;
    } 

    public function ACLgets($user, $organization_id, $user_id, $where=[]) {
        
        if($user->acl['isSuperReferente']) {
            /* 
             * SUPER-REFERENTE
             */             
            if(isset($where['SuppliersOrganizations']))
                $where = array_merge($where['SuppliersOrganizations'], ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']]);
            else 
                $where = ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']];
            return $this->gets($user, $where);
        }
        else {
            /* 
             * REFERENTE
             */            
            $results = [];
            $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');
            $suppliersOrganizationsReferents = $suppliersOrganizationsReferentsTable->gets($user, $where);
            foreach($suppliersOrganizationsReferents as $suppliersOrganizationsReferent) {
                $results[$suppliersOrganizationsReferent->suppliers_organization->id] = $suppliersOrganizationsReferent->suppliers_organization->name;
            }
            return $results;
        }
    }
   
    public function ACLgetsList($user, $organization_id, $user_id, $where=[]) {
        
        if($user->acl['isSuperReferente']) {
            if(isset($where['SuppliersOrganizations']))
                $where = array_merge($where['SuppliersOrganizations'], ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']]);
            else 
                $where = ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']];
            return $this->getsList($user, $where);
        }
        else {
            $results = [];
            $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');
            $suppliersOrganizationsReferents = $suppliersOrganizationsReferentsTable->getsList($user, $where);
            foreach($suppliersOrganizationsReferents as $suppliersOrganizationsReferent) {
                $results[$suppliersOrganizationsReferent->suppliers_organization->id] = $suppliersOrganizationsReferent->suppliers_organization->name;
            }
            return $results;            
        }
    }

    public function ACLgetsIds($user, $organization_id, $user_id, $where=[]) {
        
        $ids = [];
        $results = []; 
        if($user->acl['isSuperReferente']) {
            if(isset($where['SuppliersOrganizations']))
                $where = array_merge($where['SuppliersOrganizations'], ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']]);
            else 
                $where = ['SuppliersOrganizations.stato IN ' => ['Y', 'P', 'T']];
            $results = $this->getsList($user, $where);
        }
        else {
            $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');
            $suppliersOrganizationsReferents = $suppliersOrganizationsReferentsTable->getsList($user, $where);
            foreach($suppliersOrganizationsReferents as $suppliersOrganizationsReferent) {
                $results[$suppliersOrganizationsReferent->suppliers_organization->id] = $suppliersOrganizationsReferent->suppliers_organization->name;
            }
        }

        if(!empty($results)) {
            foreach($results as $result) {
                array_push($ids, $result->id);
            }
        }

        return $ids;
    }

    /*
    $ACLsuppliersIdsOrganization = 0; // contiene stringa supplier_organization_id 1, 3, 5
    if($this->Controller->isSuperReferente()) {
        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $ACLsuppliersIdsOrganization = $SuppliersOrganization->getSuppliersOrganizationIds($user);
    } else {
        App::import('Model', 'SuppliersOrganizationsReferent');
        $SuppliersOrganizationsReferent = new SuppliersOrganizationsReferent;

        $ACLsuppliersIdsOrganization = $SuppliersOrganizationsReferent->getSuppliersOrganizationIdsByReferent($user, $user->get('id'));
    }
    $user->set('ACLsuppliersIdsOrganization', $ACLsuppliersIdsOrganization);
*/
    /*
     *  estraggo il proprietario del listino del produttre  del GAS 
     *  $owner_articles = 'REFERENT', 'SUPPLIER', 'DES'
     *  owner_organization_id del produttore
     *  owner_supplier_organization_id del produttore
     *
     * quando creo un ordine li copio in Order.owner_...
     */
    public function getOwnArticles($user, $organization_id, $supplier_organization_id, $debug=false) {

        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
                  'SuppliersOrganizations.id' => $supplier_organization_id
                 ];
        
        if($debug) debug($where);
        
        $results = $this->find()
                            ->select(['SuppliersOrganizations.owner_articles', 
                                      'SuppliersOrganizations.owner_organization_id', 
                                      'SuppliersOrganizations.owner_supplier_organization_id'])
                            ->where($where)
                            ->first();
        if($debug) debug($results);
        
        return $results;  
    }  

    /*
     *  estraggo il produttore del GAS che abbiama concesso la gestione del listino al produttore
     *  $owner_articles = 'SUPPLIER'
     *  owner_organization_id del produttore
     *  owner_supplier_organization_id del produttore
     */
    public function getOwnSupplierBySupplierId($user, $supplier_id, $owner_organization_id, $owner_supplier_organization_id, $owner_articles = 'SUPPLIER', $debug=false) {

        $where = ['SuppliersOrganizations.supplier_id' => $supplier_id,
                  'SuppliersOrganizations.owner_articles' => $owner_articles,
                  'SuppliersOrganizations.owner_organization_id' => $owner_organization_id,
                  'SuppliersOrganizations.owner_supplier_organization_id' => $owner_supplier_organization_id];
        
        $where = array_merge(['SuppliersOrganizations.organization_id' => $user->organization->id], $where);
        if($debug) debug($where);
        
        $results = $this->find()
                            ->where($where)
                            ->contain(['Suppliers', 'CategoriesSuppliers'])
                            ->order(['SuppliersOrganizations.name'])
                            ->first();
        if($debug) debug($results);
        
        return $results;  
    }  
}