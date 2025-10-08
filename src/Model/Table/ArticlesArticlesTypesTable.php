<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;

class ArticlesArticlesTypesTable extends Table
{
    const BIO = 1;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setPrimaryKey(['organization_id', 'article_id', 'article_type_id']);

        $this->setTable('k_articles_articles_types');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => 'article_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticlesTypes', [
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
        $rules->add($rules->existsIn(['article_type_id'], 'ArticlesTypes'));

        return $rules;
    }

    public function insert($organization_id, $article_id, $article_type_id) {

        try {
            $sql =  "INSERT INTO k_articles_articles_types
                            (organization_id, article_id, article_type_id)
                            VALUES ($organization_id, $article_id, $article_type_id)";
            $connection = ConnectionManager::get('default');
            $results = $connection->execute($sql);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return true;
   }

    public function store($user=null, $article_organization_id, $article_id, $articles_types_ids) {

        $article_is_bio = 'N';
        $articles_type_ids = [];

        if(!empty($articles_types_ids))
            foreach($articles_types_ids as $articles_type_id) {
                $where = ['organization_id' => $article_organization_id, 'article_id' => $article_id, 'article_type_id' => $articles_type_id];
                $articles_type = $this->find()->where($where)->first();
                if(empty($articles_type)) {
                    $articles_type = $this->newEntity();

                    $datas = [];
                    $datas['organization_id'] = $article_organization_id;
                    $datas['article_id'] = $article_id;
                    $datas['article_type_id'] = $articles_type_id;
                    // Log::debug($datas);
                    $articles_type = $this->patchEntity($articles_type, $datas);
                    $this->save($articles_type);
                } // if(empty($articles_type))
                $articles_type_ids[] = $articles_type_id;

                if($articles_type_id==self::BIO) $article_is_bio = 'Y';
            } // foreach($articles_types_ids as $articles_type_id)

        /*
         * eliminiamo i tipi di articolo non piu' associati
         */
        if(empty($articles_type_ids))
            $where = ['organization_id' => $article_organization_id, 'article_id' => $article_id];
        else
            $where = ['organization_id' => $article_organization_id, 'article_id' => $article_id, 'article_type_id NOT IN ' => $articles_type_ids];
        $this->deleteAll($where);

        /*
         * se ho la tipologia bio (article_type_id=self::BIO) aggiungo article_is_bio
         */
        $articlesTable = TableRegistry::get('Articles');
        $article = $articlesTable->find()
            ->where(['organization_id' => $article_organization_id, 'id' => $article_id])
            ->first();
        $datas = [];
        $datas['bio'] = $article_is_bio;
        $article = $articlesTable->patchEntity($article, $datas);
        if (!$articlesTable->save($article)) {
            Log::write('error', $articlesTable->getErrors());
        }

        return true;
    }
}
