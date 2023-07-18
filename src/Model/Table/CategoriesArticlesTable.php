<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CategoriesArticles Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CategoriesArticlesTable&\Cake\ORM\Association\BelongsTo $ParentCategoriesArticles
 * @property \App\Model\Table\CategoriesArticlesTable&\Cake\ORM\Association\HasMany $ChildCategoriesArticles
 *
 * @method \App\Model\Entity\CategoriesArticle get($primaryKey, $options = [])
 * @method \App\Model\Entity\CategoriesArticle newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CategoriesArticle[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CategoriesArticle|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CategoriesArticle saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CategoriesArticle patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CategoriesArticle[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CategoriesArticle findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class CategoriesArticlesTable extends Table
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

        $this->setTable('k_categories_articles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ParentCategoriesArticles', [
            'className' => 'CategoriesArticles',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildCategoriesArticles', [
            'className' => 'CategoriesArticles',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('Articles', [
            'className' => 'Articles',
            'foreignKey' => 'category_article_id'
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
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategoriesArticles'));

        return $rules;
    }
}
