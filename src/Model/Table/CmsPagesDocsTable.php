<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsPagesDocs Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CmsPagesTable&\Cake\ORM\Association\BelongsTo $CmsPages
 * @property \App\Model\Table\CmsDocsTable&\Cake\ORM\Association\BelongsTo $CmsDocs
 *
 * @method \App\Model\Entity\CmsPagesDoc get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsPagesDoc newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsPagesDoc[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesDoc|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPagesDoc saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsPagesDoc patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesDoc[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsPagesDoc findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsPagesDocsTable extends Table
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

        $this->setTable('cms_pages_docs');
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
        $rules->add($rules->existsIn(['cms_page_id'], 'CmsPages'));
        $rules->add($rules->existsIn(['cms_doc_id'], 'CmsDocs'));

        return $rules;
    }

    public function setDocs($organization_id, $cms_page_id, $doc_ids=[]): bool {

        if(empty($doc_ids)) return true;

        $doc_ids = explode(',', $doc_ids);

        $this->deleteAll(['organization_id' => $organization_id, 'cms_page_id' => $cms_page_id]);
        foreach($doc_ids as $numResult => $doc_id) {
            $cmsPageDocs = $this->newEntity();
            $datas = [];
            $datas['organization_id'] = $organization_id;
            $datas['cms_page_id'] = $cms_page_id;
            $datas['cms_doc_id'] = $doc_id;
            $datas['sort'] = $numResult;

            $cmsPageDocs = $this->patchEntity($cmsPageDocs, $datas);
            if (!$this->save($cmsPageDocs)) {
                dd($cmsPageDocs->getErrors());
            }
        }

        return true;
    }
}
