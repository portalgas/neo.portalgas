<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OrganizationsPaysTable extends Table
{
    const BENEFICIARIO_PAY_FRANCESCO = 'Francesco';
    const BENEFICIARIO_PAY_MARCO = 'Marco';
    const TYPE_PAY_RITENUTA = 'Ritenuta';
    const TYPE_PAY_RICEVUTA = 'Ricevuta';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_organizations_pays');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'beneficiario_pay' => [
                'strategy' => 'const',
                'prefix' => 'BENEFICIARIO_PAY'
            ],
            'type_pay' => [
                'strategy' => 'const',
                'prefix' => 'TYPE_PAY'
            ]
        ]]);

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
            ->scalar('year')
            ->maxLength('year', 4)
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->date('data_pay')
            ->requirePresence('data_pay', 'create')
            ->notEmptyDate('data_pay');

        $validator
            ->scalar('beneficiario_pay')
            ->maxLength('beneficiario_pay', 50)
            ->requirePresence('beneficiario_pay', 'create')
            ->notEmptyString('beneficiario_pay');

        $validator
            ->nonNegativeInteger('tot_users')
            ->requirePresence('tot_users', 'create')
            ->notEmptyString('tot_users');

        $validator
            ->integer('tot_orders')
            ->requirePresence('tot_orders', 'create')
            ->notEmptyString('tot_orders');

        $validator
            ->integer('tot_suppliers_organizations')
            ->notEmptyString('tot_suppliers_organizations');

        $validator
            ->integer('tot_articles')
            ->requirePresence('tot_articles', 'create')
            ->notEmptyString('tot_articles');

        $validator
            ->numeric('importo')
            ->requirePresence('importo', 'create')
            ->notEmptyString('importo');

        $validator
            ->scalar('type_pay')
            ->notEmptyString('type_pay');

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
