<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KArticlesTypes Model
 *
 * @method \App\Model\Entity\KArticlesType get($primaryKey, $options = [])
 * @method \App\Model\Entity\KArticlesType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KArticlesType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KArticlesType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KArticlesType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KArticlesType findOrCreate($search, callable $callback = null, $options = [])
 */
class ArticlesTypesTable extends Table
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

        $this->setTable('k_articles_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('code')
            ->maxLength('code', 50)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('label')
            ->maxLength('label', 75)
            ->requirePresence('label', 'create')
            ->notEmptyString('label');

        $validator
            ->scalar('descrizione')
            ->maxLength('descrizione', 256)
            ->allowEmptyString('descrizione');

        $validator
            ->integer('sort')
            ->requirePresence('sort', 'create')
            ->notEmptyString('sort');

        return $validator;
    }

    public function getsList($user=null, $organization_id) {
        $results = $this->find()->order(['sort'])->all(); // ->toArray();
        return $results;
    }
}
