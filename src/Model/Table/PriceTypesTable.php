<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PriceTypes Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\PriceType get($primaryKey, $options = [])
 * @method \App\Model\Entity\PriceType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PriceType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PriceType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PriceType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PriceType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PriceType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PriceType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PriceTypesTable extends Table
{
    const TYPE_PERC = 'Percentuale';
    const TYPE_FIX = 'Fisso';
    const TYPE_FIX_USER = 'Fisso per gasista';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('price_types');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('IsSystem');
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'type' => [
                'strategy' => 'const',
                'prefix' => 'TYPE'
            ],
        ]]);

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('code')
            ->maxLength('code', 25)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->scalar('type')
            ->notEmptyString('type');

        $validator
            ->numeric('value')
            ->notEmptyString('value');

        $validator
            ->boolean('is_system')
            ->allowEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

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
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));

        return $rules;
    }
}
