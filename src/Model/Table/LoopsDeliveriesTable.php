<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoopsDeliveries Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LoopsDelivery get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoopsDelivery newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoopsDelivery[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoopsDelivery|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoopsDelivery saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoopsDelivery patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoopsDelivery[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoopsDelivery findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoopsDeliveriesTable extends Table
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

        $this->setTable('k_loops_deliveries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('luogo')
            ->maxLength('luogo', 156)
            ->requirePresence('luogo', 'create')
            ->notEmptyString('luogo');

        $validator
            ->time('orario_da')
            ->requirePresence('orario_da', 'create')
            ->notEmptyTime('orario_da');

        $validator
            ->time('orario_a')
            ->requirePresence('orario_a', 'create')
            ->notEmptyTime('orario_a');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('nota_evidenza')
            ->requirePresence('nota_evidenza', 'create')
            ->notEmptyString('nota_evidenza');

        $validator
            ->date('data_master')
            ->requirePresence('data_master', 'create')
            ->notEmptyDate('data_master');

        $validator
            ->date('data_master_reale')
            ->requirePresence('data_master_reale', 'create')
            ->notEmptyDate('data_master_reale');

        $validator
            ->date('data_copy')
            ->requirePresence('data_copy', 'create')
            ->notEmptyDate('data_copy');

        $validator
            ->date('data_copy_reale')
            ->requirePresence('data_copy_reale', 'create')
            ->notEmptyDate('data_copy_reale');

        $validator
            ->scalar('flag_send_mail')
            ->allowEmptyString('flag_send_mail');

        $validator
            ->scalar('rules')
            ->allowEmptyString('rules');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function gets($user, $organization_id, $where=[]) {
        
        $conditions = ['LoopsDeliveries.organization_id' => $organization_id];
    
        if(isset($where))
            $where += $conditions;
        else 
            $where += $conditions;
        
        $results = $this->find()
            ->where($where)
            ->order(['data_master'])
            ->all();

        return $results;
    }

    public function getsList($user, $organization_id, $where=[]) {
        
        $conditions = ['LoopsDeliveries.organization_id' => $organization_id];
    
        if(isset($where))
            $where += $conditions;
        else 
            $where += $conditions;

        $results = $this->find('list', [
                                'keyField' => 'id',
                                'valueField' => 'luogo'])
            ->where($where)
            ->order(['data_master'])
            ->all();

        return $results;
    }    
}
