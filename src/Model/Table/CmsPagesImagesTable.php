<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsPagesImages Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsPagesTable&\Cake\ORM\Association\BelongsTo $CmsPages
 * @property \App\Model\Table\CmsImagesTable&\Cake\ORM\Association\BelongsTo $CmsImages
 *
 * @method \App\Model\Entity\CmsPagesImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsPagesImage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsPagesImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesImage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPagesImage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPagesImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesImage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesImage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsPagesImagesTable extends Table
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

        $this->setTable('cms_pages_images');
        $this->setDisplayField('id');
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
        $this->belongsTo('CmsImages', [
            'foreignKey' => 'cms_image_id',
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
        $rules->add($rules->existsIn(['cms_page_id'], 'CmsPages'));
        $rules->add($rules->existsIn(['cms_image_id'], 'CmsImages'));

        return $rules;
    }
}
