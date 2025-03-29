<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CmsMenuTypes Model
 *
 * @property \App\Model\Table\CmsMenusTable&\Cake\ORM\Association\HasMany $CmsMenus
 *
 * @method \App\Model\Entity\CmsMenuType get($primaryKey, $options = [])
 * @method \App\Model\Entity\CmsMenuType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CmsMenuType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenuType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenuType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CmsMenuType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenuType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CmsMenuType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CmsMenuTypesTable extends Table
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

        $this->setTable('cms_menu_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CmsMenus', [
            'foreignKey' => 'cms_menu_type_id',
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
            ->scalar('code')
            ->maxLength('code', 15)
            ->allowEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 75)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

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
        $rules->add($rules->isUnique(['code']));

        return $rules;
    }
}
