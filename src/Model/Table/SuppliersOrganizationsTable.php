<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * SuppliersOrganizations Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SuppliersTable&\Cake\ORM\Association\BelongsTo $Suppliers
 * @property \App\Model\Table\CategorySuppliersTable&\Cake\ORM\Association\BelongsTo $CategorySuppliers
 * @property \App\Model\Table\OwnerOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerOrganizations
 * @property \App\Model\Table\OwnerSupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerSupplierOrganizations
 *
 * @method \App\Model\Entity\KSuppliersOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KSuppliersOrganization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
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
        $this->setPrimaryKey('id');

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
            'foreignKey' => 'supplier_organization_id'
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

    public function create($organization_id, $supplier)
    {
        $debug = false;
        $esito = true;
        $code = '200';
        $msg = '';
        $results = []; 

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
        $data['owner_articles'] = Configure::read('SupplierOrganizationOwnerArticlesIni'); 
        $data['can_view_orders'] = Configure::read('SupplierOrganizationCanViewOrdersIni');
        $data['can_view_orders_users'] = Configure::read('SupplierOrganizationCanViewOrdersUserIni');
        $data['can_promotions'] = Configure::read('SupplierOrganizationCanPromotionsIni');
        
        /*
         * dopo il salvataggio recupero SupplierOrganization.id e aggiorno
         */
        $data['owner_supplier_organization_id'] = 0;
        $data['owner_organization_id'] = 0;
        if($debug) debug($data);
        
        $entity = $this->newEntity();
        $entity = $this->patchEntity($entity, $data);
        if (!$this->save($entity)) {
            $esito = false;
            $code = '500';
            $msg = '';
            $results = $entity->getErrors();             
        }
        else {
            $data['owner_supplier_organization_id'] = $entity->id;
            $data['owner_organization_id'] = $entity->organization_id;
            if($debug) debug($data);

            $entity = $this->patchEntity($entity, $data);
            if (!$this->save($entity)) {
                $esito = false;
                $code = '500';
                $msg = '';
                $results = $entity->getErrors();             
            }                 
        }

        $results = ['esito' => $esito, 'code' => $code, 'msg' => $msg, 'results' => $results];

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

    /*
     * estraggo il produttore del GAS che abbiama concesso la gestione del listino al produttore
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
