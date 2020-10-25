<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrderStateCodes Model
 *
 * @method \App\Model\Entity\OrderStateCode get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrderStateCode newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\OrderStateCode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrderStateCode|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderStateCode saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderStateCode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrderStateCode[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrderStateCode findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrderStateCodesTable extends Table
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

        $this->setTable('order_state_codes');
        $this->setDisplayField('code');
        $this->setPrimaryKey('code');

        $this->addBehavior('Timestamp');
        $this->addBehavior('IsDefaultIni');
        $this->addBehavior('IsDefaultEnd');
        $this->addBehavior('IsSystem');

        $this->hasMany('Orders', [
            'foreignKey' => 'state_code',
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
            ->scalar('code')
            ->maxLength('code', 45)
            ->requirePresence('code', 'create')
            ->notEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->scalar('css_color')
            ->maxLength('css_color', 15)
            ->allowEmptyString('css_color');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->boolean('is_default_ini')
            ->notEmptyString('is_default_ini');

        $validator
            ->boolean('is_default_end')
            ->notEmptyString('is_default_end');

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
        $rules->add($rules->isUnique(['code']));

        return $rules;
    }
}
