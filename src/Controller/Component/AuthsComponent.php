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
     *      - Organization.hasArticlesOrder
     *      - User.hasArticlesOrder
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

        $results = [];
                  
        if($user->acl['isSuperReferente']) {
            /* 
             * SUPER-REFERENTE
             */         
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $where = ['SuppliersOrganizations.organization_id' => $user->organization->id,
                      'SuppliersOrganizations.stato' => 'Y'];
             $results = $suppliersOrganizationsTable->find()
                            ->where($where)
                            ->contain(['Suppliers'])
                            ->all();            
        }
        else {
            /* 
             * REFERENTE
             */
            $suppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');

            $where = ['SuppliersOrganizationsReferents.organization_id' => $user->organization->id,
                      'SuppliersOrganizationsReferents.user_id' => $user->id];

            $results = $suppliersOrganizationsReferentsTable->find()
                                    ->where($where)
                                    ->contain(['SuppliersOrganizations' => ['conditions' => ['SuppliersOrganizations.stato' => 'Y'], 
                                              'Suppliers' => ['conditions' => ['Suppliers.stato != ' => 'N']]]]) 
                                    ->all();
        }
        
        return $results;
    }
    
    public function getAclSupplierOrganizationsList($user) {
       
        $results = [];
        $suppliersOrganizations = $this->getAclSupplierOrganizations($user);
        
        if($suppliersOrganizations->count()>0) {
            if($user->acl['isSuperReferente']) {
                /* 
                 * SUPER-REFERENTE
                 */             
                foreach ($suppliersOrganizations as $suppliersOrganization) {
                        $results[$suppliersOrganization->id] = $suppliersOrganization->name;
                }
            }
            else {
                /* 
                 * REFERENTE
                 */             
                foreach ($suppliersOrganizations as $suppliersOrganization) {
                        $results[$suppliersOrganization->suppliers_organization->id] = $suppliersOrganization->suppliers_organization->name;
                }
            }
        }
        return $results;
    }    
}