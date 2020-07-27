<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
	
    /*
     * loadIdentifier(resolver
     */    
    public function findLoginActive(Query $query, array $options)
    {
        $query->where(['Users.block' => 0]);
        return $query;
    }         

    /*
     * utilizzato dopo login 
     * Joomla25Authenticate
     * UserController::login
     *
     * user_organization_id = organization_id dell'utente
     * user_id = id dell'utente 
     * organization_id = organization_id scelta (per root)
     */
    public function findLogin($user_organization_id, $user_id, $organization_id, $debug=false)
    {
        debug('user_organization_id '.$user_organization_id);
        debug('user_id '.$user_id);
        debug('organization_id '.$organization_id);
        // $user_organization_id / organization_id puo' essere 0 (per root)
        if (empty($user_id)) {
            return null;
        }
        
        $where = ['organization_id' => $user_organization_id,
                  'id' => $user_id,
                  'block' => 0];
        if($debug) debug($where);
        
        $user = $this->find()
            ->select([
                'Users.id',
                'Users.organization_id',
                'Users.username',
                'Users.email',
                'Users.registerDate',
                'Users.lastvisitDate',
            ])
            ->where($where)
            ->contain(['UserProfiles', 'UserUsergroupMap' => ['UserGroups']])
            ->first();
        // if($debug) debug($user);
        
        if (!$user) {
            return null;
        }

        $user->unsetProperty('password');

        /*
         * dati organization
         * organizzazione al quale appartiene lo user
         * root: organizzazione scelta
         */ 
        $where = ['Organizations.id' => $organization_id,
                  'Organizations.stato' => 'Y'];
        if($debug) debug($where);
        
        $organization = $this->Organizations->find()
            ->select([
                'Organizations.id',  
                'Organizations.name',
                'Organizations.cf',
                'Organizations.piva',
                'Organizations.mail',
                'Organizations.www',
                'Organizations.www2',
                'Organizations.telefono',
                'Organizations.indirizzo',
                'Organizations.localita',
                'Organizations.cap',
                'Organizations.provincia',
                'Organizations.lat',
                'Organizations.lng',
                'Organizations.img1',
                'Organizations.template_id',
                'Organizations.type',
                'Organizations.paramsConfig',
                'Organizations.paramsFields',
                'Organizations.hasMsg',
                'Organizations.msgText',
                'Templates.id',
                'Templates.name',
                'Templates.descri',
                'Templates.descri_order_cycle_life',
                'Templates.payToDelivery',
                'Templates.orderForceClose',
                'Templates.orderUserPaid',
                'Templates.orderSupplierPaid',
                'Templates.ggArchiveStatics',
                'Templates.hasCassiere',
                'Templates.hasTesoriere'                
            ])
            ->where($where)
            ->contain(['Templates'])
            ->first();
        // if($debug) debug($organization);            

        $user->organization = $organization;
         if($debug) debug($user);  

        /*
         * creo array con i group_id dell'utente, per UserComponent
         */
        $group_ids = [];
        if($user->has('user_usergroup_map')) {
            foreach($user->user_usergroup_map as $user_usergroup_map) {
                $group_ids[$user_usergroup_map->group_id] = $user_usergroup_map->user_group->title;
            }
            unset($user->user_usergroup_map);
        }
        // debug($group_ids);
        $user->group_ids = $group_ids;
        
        /*
         * rimappo array user.profiles
         * user->user_profiles['profile.address'] = value
         */
        $user_profiles = [];
        if($user->has('user_profiles')) {
            foreach($user->user_profiles as $user_profile) {
                $profile_key = str_replace('profile.', '', $user_profile->profile_key);
                /*
                 * elimino primo e ultimo carattere se sono "
                 */
                if(!empty($user_profile->profile_value) && strpos(substr($user_profile->profile_value, 0, 1), '"')!==false) {
                    $user_profile->profile_value = substr($user_profile->profile_value, 1, strlen($user_profile->profile_value)-2);
                }

                $user_profiles[$profile_key] = $user_profile->profile_value;
            }
            
            unset($user->user_profiles);
        }
        // debug($user_profiles);
        $user->user_profiles = $user_profiles;           
        
        /*
         *
         * acl
         */
        $usergroupsTable = TableRegistry::get('UserGroups');        
        
        $user->acl = [];
        $user->acl['isRoot'] = $usergroupsTable->isRoot($user);
        $user->acl['isManager'] = $usergroupsTable->isManager($user);
        $user->acl['isCassiere'] = $usergroupsTable->isCassiere($user);
        $user->acl['isSuperReferente'] = $usergroupsTable->isSuperReferente($user);
        $user->acl['isReferentGeneric'] = $usergroupsTable->isReferentGeneric($user);

        return $user;
    }    
}