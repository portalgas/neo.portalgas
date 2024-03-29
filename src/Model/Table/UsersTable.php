<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Traits;

class UsersTable extends Table
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

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
        
        $this->addBehavior('Users');
        $user = $this->find()
            ->select([
                'Users.id',
                'Users.organization_id',
                'Users.username',
                'Users.email',
                'Users.registerDate',
                'Users.lastvisitDate',
            ])
            ->contain(['UserProfiles', 'UserUsergroupMap' => ['UserGroups']])
            ->where($where)
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
        $user->acl['isManagerDelivery'] = $usergroupsTable->isManagerDelivery($user);
        $user->acl['isTesoriere'] = $usergroupsTable->isTesoriere($user);
        $user->acl['isStoreroom'] = $usergroupsTable->isStoreroom($user);
        $user->acl['isManagerEvents'] = $usergroupsTable->isManagerEvents($user);
        $user->acl['isUserFlagPrivay'] = $usergroupsTable->isUserFlagPrivay($user);
        
        // gruppi 
        $user->acl['isGasGroupsManagerGroups'] = $usergroupsTable->isGasGroupsManagerGroups($user);
        $user->acl['isGasGroupsManagerDeliveries'] = $usergroupsTable->isGasGroupsManagerDeliveries($user);
        $user->acl['isGasGroupsManagerParentOrders'] = $usergroupsTable->isGasGroupsManagerParentOrders($user);
        $user->acl['isGasGroupsManagerOrders'] = $usergroupsTable->isGasGroupsManagerOrders($user);

        // produttore
        $user->acl['isProdGasSupplierManager'] = $usergroupsTable->isProdGasSupplierManager($user);

        $organization = $this->_getOrganization($user, $user_organization_id, $organization_id, $debug); 
      
        $user->des_id = 0;
        if(isset($organization->paramsConfig['hasDes']) && $organization->paramsConfig['hasDes']=='Y') {
            // DES
            $user->acl['isDes'] = $usergroupsTable->isDes($user);
            $user->acl['isManagerDes'] = $usergroupsTable->isManagerDes($user);
            $user->acl['isSuperReferenteDes'] = $usergroupsTable->isSuperReferenteDes($user);
            $user->acl['isReferenteDes'] = $usergroupsTable->isReferenteDes($user);
            $user->acl['isTitolareDesSupplier'] = $usergroupsTable->isTitolareDesSupplier($user);
            $user->acl['isReferentDesAllGas'] = $usergroupsTable->isReferentDesAllGas($user);
            $user->acl['isManagerUserDes'] = $usergroupsTable->isManagerUserDes($user);

            /*
            * DES associo il des_id se sono associato ad un solo DES
            */
            $desOrganizationsTable = TableRegistry::get('DesOrganizations');
            $where = ['organization_id' => $user_organization_id];
            $desOrganizations = $desOrganizationsTable->find()->select(['des_id'])->where($where)->all();
            if($desOrganizations->count()==1) {
                foreach($desOrganizations as $desOrganization) {
                    $user->des_id = $desOrganization->des_id;
                    break;
                }
            }
        }

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
    
    public function getList($user, $organization_id, $where=[]) {

        $where_user = ['organization_id' => $organization_id,
                        'block' => 0,
                        'username NOT LIKE' => 'dispensa@%'];
        if(!empty($where))
            $where_user = array_merge($where_user, $where);

        $results = $this->find('list', ['conditions' => $where_user, 'order' => ['name']]);
        return $results;
    }
      
    public function gets($user, $organization_id, $where=[]) {

        $where_user = ['organization_id' => $organization_id,
                        'block' => 0,
                        'username NOT LIKE' => 'dispensa@%'];
        if(!empty($where))
            $where_user = array_merge($where_user, $where);

        $this->addBehavior('Users');
        $results = $this->find()
                        ->contain(['UserProfiles'])
                        ->where($where_user)
                        ->order(['name'])
                        ->all();

        return $results;
    }

    /*
    * da portalgas per cart previews
    * crea stringa cifrata ma non leggibile
    * php 7.4 non supportato
    *   $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $username, MCRYPT_MODE_ECB);
    *
    * converte stringa cifrata in modo leggibile (MGCP+iQL/0qPiL2H62c+WXrnY856xfided9FJhjarEU=)
    *   $encrypted_base64 = base64_encode($encrypted);	
    */
    public function getUsernameCrypted($value) {
	 	
		if(!empty($value)) {
			try {
				$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(Configure::read('crypt_method')));
				$results = openssl_encrypt($value, Configure::read('crypt_method'), Configure::read('crypt_key'), 0, $iv);
				$results = base64_encode($results.'::'.$iv);
	        } catch (Exception $e) {
	            Log::error('_encoding '.$value);
	            Log::error('_encoding '.$iv);
	            Log::error($e);
	        }
		}

        return $results;
    }

	 /*
	  * creo url senza lo username, 
	  * in Cron::mailUsersOrdersOpen, quando ciclo per utenti ho gia' creato il messaggio per consegna
	  */
      public function getUrlCartPreviewNoUsername($user, $delivery_id) {
	 	 
        $tmp = "";
    
        $E = '';
        $O = '';
        $R = '';
        $D = '';
        $org_id = '';
         
        $E = $this->randomString($length=5);
         
        $O = rand (10, 99).$user->organization->id;
         
        $R = "{u}";
         
        $D = rand (10, 99).$delivery_id;
         
        $org_id = $user->organization->id;
    
        $tmp = 'E='.$E.'&O='.$O.'&R='.$R.'&D='.$D.'&org_id='.$org_id;
         
        return $tmp;
    }    
}