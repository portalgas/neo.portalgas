<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProdGasPromotionsOrganizationsDeliveriesTable extends Table
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

        $this->setTable('k_prod_gas_promotions_organizations_deliveries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProdGasPromotions', [
            'foreignKey' => 'prod_gas_promotion_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => ['organization_id', 'delivery_id'],
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('isConfirmed')
            ->notEmptyString('isConfirmed');

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
        $rules->add($rules->existsIn(['supplier_id'], 'Suppliers'));
        $rules->add($rules->existsIn(['prod_gas_promotion_id'], 'ProdGasPromotions'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['delivery_id'], 'Deliveries'));

        return $rules;
    }

    public function getsList($user, $organization_id, $where=[], $debug=false) {

        $listResults = [];

        $results = $this->gets($user, $organization_id, $where);
        if(!empty($results)) {
            foreach($results as $result) {
                 /*
                  * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                  * key array non per id, nel json perde l'ordinamento della data
                  * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM yyyy');
                  */                
                $listResults[$result->delivery->id] = $result->delivery->data->i18nFormat('eeee d MMMM').' - '.$result->delivery->luogo;
            }
        }
   
        // debug($listResults);
        return $listResults;
    } 
    

    public function gets($user, $organization_id, $where=[], $debug=false) {

        $results = [];

        $where_promotions = [];
        if(isset($where['ProdGasPromotionsOrganizationsDeliveries']))
            $where_promotions = $where['ProdGasPromotionsOrganizationsDeliveries'];
        $where_promotions = array_merge(['ProdGasPromotionsOrganizationsDeliveries.organization_id' => $organization_id], $where_promotions);

        $where_delivery = [];
        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id],  $where_delivery);
                           
        if($debug) debug($where_promotions);
        if($debug) debug($where_delivery);
        $results = $this->find()
                                ->where($where_promotions)
                                ->contain(['Deliveries' => ['conditions' => $where_delivery]])
                                ->order(['Deliveries.data'])
                                ->all();

        // debug($results);
      
        return $results;
    }       
}
