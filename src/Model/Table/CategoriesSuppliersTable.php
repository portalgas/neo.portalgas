<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * CategoriesSuppliers Model
 *
 * @property \App\Model\Table\CategoriesSuppliersTable&\Cake\ORM\Association\BelongsTo $ParentCategoriesSuppliers
 * @property \App\Model\Table\JCategoriesTable&\Cake\ORM\Association\BelongsTo $JCategories
 * @property \App\Model\Table\CategoriesSuppliersTable&\Cake\ORM\Association\HasMany $ChildCategoriesSuppliers
 *
 * @method \App\Model\Entity\KCategoriesSupplier get($primaryKey, $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KCategoriesSupplier findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class CategoriesSuppliersTable extends Table
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

        $this->setTable('k_categories_suppliers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree');

        $this->belongsTo('ParentCategoriesSuppliers', [
            'className' => 'CategoriesSuppliers',
            'foreignKey' => 'parent_id',
        ]);
        $this->belongsTo('JCategories', [
            'foreignKey' => 'j_category_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ChildCategoriesSuppliers', [
            'className' => 'CategoriesSuppliers',
            'foreignKey' => 'parent_id',
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
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategoriesSuppliers'));
        $rules->add($rules->existsIn(['j_category_id'], 'JCategories'));

        return $rules;
    }
}
