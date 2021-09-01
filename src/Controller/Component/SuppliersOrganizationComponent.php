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
                    $label .= '('.$suppliersOrganizations->supplier->descri.')';

                $results[$suppliersOrganizations->id] = $label;

            }
            else {
                foreach ($suppliersOrganizations as $suppliersOrganization) {
                    $label = $suppliersOrganization->name;
                    if(!empty($suppliersOrganization->supplier->descri))
                        $label .= '('.$suppliersOrganization->supplier->descri.')';

                    $results[$suppliersOrganization->id] = $label;
                }                
            }
        }

        return $results;
    }

    /*
     * dato un supplier_id, creo l'associazione con il GAS (suppliersOrganizations)
     */
    public function import($user, $supplier_id, $debug=false) {

        // $debug = true;
        
        $organization_id = $user->organization->id; // gas scelto

        /*
         * ctrl che il supplier sia Organizations.type = 'PRODGAS' o owner_organization_id
         */
        $suppliersTable = TableRegistry::get('Suppliers');

        $where = ['Suppliers.owner_organization_id != ' => 0,
                 'Suppliers.stato' => ['Y', 'T']];
        $supplier = $suppliersTable->find()
                    ->where($where)
                    ->first();

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
                 'SuppliersOrganizations.supplier_id' => $supplier_id];
        $suppliersOrganization = $suppliersOrganizationsTable->find()
                    ->where($where)
                    ->first();
        if($debug) debug($where);
        if($debug) debug($suppliersOrganization);
debug($suppliersOrganization);
exit;
        if(!empty($suppliersOrganization))  {
            $esito = true;
            $code = 200;
            $msg = 'supplierOrganizationsExists - supplier exist with supplier_id = ['.$supplier_id.'], organization_id = ['.$organization_id.'] => not insert';
            $action = false;
        }
        else {

            /*
             * creo SuppliersOrganizations ma il listino lo gestisce il produttore
             */
            $suppliersTable = TableRegistry::get('Suppliers');

            $supplier = $suppliersTable->find()
                                    ->contain(['SuppliersOrganizations' =>  
                                                ['Organizations' => ['conditions' => ['type' => 'PRODGAS']]]])
                                    ->where(['Suppliers.id' => $supplier_id])
                                    ->first();
             debug($supplier);
            $data_override = [];
            $data_override['owner_articles'] = 'SUPPLIER';
            $data_override['owner_organization_id'] = $supplier->owner_organization_id;
            $data_override['owner_supplier_organization_id'] = $supplier->suppliers_organizations[0]->id;
             debug($data_override);
exit;
            $results = $suppliersOrganizationsTable->create($organization_id, $supplier, $data_override);   
            if(!$results['esito']) {
                $esito = $results['esito'];
                $code = $results['code'];
                $msg = $results['msg'];
                $results = $results['results'];
            }
            else {
                $esito = true;
                $code = 200;
                $msg = 'supplierOrganizationsExists - supplier not exist with supplier_id = ['.$supplier_id.'], organization_id = ['.$organization_id.'] => insert';                
            }
            
            /* 
             * sempre a false perche' l'ho salvato prima
             */
            $action = false;
        }

        exit;
    }
}