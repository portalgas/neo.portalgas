<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsPageImages Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsPagesTable&\Cake\ORM\Association\BelongsTo $CmsPages
 *
 * @method \App\Model\Entity\CmsPageImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsPageImage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsPageImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsPageImage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPageImage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPageImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPageImage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPageImage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsPageImagesTable extends Table
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

        $this->setTable('cms_page_images');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CmsPages', [
            'foreignKey' => 'cms_page_id',
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 75)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('path')
            ->allowEmptyString('path');

        $validator
            ->scalar('ext')
            ->maxLength('ext', 75)
            ->requirePresence('ext', 'create')
            ->notEmptyString('ext');

        $validator
            ->integer('sort')
            ->allowEmptyString('sort');

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
        $rules->add($rules->existsIn(['cms_page_id'], 'CmsPages'));

        return $rules;
    }
}
