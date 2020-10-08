<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JUserProfiles Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\JUserProfile get($primaryKey, $options = [])
 * @method \App\Model\Entity\JUserProfile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JUserProfile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JUserProfile|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUserProfile saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUserProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JUserProfile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JUserProfile findOrCreate($search, callable $callback = null, $options = [])
 */
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
}
