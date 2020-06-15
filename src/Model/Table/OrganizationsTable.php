<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KOrganizations Model
 *
 * @property \App\Model\Table\TemplatesTable&\Cake\ORM\Association\BelongsTo $Templates
 * @property \App\Model\Table\JPageCategoriesTable&\Cake\ORM\Association\BelongsTo $JPageCategories
 * @property \App\Model\Table\GcalendarsTable&\Cake\ORM\Association\BelongsTo $Gcalendars
 *
 * @method \App\Model\Entity\KOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\KOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KOrganization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
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
		$this->addBehavior('OrganizationsParams');
		
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
            ->requirePresence('descrizione', 'create')
            ->notEmptyString('descrizione');

        $validator
            ->scalar('indirizzo')
            ->maxLength('indirizzo', 50)
            ->requirePresence('indirizzo', 'create')
            ->notEmptyString('indirizzo');

        $validator
            ->scalar('localita')
            ->maxLength('localita', 50)
            ->requirePresence('localita', 'create')
            ->notEmptyString('localita');

        $validator
            ->scalar('cap')
            ->maxLength('cap', 5)
            ->requirePresence('cap', 'create')
            ->notEmptyString('cap');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 2)
            ->requirePresence('provincia', 'create')
            ->notEmptyString('provincia');

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
            ->requirePresence('www2', 'create')
            ->notEmptyString('www2');

        $validator
            ->scalar('sede_logistica_1')
            ->maxLength('sede_logistica_1', 256)
            ->requirePresence('sede_logistica_1', 'create')
            ->notEmptyString('sede_logistica_1');

        $validator
            ->scalar('sede_logistica_2')
            ->maxLength('sede_logistica_2', 256)
            ->requirePresence('sede_logistica_2', 'create')
            ->notEmptyString('sede_logistica_2');

        $validator
            ->scalar('sede_logistica_3')
            ->maxLength('sede_logistica_3', 256)
            ->requirePresence('sede_logistica_3', 'create')
            ->notEmptyString('sede_logistica_3');

        $validator
            ->scalar('sede_logistica_4')
            ->maxLength('sede_logistica_4', 256)
            ->requirePresence('sede_logistica_4', 'create')
            ->notEmptyString('sede_logistica_4');

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
            ->requirePresence('banca_iban', 'create')
            ->notEmptyString('banca_iban');

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
            ->scalar('img1')
            ->maxLength('img1', 15)
            ->requirePresence('img1', 'create')
            ->notEmptyString('img1');

        $validator
            ->integer('j_group_registred')
            ->requirePresence('j_group_registred', 'create')
            ->notEmptyString('j_group_registred');

        $validator
            ->scalar('j_seo')
            ->maxLength('j_seo', 50)
            ->requirePresence('j_seo', 'create')
            ->notEmptyString('j_seo');

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
}
