<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Traits;

class SupplierOrganizationCashExcludedsTable extends Table
{
    use Traits\SqlTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('supplier_organization_cash_excludeds');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            'joinType' => 'INNER',
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
            ->allowEmptyString('id', null, 'create');

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
        $rules->add($rules->existsIn(['organization_id', 'supplier_organization_id'], 'SuppliersOrganizations'));

        return $rules;
    }

    public function gets($user, $where = []) {

        $results = [];

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $where = array_merge(['SuppliersOrganizations.stato' => 'Y', 'Suppliers.stato IN ' => ['Y', 'T']], $where);
        // debug($where);
        $suppliersOrganizations = $suppliersOrganizationsTable->gets($user, $where);

        if(!empty($suppliersOrganizations)) {

            foreach($suppliersOrganizations as $numResult => $suppliersOrganization) {

                $results[$numResult] = $suppliersOrganization;

                $where = ['SupplierOrganizationCashExcludeds.supplier_organization_id' => $suppliersOrganization->id,
                          'SupplierOrganizationCashExcludeds.organization_id' => $suppliersOrganization->organization_id];
                // debug($where);
                $supplierOrganizationCashExcludeds = $this->find()
                                                            ->where($where)
                                                            ->first();
                if(!empty($supplierOrganizationCashExcludeds)) {
                    $results[$numResult]['supplierOrganizationCashExcludeds'] = $supplierOrganizationCashExcludeds;
                }
                else
                    $results[$numResult]['supplierOrganizationCashExcludeds'] = '';
            } // foreach 
        }
        
        // debug($results);
        return $results;
    }  

    public function isSupplierOrganizationCashExcluded($user, $organization_id, $supplier_organization_id, $debug=false) {

        $where = ['SupplierOrganizationCashExcludeds.supplier_organization_id' => $supplier_organization_id,
                  'SupplierOrganizationCashExcludeds.organization_id' => $organization_id];
         
        $results = $this->getCount('SupplierOrganizationCashExcludeds', $where);
        // debug($where);
        // debug($results);

        if($results==0)
            return false;
        else
            return true;
    }
}