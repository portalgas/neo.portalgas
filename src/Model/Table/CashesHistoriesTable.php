<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CashesHistories Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\CashesTable&\Cake\ORM\Association\BelongsTo $Cashes
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\CashesHistory get($primaryKey, $options = [])
 * @method \App\Model\Entity\CashesHistory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CashesHistory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CashesHistory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CashesHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CashesHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CashesHistory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CashesHistory findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CashesHistoriesTable extends Table
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

        $this->setTable('k_cashes_histories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Cashes', [
            'foreignKey' => 'cash_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

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
        $rules->add($rules->existsIn(['cash_id'], 'Cashes'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function getByUser($user, $organization_id, $user_id, $options=[], $debug=false) {

        $results = [];

        $where = ['CashesHistories.organization_id' => $organization_id,
                  'CashesHistories.user_id' => $user_id];
        if($debug) debug($where);

        $results = $this->find()
                        ->where($where)
                        ->order(['CashesHistories.id' => 'asc']) // per created no perche' e' sempre =
                        ->all();

        if($debug) debug($results);
        
        return $results;
    }    
}
