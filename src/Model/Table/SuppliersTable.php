<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class SuppliersTable extends Table
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

        $this->setTable('k_suppliers');
        $this->setAlias('Suppliers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CategoriesSuppliers', [
            'foreignKey' => 'category_supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('OwnerOrganizations', [
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER'
        ]); 
        $this->belongsTo('Content', [
            'foreignKey' => 'j_content_id',
            'joinType' => 'LEFT'
        ]); 
        $this->hasMany('SuppliersOrganizations', [
            'foreignKey' => 'supplier_id'
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
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 50)
            ->allowEmptyString('nome');

        $validator
            ->scalar('cognome')
            ->maxLength('cognome', 50)
            ->allowEmptyString('cognome');

        $validator
            ->scalar('descrizione')
            ->allowEmptyString('descrizione');

        $validator
            ->scalar('indirizzo')
            ->maxLength('indirizzo', 50)
            ->allowEmptyString('indirizzo');

        $validator
            ->scalar('localita')
            ->maxLength('localita', 50)
            ->allowEmptyString('localita');

        $validator
            ->scalar('cap')
            ->maxLength('cap', 5)
            ->allowEmptyString('cap');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 2)
            ->allowEmptyString('provincia');

        $validator
            ->scalar('lat')
            ->maxLength('lat', 15)
            ->requirePresence('lat', 'create')
            ->notEmptyString('lat');

        $validator
            ->scalar('lng')
            ->maxLength('lng', 15)
            ->requirePresence('lng', 'create')
            ->notEmptyString('lng');

        $validator
            ->scalar('telefono')
            ->maxLength('telefono', 20)
            ->allowEmptyString('telefono');

        $validator
            ->scalar('telefono2')
            ->maxLength('telefono2', 20)
            ->allowEmptyString('telefono2');

        $validator
            ->scalar('fax')
            ->maxLength('fax', 20)
            ->allowEmptyString('fax');

        $validator
            ->scalar('mail')
            ->maxLength('mail', 100)
            ->allowEmptyString('mail');

        $validator
            ->scalar('www')
            ->maxLength('www', 100)
            ->allowEmptyString('www');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('cf')
            ->maxLength('cf', 16)
            ->allowEmptyString('cf');

        $validator
            ->scalar('piva')
            ->maxLength('piva', 11)
            ->allowEmptyString('piva');

        $validator
            ->scalar('conto')
            ->maxLength('conto', 50)
            ->allowEmptyString('conto');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 50)
            ->requirePresence('img1', 'create')
            ->notEmptyString('img1');

        $validator
            ->scalar('can_promotions')
            ->notEmptyString('can_promotions');

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
        $rules->add($rules->existsIn(['category_supplier_id'], 'CategoriesSuppliers'));
        $rules->add($rules->existsIn(['owner_organization_id'], 'OwnerOrganizations'));

        return $rules;
    }

    public function getByVatNumber($vatNumber) {

        $where = ['Suppliers.piva' => $vatNumber];
        $results = $this->find()
                    ->where($where)
                    ->first();
        return $results;
    }  

    public function getById($user, $supplier_id, $where=[], $debug=false) {

        $results = [];

        /*
         * prendo anche i Organizations.stato cosi' su /site/produttori compaiono i gas associati 
         */
        if(!isset($where['Organizations']))
           $where['Organizations'] = [];
        $where['Organizations'] = array_merge(['Organizations.stato IN' => ['Y', 'N'], 'Organizations.type IN' => ['GAS']], $where['Organizations']);
        
        if(!isset($where['SuppliersOrganizations']))
           $where['SuppliersOrganizations'] = [];
        $where['SuppliersOrganizations'] = array_merge(['SuppliersOrganizations.stato' => 'Y'], $where['SuppliersOrganizations']);
        
        if(!isset($where['Suppliers']))
           $where['Suppliers'] = [];
        $where['Suppliers'] = array_merge(['Suppliers.id' => $supplier_id], $where['Suppliers']);

        $results = $this->find()
                        ->contain(['Content', 'CategoriesSuppliers',
                                    'SuppliersOrganizations' => 
                                      ['conditions' => $where['SuppliersOrganizations'], 'Organizations' => ['conditions' =>  $where['Organizations']]],
                        ])
                        ->where($where['Suppliers'])
                        ->first();

        return $results;
    }
}
