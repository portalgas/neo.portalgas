<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProdGasPromotionsTable extends Table
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

        $this->setTable('k_prod_gas_promotions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
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
            ->scalar('name')
            ->maxLength('name', 150)
            ->allowEmptyString('name');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 50)
            ->allowEmptyString('img1');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->requirePresence('data_fine', 'create')
            ->notEmptyDate('data_fine');

        $validator
            ->numeric('importo_originale')
            ->notEmptyString('importo_originale');

        $validator
            ->numeric('importo_scontato')
            ->notEmptyString('importo_scontato');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('contact_name')
            ->maxLength('contact_name', 150)
            ->requirePresence('contact_name', 'create')
            ->notEmptyString('contact_name');

        $validator
            ->scalar('contact_mail')
            ->maxLength('contact_mail', 100)
            ->requirePresence('contact_mail', 'create')
            ->notEmptyString('contact_mail');

        $validator
            ->scalar('contact_phone')
            ->maxLength('contact_phone', 100)
            ->requirePresence('contact_phone', 'create')
            ->notEmptyString('contact_phone');

        $validator
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->requirePresence('state_code', 'create')
            ->notEmptyString('state_code');

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

        return $rules;
    }
}
