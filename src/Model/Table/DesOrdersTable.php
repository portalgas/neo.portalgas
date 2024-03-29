<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class DesOrdersTable extends Table
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

        $this->setTable('k_des_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DesSuppliers', [
            'foreignKey' => 'des_supplier_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER',
        ]);

        /*
         * ordini DES di tutti i GAS
         */        
        $this->hasMany('DesOrdersOrganizations', [
            'foreignKey' => ['des_order_id'], 
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
            ->scalar('luogo')
            ->maxLength('luogo', 255)
            ->requirePresence('luogo', 'create')
            ->notEmptyString('luogo');

        $validator
            ->scalar('nota')
            ->requirePresence('nota', 'create')
            ->notEmptyString('nota');

        $validator
            ->scalar('nota_evidenza')
            ->requirePresence('nota_evidenza', 'create')
            ->notEmptyString('nota_evidenza');

        $validator
            ->date('data_fine_max')
            ->requirePresence('data_fine_max', 'create')
            ->notEmptyDate('data_fine_max');

        $validator
            ->scalar('hasTrasport')
            ->notEmptyString('hasTrasport');

        $validator
            ->numeric('trasport')
            ->notEmptyString('trasport');

        $validator
            ->scalar('hasCostMore')
            ->notEmptyString('hasCostMore');

        $validator
            ->numeric('cost_more')
            ->notEmptyString('cost_more');

        $validator
            ->scalar('hasCostLess')
            ->notEmptyString('hasCostLess');

        $validator
            ->numeric('cost_less')
            ->notEmptyString('cost_less');

        $validator
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->notEmptyString('state_code');

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
        $rules->add($rules->existsIn(['des_id'], 'Des'));
        $rules->add($rules->existsIn(['des_supplier_id'], 'DesSuppliers'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));

        return $rules;
    }

    /*
     * dati utilizzati  $this->element('boxDesOrderToOrder', array('results' => $desOrdersResults));    
     */
    public function getDesOrder($user, $des_order_id, $debug = false) {

        $suppliersTable = TableRegistry::get('Suppliers');
        $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');
        $organizationsTable = TableRegistry::get('Organizations');
   
        $where = ['DesOrders.id' => $des_order_id];
        /*
         * se arrivo da FO o sono ROOT e' vuoto! 
         */
        if(!empty($user->des_id)) 
            $where += ['DesOrders.des_id' => $user->des_id];
        
        $results = $this->find()->contain(['Des', 'DesSuppliers'])
                                ->where($where)->first();
        if (empty($results))
            return;

        /*
         * estraggo OwnOrganization
         */
        $where = ['Organizations.id' => $results->des_supplier->own_organization_id];
        $ownOrganizationResults = $organizationsTable->find()->where($where)->first();

        /*
         * estraggo produttore
         */
        $where = ['Suppliers.id' => $results->des_supplier->supplier_id];
        $supplierResults = $suppliersTable->find()->where($where)->first();

        /*
         * estraggo ordini dei GAS (DesOrdersOrganizations) associati al DesOrders
         */
        $where = [];
        $where = ['DesOrdersOrganizations.des_order_id' => $results->id];
        /*
         * se arrivo da FO o sono ROOT e' vuoto! 
         */
        if(!empty($user->des_id)) 
            $where += ['DesOrdersOrganizations.des_id' => $user->des_id];

        $desOrdersOrganizationsResults = $desOrdersOrganizationsTable
                                                ->find()
                                                ->contain(['Organizations', 'Orders'])
                                                ->where($where)->all();

        $results['OwnOrganization'] = $ownOrganizationResults;
        $results['Supplier'] = $supplierResults;
        $results['DesOrdersOrganizations'] = $desOrdersOrganizationsResults;

        return $results;
    }
}
