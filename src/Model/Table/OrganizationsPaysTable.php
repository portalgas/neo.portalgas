<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrganizationsPaysTable extends Table
{
    const BENEFICIARIO_PAY_FRANCESCO = 'Francesco';
    const BENEFICIARIO_PAY_MARCO = 'Marco';
    const TYPE_PAY_RICEVUTA = 'Ricevuta';
    const TYPE_PAY_RITENUTA = 'Ritenuta';

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

        /*
         impostato valore di default Configure::read('DB.field.date.empty')
        $validator
            ->date('data_pay')
            ->requirePresence('data_pay', 'create')
            ->notEmptyDate('data_pay');
        */
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
            ->numeric('import_additional_cost')
            ->requirePresence('import_additional_cost', 'create')
            ->notEmptyString('import_additional_cost');

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

    public function isSaldato($user, $organizationsPay, $debug=false) {

        if($organizationsPay->data_pay->format('Y-m-d') != Configure::read('DB.field.date.empty'))
            return true;
        else
            return false;
    }

    /*
     * in base agli utenti gestisce la fasce
     */
    public function getImportoLabel($user, $organization_id, $year, $tot_users, $debug=false) {

        $results = [];

        if($year < Configure::read('OrganizationPayFasceYearStart')) {
            /*
             * calcolo a persona
             */
            $results['importo'] = (Configure::read('costToUser') * (float)$tot_users);
            
            if($results['importo'] > Configure::read('OrganizationPayImportMax')) {
                $results['importo'] = Configure::read('OrganizationPayImportMax');
                $results['importo_nota'] = ' <span>(max)</span>';
            }
            else
                $results['importo_nota'] = '';
            
            $results['importo_e'] = number_format($results['importo'],2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).'&nbsp;&euro;';
        }
        else {
            if($tot_users<=25) 
                $importo = 25;
             else
             if($tot_users>25 && $tot_users<=50) 
                $importo = 50;
             else
             if($tot_users>50 && $tot_users<=75) 
                $importo = 75;
             else
             if($tot_users>75) 
                $importo = 100;

            $results['importo'] = $importo; 
            $results['importo_e'] = number_format($importo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).'&nbsp;&euro;';
            $results['importo_nota'] = '';
        }

        return $results;
    }


    /*
     * in base agli utenti gestisce la fasce
     */
    public function getImporto($user, $organization_id, $year, $tot_users, $debug) {

        $results = 0;

        if($year >= Configure::read('OrganizationPayFasceYearStart')) {
            if($tot_users<=25) 
                $results = 25;
             else
             if($tot_users>25 && $tot_users<=50) 
                $results = 50;
             else
             if($tot_users>50 && $tot_users<=75) 
                $results = 75;
             else
             if($tot_users>75) 
                $results = 100;
        }

        return $results;
    }    
}
