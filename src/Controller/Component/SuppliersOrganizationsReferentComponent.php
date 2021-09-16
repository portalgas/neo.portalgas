<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class SuppliersOrganizationsReferentComponent extends Component {

    /*
     * se !isSuperReferente = dato un supplier_organization_id e uno user creo l'associazione 
     * se !isReferentGeneric = lo metto nel gruppo
     * options = type (REFERENTE / COREFERENTE - default REFERENTE) / group_id (default Configure::read('group_id_referent'))
     */
    public function import($user, $supplier_organization_id, $options=[], $debug=false) {

        // $debug = true;
        $continua = true;

        $results = [];
        $results['esito'] = true;
        $results['msg'] = '';
        $results['msg_human'] = '';
        $results['datas'] = [];

            
        if(!isset($user->acl)) {
            $results['esito'] = false;
            $results['msg'] = 'user->acl required';
            $results['msg_human'] = "Errore nell'autenticazione";
            $results['datas'] = $user;

            $continua = false;
        }  

        if($continua && $user->acl['isSuperReferente']) {
            return $results;
        }  

        $user_id = $user->get('id');
        $organization_id = $user->organization->id; // gas scelto

        if($continua) {

            isset($options['type']) ?     $type = $options['type']: $type = 'REFERENTE';
            isset($options['group_id']) ? $group_id = $options['group_id']: $group_id = Configure::read('group_id_referent');

            $organization_id = $user->organization->id; // gas scelto

            $SuppliersOrganizationsReferentsTable = TableRegistry::get('SuppliersOrganizationsReferents');

            /*
             * ctrl che non esista gia'
             */
            $where = ['SuppliersOrganizationsReferents.organization_id' => $organization_id,
                      'SuppliersOrganizationsReferents.supplier_organization_id' => $supplier_organization_id,
                      'SuppliersOrganizationsReferents.user_id' => $user_id];
            $suppliersOrganizationsReferentResults = $SuppliersOrganizationsReferentsTable->find()
                                                                                        ->where($where)
                                                                                        ->first();

            if(!empty($suppliersOrganizationsReferentResults)) {
                return $results;
            }   
                                                                                                 
            /*
             * inserisco occorrenza
             */
            $datas = [];
            $datas['organization_id'] = $organization_id;
            $datas['supplier_organization_id'] = $supplier_organization_id;
            $datas['type'] = $type;
            $datas['user_id'] = $user_id;
            $datas['group_id'] = $group_id;
            if($debug) debug($datas);

            $suppliersOrganizationsReferent = $SuppliersOrganizationsReferentsTable->newEntity();
            $suppliersOrganizationsReferent = $SuppliersOrganizationsReferentsTable->patchEntity($suppliersOrganizationsReferent, $datas);
            // debug($suppliersOrganizationsReferent);
            if (!$SuppliersOrganizationsReferentsTable->save($suppliersOrganizationsReferent)) {
                $results['esito'] = false;
                $results['code'] = '500';
                $results['msg'] = $suppliersOrganizationsReferent->getErrors();
                $results['msg_human'] = "Errore nell'inserimento del produttore";
                $results['datas'] = $suppliersOrganizationsReferent->getErrors();  

                $continua = false;            
            }
        } // end if($continua)
     
        if($continua) {
            if(!$user->acl['isReferentGeneric']) {
                /* 
                 * associo lo user al gruppo referente (j_user_usergroup_map)
                 */
                $userUsergroupMapTable = TableRegistry::get('UserUsergroupMap');

                $datas = [];
                $datas['user_id'] = $user_id;
                $datas['group_id'] = $group_id;
                if($debug) debug($datas);

                $userUsergroupMap = $userUsergroupMapTable->newEntity();
                $userUsergroupMap = $userUsergroupMapTable->patchEntity($userUsergroupMap, $datas);
                // debug($userUsergroupMap);
                if (!$userUsergroupMapTable->save($userUsergroupMap)) {
                    $results['esito'] = false;
                    $results['code'] = '500';
                    $results['msg'] = $userUsergroupMap->getErrors();
                    $results['msg_human'] = "Errore nell'associzione dell'utente al gruppo referente";
                    $results['datas'] = $userUsergroupMap->getErrors();  

                    $continua = false;            
                }
            }  
        }

        return $results;
    }
}