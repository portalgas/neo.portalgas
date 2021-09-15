<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class ProdGasSupplierComponent extends Component {

    public $components = ['SuppliersOrganization', 'SuppliersOrganizationsReferent'];

    /*
     * dato un supplier_id, creo l'associazione con il GAS (suppliersOrganizations)
     * produttore che gestisce il listino articoli (Organizations.type = 'PRODGAS' o owner_organization_id)
     */
    public function import($user, $supplier_id, $debug=false) {

        // $debug = true;
        $results = [];
               
        $organization_id = $user->organization->id; // gas scelto

        /*
         * ctrl che il supplier sia Organizations.type = 'PRODGAS' o owner_organization_id
         */
        $prodGasSuppliersTable = TableRegistry::get('ProdGasSuppliers');

        $where = [];
        $where['Suppliers'] = ['Suppliers.stato IN ' => ['Y', 'T']];
        $supplier = $prodGasSuppliersTable->getBySupplierId($user, $supplier_id, $where, $debug);

        if(!empty($supplier)) {

            /*
             * creo occorrenza SuppliersOrganization
             */
            $options = [];
            $options['owner_articles'] = 'SUPPLIER';
            $options['owner_organization_id'] = $supplier->owner_supplier_organization->organization_id;
            $options['owner_supplier_organization_id'] = $supplier->owner_supplier_organization->id;

            $results = $this->SuppliersOrganization->import($user, $supplier_id, $options, $debug);

            if($results['esito']==true) {

                $suppliersOrganization = $results['datas'];
                $supplier_organization_id = $suppliersOrganization->id;

                $results = $SuppliersOrganizationsReferent->import($user, $supplier_organization_id);          
            }
        }
     
        return $results;
    }
}