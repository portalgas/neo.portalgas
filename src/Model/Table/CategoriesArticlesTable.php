<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
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


	public function getIsSystemId($user, $organization_id) {

		$results = $this->getIsSystem($user, $organization_id);
		if(empty($results)) {
			return 0;
		}
		return $results->id;
	}

	/*
	 * setto con la categoria di default (Generali) gli articoli che non hanno categoria
	 */
	public function setCategoryDefaultToArticles($user=null, $organization_id, $debug=false) {

		$category = $this->getIsSystem($user, $organization_id);

		/*
		 * estraggo articoli senza categoria impostata
		 */
        $articlesTable = TableRegistry::get('Articles');

		$update_fields = ['category_article_id' => $category->id];
		$where = ['organization_id' => $organization_id,
				   'category_article_id' => 0];
		$results = $articlesTable->updateAll($update_fields, $where);
		if($debug) debug($update_fields);
		if($debug) debug($where);

		return $results;
	}

	/*
     * $user=null se chiamato dal cron CategoriesArticleIsSystemCommand
	 * se $truncate=true cancella tutte le cateogirie dell'org che non sono is_system
	 * 	utile la prima volta per creare quella 'Generale' e cancella le vecchie categorie
	 *
	 *  ora gestione con truncate in neo
	 */
	public function getIsSystem($user=null, $organization_id, $truncate=false, $debug=false) {

		$where = ['organization_id' => $organization_id,
                  'is_system'=> true];
		$options['recursive'] = -1;
		$results = $this->find()->where($where)->first();
		if($debug) debug($where);
		if($debug) debug($results);
		if(empty($results)) {

			if($truncate) {
				if($debug) echo('deleteAll organization_id '.$organization_id);
				$this->deleteAll(['organization_id' => $organization_id], false);
			}

			if($this->createIsSystem($user, $organization_id)) {
				if($debug) echo('createIsSystem organization_id '.$organization_id);
				$results = $this->find()->where($where)->first();
			}

			if($truncate) {
				if($debug) echo('setCategoryDefaultToArticles organization_id '.$organization_id);
				$this->setCategoryDefaultToArticles($user, $organization_id);
			}
		}

		return $results;
	}

	public function createIsSystem($user=null, $organization_id) {

		$datas = [];
		$datas['organization_id'] = $organization_id;
		$datas['name'] = 'Generale';
		$datas['is_system'] = true;
		$datas['parent_id'] = null;
		$datas['lft'] = 1;
		$datas['rght'] = 2;
		$category = $this->newEntity();
        $category = $this->patchEntity($category, $datas);
        if (!$this->save($category)) {
            debug($category->getErrors());
			return false;
		}

		return true;
	}

    /*
     * aggiungo un id contatore perche' se no cambia l'ordinamento
     */
    public function jsListGets($user=null, $organization_id) {

        $categories_articles = $this->find('treeList', [
                                            'spacer' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'conditions' => ['Organization_id' => $organization_id],
                                            'order' => ['name']
                                            ]);

        $results = [];
        $i = 0;
        foreach($categories_articles->toArray() as $id => $name) {
            $results[$i] = ['id' => $id, 'name' => $name];
            $i++;
        }
        // $results = json_encode($results);

        return $results;
    }

    public function getsList($user=null, $organization_id) {

        $results = $this->find('treeList', [
            'spacer' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'conditions' => ['Organization_id' => $organization_id],
            'order' => ['name']
        ]);

        return $results;
    }
}
