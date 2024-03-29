<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UserProfilesTable extends Table
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

        $this->setTable('j_user_profiles');
        $this->addBehavior('UserProfiles');

        $this->belongsTo('Users', [
            'foreignKey' => 'id',
            'joinType' => 'INNER'
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
            ->scalar('profile_key')
            ->maxLength('profile_key', 100)
            ->requirePresence('profile_key', 'create')
            ->notEmptyFile('profile_key');

        $validator
            ->scalar('profile_value')
            ->requirePresence('profile_value', 'create')
            ->notEmptyFile('profile_value');

        $validator
            ->integer('ordering')
            ->notEmptyString('ordering');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function getValuesByUserId($user_id) {

        $results = [];

        $where = ['user_id' => $user_id];
        $user_profiles = $this->find()
                                ->where($where)
                                ->all();
        foreach($user_profiles as $user_profile) {
            $results[$user_profile['profile_key']] = $user_profile['profile_value'];
        }

        return $results;
    }
}
