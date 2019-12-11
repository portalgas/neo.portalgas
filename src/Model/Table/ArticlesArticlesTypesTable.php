<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KArticlesArticlesTypes Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 * @property \App\Model\Table\ArticleTypesTable&\Cake\ORM\Association\BelongsTo $ArticleTypes
 *
 * @method \App\Model\Entity\KArticlesArticlesType get($primaryKey, $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesArticlesType findOrCreate($search, callable $callback = null, $options = [])
 */
class ArticlesArticlesTypesTable extends Table
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

        $this->setTable('k_articles_articles_types');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => 'article_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticleTypes', [
            'foreignKey' => 'article_type_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['article_id'], 'Articles'));
        $rules->add($rules->existsIn(['article_type_id'], 'ArticleTypes'));

        return $rules;
    }
}
