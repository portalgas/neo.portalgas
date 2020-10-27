<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;

class OrganizationsTable extends Table
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

        $this->setTable('k_organizations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
		// lo associo quando serve $this->addBehavior('OrganizationsParams');
		
        $this->belongsTo('Templates', [
            'foreignKey' => 'template_id',
            'joinType' => 'INNER'
        ]);
        /*
        $this->belongsTo('JPageCategories', [
            'foreignKey' => 'j_page_category_id',
            'joinType' => 'INNER'
        ]);
		*/

        $this->hasMany('OrganizationsPays', [
            'foreignKey' => 'organization_id',
        ]); 

        /*
         * se l'organization type = PACT / PRODGAS
         *  e il legame con il produttore associato
         */
        $this->hasOne('SuppliersOrganizations');                 
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
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

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
            ->requirePresence('cap', 'create')
            ->allowEmptyString('cap');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 2)
            ->allowEmptyString('provincia');

        $validator
            ->scalar('telefono')
            ->maxLength('telefono', 20)
            ->allowEmptyString('telefono');

        $validator
            ->scalar('telefono2')
            ->maxLength('telefono2', 20)
            ->allowEmptyString('telefono2');

        $validator
            ->scalar('mail')
            ->maxLength('mail', 100)
            ->allowEmptyString('mail');

        $validator
            ->scalar('www')
            ->maxLength('www', 100)
            ->allowEmptyString('www');

        $validator
            ->scalar('www2')
            ->maxLength('www2', 100)
            ->allowEmptyString('www2');

        $validator
            ->scalar('sede_logistica_1')
            ->maxLength('sede_logistica_1', 256)
            ->allowEmptyString('sede_logistica_1');

        $validator
            ->scalar('sede_logistica_2')
            ->maxLength('sede_logistica_2', 256)
            ->allowEmptyString('sede_logistica_2');

        $validator
            ->scalar('sede_logistica_3')
            ->maxLength('sede_logistica_3', 256)
            ->allowEmptyString('sede_logistica_3');

        $validator
            ->scalar('sede_logistica_4')
            ->maxLength('sede_logistica_4', 256)
            ->allowEmptyString('sede_logistica_4');

        $validator
            ->scalar('cf')
            ->maxLength('cf', 16)
            ->allowEmptyString('cf');

        $validator
            ->scalar('piva')
            ->maxLength('piva', 11)
            ->allowEmptyString('piva');

        $validator
            ->scalar('banca')
            ->maxLength('banca', 250)
            ->allowEmptyString('banca');

        $validator
            ->scalar('banca_iban')
            ->maxLength('banca_iban', 27)
            ->allowEmptyString('banca_iban');

        $validator
            ->scalar('lat')
            ->maxLength('lat', 15)
            ->allowEmptyString('lat');

        $validator
            ->scalar('lng')
            ->maxLength('lng', 15)
            ->allowEmptyString('lng');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 15)
            ->allowEmptyString('img1');

        $validator
            ->integer('j_group_registred')
            ->requirePresence('j_group_registred', 'create')
            ->notEmptyString('j_group_registred');

        $validator
            ->scalar('j_seo')
            ->maxLength('j_seo', 50)
            ->allowEmptyString('j_seo');

        $validator
            ->scalar('type')
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('paramsConfig')
            ->requirePresence('paramsConfig', 'create')
            ->notEmptyString('paramsConfig');

        $validator
            ->scalar('paramsFields')
            ->requirePresence('paramsFields', 'create')
            ->notEmptyString('paramsFields');

        $validator
            ->scalar('paramsPay')
            ->requirePresence('paramsPay', 'create')
            ->notEmptyString('paramsPay');

        $validator
            ->scalar('hasMsg')
            ->notEmptyString('hasMsg');

        $validator
            ->scalar('msgText')
            ->allowEmptyString('msgText');

        $validator
            ->scalar('stato')
            ->requirePresence('stato', 'create')
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
        $rules->add($rules->existsIn(['template_id'], 'Templates'));

        return $rules;
    }

    public function factory($organization_type_id) {

        $table_registry = '';

        switch (strtoupper($organization_type_id)) {
            case Configure::read('Organization.type.gas'):
                $table_registry = 'OrganizationsGas';
                break;
            case Configure::read('Organization.type.prodgas'):
                $table_registry = 'OrganizationsProdGas';
                break;
            case Configure::read('Organization.type.pact'):
                $table_registry = 'OrganizationsPact';
                break;
            default:
                die('OrganizationsTable organization_type_id ['.$organization_type_id.'] non previsto');
                break;
        }

        return TableRegistry::get($table_registry);
    }    
}
