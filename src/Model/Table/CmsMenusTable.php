<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Traits;

/**
 * CmsMenus Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsMenuTypesTable&\Cake\ORM\Association\BelongsTo $CmsMenuTypes
 * @property \App\Model\Table\CmsDocsTable&\Cake\ORM\Association\HasMany $CmsDocs
 * @property \App\Model\Table\CmsPagesTable&\Cake\ORM\Association\HasMany $CmsPages
 *
 * @method \App\Model\Entity\CmsMenu get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsMenu newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsMenu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenu|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenu saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenu[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenu findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsMenusTable extends Table
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

        $this->setTable('cms_menus');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CmsMenuTypes', [
            'foreignKey' => 'cms_menu_type_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CmsDocs', [
            'foreignKey' => 'cms_menu_id',
        ]);
        $this->hasMany('CmsPages', [
            'foreignKey' => 'cms_menu_id',
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 100)
            ->allowEmptyString('slug');

        $validator
            ->scalar('options')
            ->allowEmptyString('options');

        $validator
            ->integer('sort')
            ->allowEmptyString('sort');

        $validator
            ->boolean('is_home')
            ->notEmptyString('is_home');

        $validator
            ->boolean('is_public')
            ->notEmptyString('is_public');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->existsIn(['cms_menu_type_id'], 'CmsMenuTypes'));

        return $rules;
    }

    /*
     * estrae le voci di menu' per le pagine non associate a pagine
     */
    public function getMenuToAssociateList($organization_id) {

        $resultList = [];
        $results = $this->find('all', [
            'conditions' => ['organization_id' => $organization_id, 'cms_menu_type_id' => 1],
            'contain' => ['CmsPages']]);

        if(!empty($results)) {
            /*
             * verifico se la voce di menu' è associata ad una pagina
             */
            $results = $results->toArray();
            foreach ($results as $numResult => $result) {
                if(empty($result->cms_pages)) {
                    $resultList[$result->id] = $result->name;
                }
            }
        }
        return $resultList;
    }

    public function getLastSort($organization_id) {

        $resultList = [];
        $results = $this->find()
            ->where(['organization_id' => $organization_id, 'is_active' => 1])
            ->all();

        if(!empty($results)) {
            /*
             * verifico se la voce di menu' è associata ad una pagina
             */
            $results = $results->toArray();
            foreach ($results as $numResult => $result) {
                if(empty($result->cms_pages)) {
                    $resultList[$result->id] = $result->name;
                }
            }
        }
        return $resultList;
    }
}
