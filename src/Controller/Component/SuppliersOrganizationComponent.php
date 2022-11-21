<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class SuppliersOrganizationComponent extends Component {

    protected $_registry;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function getListByResults($user, $suppliersOrganizations, $debug=false) {

        $results = [];

        if(!empty($suppliersOrganizations)) {
            if(!is_array($suppliersOrganizations) && !$suppliersOrganizations instanceof \Cake\ORM\ResultSet) {
                $label = $suppliersOrganizations->name;
                if(!empty($suppliersOrganizations->supplier->descri))
                    $label .= ' ('.$suppliersOrganizations->supplier->descri.')';

                $results[$suppliersOrganizations->id] = $label;

            }
            else {
                foreach ($suppliersOrganizations as $suppliersOrganization) {
                    $label = $suppliersOrganization->name;
                    if(!empty($suppliersOrganization->supplier->descri))
                        $label .= ' ('.$suppliersOrganization->supplier->descri.')';

                    $results[$suppliersOrganization->id] = $label;
                }                
            }
        }

        return $results;
    }

    /*
     * dato un supplier_id, creo l'associazione con il GAS (suppliersOrganizations)
     * options = owner_articles  'SUPPLIER', 'REFERENT', 'DES', 'PACT'
     *           owner_organization_id 
     *           owner_supplier_organization_id
     */
    public function import($user, $supplier_id, $options=[], $debug=false) {

        // $debug = true;
        $continua = true;

        $results = [];
        $results['esito'] = true;
        $results['msg'] = '';
        $results['msg_human'] = '';
        $results['datas'] = [];
            
        $organization_id = $user->organization->id; // gas scelto

        /*
         * ctrl se il GAS non
         * gia' associato => salto
         * e' in stato N => lo attivo
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
                 'SuppliersOrganizations.supplier_id' => $supplier_id];
        if($debug) debug($where);
        $suppliersOrganization = $suppliersOrganizationsTable->find()
                    ->where($where)
                    ->first();
        
        if($debug) debug($suppliersOrganization);

        if(!empty($suppliersOrganization))  {

            switch ($suppliersOrganization->stato) {
                case 'N':
                    /*
                     * lo riattivo
                     */
                    $datas = [];
                    $datas['stato'] = 'Y';
                    $suppliersOrganization = $suppliersOrganizationsTable->patchEntity($suppliersOrganization, $datas);
                    if (!$suppliersOrganizationsTable->save($suppliersOrganization)) {
                        $errors = $suppliersOrganization->getErrors();
                        $results['esito'] = false;
                        $results['msg'] = $errors;
                        $results['msg_human'] = 'Errore nel salvataggio del produttore';
                        $results['datas'] = $suppliersOrganization;

                        $continua = false;
                    }     
                break;
                case 'Y':
                    /*
                     * gia' presente
                     */
                    $results['esito'] = false;
                    $results['msg'] = $suppliersOrganization;
                    $results['msg_human'] = 'Produttore già presente';
                    $results['datas'] = $suppliersOrganization;

                    $continua = false;
                break;
                default:
                    $results['esito'] = false;
                    $results['msg'] = $suppliersOrganization->stato;
                    $results['msg_human'] = 'Stato del produttore non previsto';
                    $results['datas'] = $suppliersOrganization;

                    $continua = false;
                break;
            }
        }
        else {
            /*
             * produttore non presente, lo associo al GAS
             */            
            $suppliersTable = TableRegistry::get('Suppliers');

            $supplier = $suppliersTable->find()
                                    ->where(['Suppliers.id' => $supplier_id])
                                    ->first();
            // debug($supplier);


            $suppliersOrganizationResults = $suppliersOrganizationsTable->create($organization_id, $supplier, $options);
            if($suppliersOrganizationResults['esito']===false) {
                $results['esito'] = false;
                $results['msg'] = $suppliersOrganizationResults['esito'];
                $results['msg_human'] = $suppliersOrganizationResults['msg_human'];
                $results['datas'] = $suppliersOrganizationResults['datas'];

                $continua = false;                
            }

            if($continua) {
                // restituisco il suppliersOrganizations creato, ex id
                $results['datas'] = $suppliersOrganizationResults['datas'];;
            }
        }

        return $results;

    }
}