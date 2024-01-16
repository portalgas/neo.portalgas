<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class UsergroupsTable extends Table
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

        $this->setTable('j_usergroups');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('ParentJUsergroups', [
            'className' => 'JUsergroups',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildJUsergroups', [
            'className' => 'JUsergroups',
            'foreignKey' => 'parent_id'
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('rgt')
            ->notEmptyString('rgt');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->notEmptyString('title');

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentJUsergroups'));

        return $rules;
    }

    public function isRoot($user) {        
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_root'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isRootSupplier($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_root_supplier'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * manager
     */
    public function isManager($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_manager'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isManagerDelivery($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_manager_delivery'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * referente
     */
    public function isReferente($user) {
        if (isset($user) && $user->id != 0) {
            if (Configure::read('developer.mode'))
                return true;

            if (array_key_exists(Configure::read('group_id_referent'), $user->group_ids))
                return true;
        }
        return false;
    }

    /*
     * super-referente, gestisce tutti i produttori 
     */
    public function isSuperReferente($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_super_referent'), $user->group_ids)) 
            return true;
        else 
            return false;
    }

    /*
     * referente cassa (pagamento degli utenti alla consegna)
     */
    public function isCassiere($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_cassiere'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * referente cassa (pagamento degli utenti alla consegna) dei produttori di cui e' referente
     */
    public function isReferentCassiere($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_referent_cassiere'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * referente tesoriere (pagamento con richiesta degli utenti dopo consegna)
     *      gestisce anche il pagamento del suo produttore
     */
    public function isReferentTesoriere($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_referent_tesoriere'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isReferentGeneric($user) {
        if (isset($user) && $user->id != 0 && (
                array_key_exists(Configure::read('group_id_referent'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_super_referent'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_referent_tesoriere'), $user->group_ids)
                ))
            return true;
        else
            return false;
    }

    public function isCassiereGeneric($user) {
        if (isset($user) && $user->id != 0 && (
                array_key_exists(Configure::read('group_id_cassiere'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_referent_cassiere'), $user->group_ids)
                ))
            return true;
        else
            return false;
    }

    /*
     *  pagamento ai fornitori
     */
    public function isTesoriere($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_tesoriere'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isTesoriereGeneric($user) {
        if (isset($user) && $user->id != 0 && (
                array_key_exists(Configure::read('group_id_referent_tesoriere'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_tesoriere'), $user->group_ids)
                ))
            return true;
        else
            return false;
    }

    public function isStoreroom($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_storeroom'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * DES
     */
    public function isDes($user) {
        if (isset($user) && $user->id != 0 && (
                array_key_exists(Configure::read('group_id_manager_des'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_referent_des'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_super_referent_des'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_titolare_des_supplier'), $user->group_ids) ||
                array_key_exists(Configure::read('group_id_des_supplier_all_gas'), $user->group_ids)
                ))
            return true;
        else
            return false;
    }

    public function isManagerDes($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_manager_des'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isReferenteDes($user) {
        if (isset($user) && $user->id != 0) {
            if (Configure::read('developer.mode'))
                return true;

            if (array_key_exists(Configure::read('group_id_referent_des'), $user->group_ids))
                return true;
        }
        return false;
    }

    public function isSuperReferenteDes($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_super_referent_des'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isTitolareDesSupplier($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_titolare_des_supplier'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isReferentDesAllGas($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_des_supplier_all_gas'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isManagerUserDes($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_user_manager_des'), $user->group_ids))
            return true;
        else
            return false;
    }
    
    public function isUserFlagPrivay($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_user_flag_privacy'), $user->group_ids))
            return true;
        else
            return false;
    }
    
    /*
     * gestisce i calendar events
     */
    public function isManagerEvents($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_events'), $user->group_ids))
            return true;
        else
            return false;
    }  

    /*
     * gruppi
     */    
    public function isGasGroupsManagerGroups($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_gas_groups_manager_groups'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isGasGroupsManagerDeliveries($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_gas_groups_manager_consegne'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isGasGroupsManagerParentOrders($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_gas_groups_manager_parent_orders'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isGasGroupsManagerOrders($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_gas_groups_manager_orders'), $user->group_ids))
            return true;
        else
            return false;
    }

    public function isGasGroupsCassiere($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('group_id_gas_groups_id_cassiere'), $user->group_ids))
            return true;
        else
            return false;
    }

    /*
     * produttori, ha i gruppi gasSuperReferente / prodGasSupplierManager 
     */
    public function isProdGasSupplierManager($user) {
        if (isset($user) && $user->id != 0 && array_key_exists(Configure::read('prod_gas_supplier_manager'), $user->group_ids))
            return true;
        else
            return false;
    }  
}
