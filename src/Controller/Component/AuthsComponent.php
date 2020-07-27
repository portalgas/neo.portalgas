<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class AuthsComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
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
        if (isset($user) && $user->organization->paramsConfig['hasArticlesOrder'] == 'Y' && $user->user->hasArticlesOrder == 'Y')
            return true;
        else
            return false;
    }

    public function getAclSupplierOrganizations($user) {

        $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');

        $where = ['SuppliersOrganizationsReferents.organization_id' => $user->organization->id];
        if(!$user->acl['isSuperReferente'])
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