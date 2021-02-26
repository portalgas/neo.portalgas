<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class ProdGasPromotionsOrganizationsTable extends Table
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

        $this->setTable('k_prod_gas_promotions_organizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ProdGasPromotions', [
            'foreignKey' => 'prod_gas_promotion_id',
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
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->scalar('nota_supplier')
            ->allowEmptyString('nota_supplier');

        $validator
            ->scalar('nota_user')
            ->requirePresence('nota_user', 'create')
            ->notEmptyString('nota_user');

        $validator
            ->scalar('state_code')
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
        $rules->add($rules->existsIn(['prod_gas_promotion_id'], 'ProdGasPromotions'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function hasGasUsersPromotions($organization_id) {

        $where = ['ProdGasPromotionsOrganizations.organization_id' => $organization_id,
                  'ProdGasPromotions.type' => 'GAS-USERS'];

        $results = $this->find()
                        ->contain(['ProdGasPromotions'])
                        ->where($where)
                        ->count();
        // debug($results);

        if($results==0)
            return false;
        else
            return true;
    }
}
