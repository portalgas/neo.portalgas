<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
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

        $this->setTable('j_users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('UserProfiles', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT'
        ]);
        $this->hasMany('UserUsergroupMap', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT'
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmptyString('name');

        $validator
            ->scalar('username')
            ->maxLength('username', 150)
            ->notEmptyString('username');

        $validator
            ->email('email')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 100)
            ->notEmptyString('password');

        $validator
            ->scalar('usertype')
            ->maxLength('usertype', 25)
            ->notEmptyString('usertype');

        $validator
            ->notEmptyString('block');

        $validator
            ->allowEmptyString('sendEmail');

        $validator
            ->dateTime('registerDate')
            ->notEmptyDateTime('registerDate');

        $validator
            ->dateTime('lastvisitDate')
            ->notEmptyDateTime('lastvisitDate');

        $validator
            ->scalar('activation')
            ->maxLength('activation', 100)
            ->notEmptyString('activation');

        $validator
            ->scalar('params')
            ->requirePresence('params', 'create')
            ->notEmptyString('params');

        $validator
            ->dateTime('lastResetTime')
            ->notEmptyDateTime('lastResetTime');

        $validator
            ->integer('resetCount')
            ->notEmptyString('resetCount');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));

        return $rules;
    }
	
    public function findByUsername($organization_id, $username)
    {
        if (empty($organization_id) || empty($username)) {
            return null;
        }

		$this->Organizations->removeBehavior('OrganizationsParams');
		
		$where = ['organization_id' => $organization_id,
				  'username' => $username,
				  'block' => 0,
				  'Organizations.stato' => 'Y'];
		// debug($where);
		
        $user = $this->find()
            ->select([
                'Users.id',
                'Users.organization_id',
                'Users.username',
                'Users.email',
				'Users.registerDate',
				'Users.lastvisitDate',
				'Organizations.name',
				'Organizations.mail',
				'Organizations.www',
				'Organizations.www2',
				'Organizations.lat',
				'Organizations.lng',
				'Organizations.img1',
				'Organizations.template_id',
				'Organizations.type',
				'Organizations.paramsConfig',
				'Organizations.paramsFields',
				'Organizations.hasMsg',
				'Organizations.msgText'
            ])
            ->where($where)
			->contain(['Organizations', 'UserProfiles', 'UserUsergroupMap' => ['Usergroups']])
            ->first();

        if (!$user) {
            return null;
        }

		if(!empty($user->organization->paramsConfig))
			$user->organization->paramsConfig = json_decode($user->organization->paramsConfig, true);
		if(!empty($user->organization->paramsFields))
			$user->organization->paramsFields = json_decode($user->organization->paramsFields, true);
		if(!empty($user->organization->paramsPay))
			$user->organization->paramsPay = json_decode($user->organization->paramsPay, true);
		
		
        $user->unsetProperty('password');

        return $user;
    }	    
}