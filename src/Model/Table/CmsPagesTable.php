<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsPages Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsMenusTable&\Cake\ORM\Association\BelongsTo $CmsMenus
 * @property &\Cake\ORM\Association\HasMany $CmsPagesDocs
 * @property &\Cake\ORM\Association\HasMany $CmsPagesImages
 *
 * @method \App\Model\Entity\CmsPage get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsPage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsPage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsPage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsPagesTable extends Table
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

        $this->setTable('cms_pages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CmsMenus', [
            'foreignKey' => 'cms_menu_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('CmsPagesDocs', [
            'foreignKey' => 'cms_page_id',
            'sort' => 'sort'
        ]);
        $this->hasMany('CmsPagesImages', [
            'foreignKey' => 'cms_page_id',
            'sort' => 'sort'
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
            ->maxLength('name', 75)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('body')
            ->allowEmptyString('body');

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

        return $rules;
    }
}
