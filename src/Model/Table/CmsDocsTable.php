<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsDocs Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property &\Cake\ORM\Association\HasMany $CmsMenusDocs
 * @property &\Cake\ORM\Association\HasMany $CmsPagesDocs
 *
 * @method \App\Model\Entity\CmsDoc get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsDoc newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsDoc[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsDoc|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsDoc saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsDoc patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsDoc[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsDoc findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsDocsTable extends Table
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

        $this->setTable('cms_docs');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CmsMenusDocs', [
            'foreignKey' => 'cms_doc_id',
        ]);
        $this->hasMany('CmsPagesDocs', [
            'foreignKey' => 'cms_doc_id',
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
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('path')
            ->maxLength('path', 256)
            ->allowEmptyString('path');

        $validator
            ->scalar('ext')
            ->maxLength('ext', 75)
            ->requirePresence('ext', 'create')
            ->notEmptyString('ext');

        $validator
            ->numeric('size')
            ->allowEmptyString('size');

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
