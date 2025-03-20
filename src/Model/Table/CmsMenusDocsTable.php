<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsMenusDocs Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsMenusTable&\Cake\ORM\Association\BelongsTo $CmsMenus
 * @property \App\Model\Table\CmsDocsTable&\Cake\ORM\Association\BelongsTo $CmsDocs
 *
 * @method \App\Model\Entity\CmsMenusDoc get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsMenusDoc newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsMenusDoc[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenusDoc|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenusDoc saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenusDoc patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenusDoc[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenusDoc findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsMenusDocsTable extends Table
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

        $this->setTable('cms_menus_docs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CmsMenus', [
            'foreignKey' => 'cms_menu_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CmsDocs', [
            'foreignKey' => 'cms_doc_id',
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
            ->integer('sort')
            ->notEmptyString('sort');

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
        $rules->add($rules->existsIn(['cms_menu_id'], 'CmsMenus'));
        $rules->add($rules->existsIn(['cms_doc_id'], 'CmsDocs'));

        return $rules;
    }
}
