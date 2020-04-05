<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

/*
$user->group_ids = 
	[
		(int) 2 => 'Registered',
		(int) 18 => 'gasReferente',
	]
*/
class AuthComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
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
     * 		gestisce anche il pagamento del suo produttore
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
     * verifica se un utente ha la gestione degli articoli sugli ordini
     * dipende da 
     * 		- Organization.hasArticlesOrder
     * 		- User.hasArticlesOrder
     * 
     * anche in AppHelper, AppModel
     */ 
    public function isUserPermissionArticlesOrder($user) {
        if (isset($user) && $user['organization']->hasArticlesOrder == 'Y' && $user->user->hasArticlesOrder == 'Y')
            return true;
        else
            return false;
    }

    public function getAclSupplierOrganizations($user) {

        $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');

        $where = ['SuppliersOrganizationsReferents.organization_id' => $user['organization']->id];
        if(!$this->isSuperReferente($user))
            $where += ['user_id' => $user->id];
        // debug($where);
        $suppliersOrganizationsReferents = $suppliersOrganizationsReferentsTable->find()
                                ->where($where)
                                ->contain(['SuppliersOrganizations' => ['Suppliers']])
                                ->all();
        // debug($suppliersOrganizationsReferents);
        return $suppliersOrganizationsReferents;
    }
    
    public function getAclSupplierOrganizationsList($user) {
        
        $results = [];
        $suppliersOrganizationsReferents = $this->getAclSupplierOrganizations($user);
        
        if($suppliersOrganizationsReferents->count()>0) {
            foreach ($suppliersOrganizationsReferents as $suppliersOrganizationsReferent) {
                    $results[$suppliersOrganizationsReferent->suppliers_organization->id] = $suppliersOrganizationsReferent->suppliers_organization->name;
            }
        }
        return $results;
    }    
}