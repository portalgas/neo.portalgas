<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use App\Traits;

class UsersTable extends Table
{
    use Traits\SqlTrait;

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
     * loadIdentifier(resolver)
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
     * user_organization_id = organization_id dell'utente (per root==0)
     * user_id = id dell'utente 
     * organization_id = organization_id scelta (per root)
     */
    public function findLogin($user_organization_id, $user_id, $organization_id, $debug=false)
    {
        if($debug) debug('user_organization_id (organization_id dell\'utente) '.$user_organization_id);
        if($debug) debug('user_id (id dell\'utente) '.$user_id);
        if($debug) debug('organization_id (organization_id scelta (per root)) '.$organization_id);
        // $user_organization_id / organization_id puo' essere 0 (per root)
        if (empty($user_id)) {
            return null;
        }

        $where = ['organization_id' => $user_organization_id,
                  'block' => 0,
                  'id' => $user_id];
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
         if($debug) debug($user);

        if (!$user) {
            return null;
        }

        $user->unsetProperty('password');

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
        
        // sotto gruppi 
        $user->acl['isGasGropusManagerGroups'] = $usergroupsTable->isGasGropusManagerGroups($user);
        $user->acl['isGasGropusManagerDelivery'] = $usergroupsTable->isGasGropusManagerDelivery($user);
        $user->acl['isGasGropusManagerOrders'] = $usergroupsTable->isGasGropusManagerOrders($user);
 
        // produttore
        $user->acl['isProdGasSupplierManager'] = $usergroupsTable->isProdGasSupplierManager($user);

        $organization = $this->_getOrganization($user, $user_organization_id, $organization_id, $debug); 

        if(isset($organization->type))
        switch($organization->type) {
            case 'GAS':
                $user = $this->_getOrganizationByGas($user, $user_organization_id, $organization_id, $debug);
                $user = $this->_setCash($user, $user->organization);
                break;
            case 'SOCIALMARKET':
                $user = $this->_getOrganizationByGas($user, $user_organization_id, $organization_id, $debug);
                break;
            case 'PRODGAS':
                $user = $this->_getOrganizationByProdGasSupplier($user, $user_organization_id, $organization_id, $debug);
            break;     
        }

        // debug($user);
       
        return $user;
    }

    private function _getOrganization($user, $user_organization_id, $organization_id, $debug=false) {

        /*
         * sono root user_organization_id = 0
         */
        if(empty($user_organization_id))
            $user_organization_id = $organization_id;

        /*
         * dati organization
         * organizzazione al quale appartiene lo user
         * root: organizzazione scelta
         */ 
        $where = ['Organizations.id' => $user_organization_id,
                  'Organizations.stato' => 'Y'];
        if($debug) debug($where);
        
        $this->Organizations->addBehavior('OrganizationsParams');
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
                'Organizations.j_seo'               
            ])
            ->where($where)
            ->first();
        // if($debug) debug($organization);            

        return $organization;
    }

    private function _getOrganizationByGas($user, $user_organization_id, $organization_id, $debug=false) {

        /*
         * sono root user_organization_id = 0
         */
        if(empty($user_organization_id))
            $user_organization_id = $organization_id;

        /*
         * dati organization
         * organizzazione al quale appartiene lo user
         * root: organizzazione scelta
         */ 
        $where = ['Organizations.id' => $user_organization_id,
                  'Organizations.stato' => 'Y'];
        if($debug) debug($where);
        
        $this->Organizations->addBehavior('OrganizationsParams');
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
                'Organizations.j_seo',
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

        return $user;
    }

    private function _getOrganizationByProdGasSupplier($user, $user_organization_id, $organization_id, $debug=false) {
 
        /*
         * sono root user_organization_id = 0
         */
        if(empty($user_organization_id))
            $user_organization_id = $organization_id;

        /*
         * dati organization
         * organizzazione al quale appartiene lo user
         * root: organizzazione scelta
         */ 
        $where = ['Organizations.id' => $user_organization_id,
                  'Organizations.stato' => 'Y'];
        if($debug) debug($where);
        
        $this->Organizations->addBehavior('OrganizationsParams');
        $organization = $this->Organizations->find()
            ->where($where)
            ->contain(['SuppliersOrganizations' => ['Suppliers']])
            ->first();
        if($debug) debug($organization);            

        $user->organization = $organization;
        if($debug) debug($user);  

        return $user;
    }

    private function _setCash($user, $organization) {
        
        /*
         * aggiungo i dati per il prepagati, x BO e FE
         */
        if(isset($organization->paramsConfig['cashLimit'])) {
            $user->organization_cash_limit = $organization->paramsConfig['cashLimit'];
            $user->organization_cash_limit_label = __('FE-'.$organization->paramsConfig['cashLimit']);
            $user->organization_limit_cash_after = $this->convertImport($organization->paramsConfig['limitCashAfter']);
            $user->organization_limit_cash_after_ = $organization->paramsConfig['limitCashAfter'];
            $user->organization_limit_cash_after_e = $organization->paramsConfig['limitCashAfter'].'&nbsp;&euro;';            
        }

        /*
         * totale cassa
         */
        $cashesTable = TableRegistry::get('Cashes');
        
        $user_cash = $cashesTable->getTotaleCashToUser($user, $user->id);
        $user->user_cash = $user_cash;
        $user->user_cash_ = number_format($user_cash ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
        $user->user_cash_e = number_format($user_cash ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).'&nbsp;&euro;';
                    
        /*
         * gestione prepagato
         */
         if(isset($user->organization->paramsConfig) && $user->organization->paramsConfig['cashLimit']=='LIMIT-CASH-USER') {

                $cashesUsersTable = TableRegistry::get('CashesUsers');
                
                $where = ['CashesUsers.organization_id' => $user->organization->id,
                          'CashesUsers.user_id' => $user->id];
                $cashesUsersResults = $cashesUsersTable->find()
                                                    ->where($where)
                                                    ->first();
                                                   
                if(!empty($cashesUsersResults)) {
                    $user->user_limit_type = $cashesUsersResults->limit_type;
                    $user->user_limit_after = $cashesUsersResults->limit_after;
                    $user->user_limit_after_ =  number_format($cashesUsersResults->limit_after ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                    $user->user_limit_after_e = number_format($cashesUsersResults->limit_after ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).'&nbsp;&euro;';
                }                   
                         
        } // end if($user->organization->paramsConfig['cashLimit']=='LIMIT-CASH-USER')

        return $user;
    }        
}