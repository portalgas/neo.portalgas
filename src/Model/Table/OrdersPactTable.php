<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 * @property \App\Model\Table\OwnerOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerOrganizations
 * @property \App\Model\Table\OwnerSupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnerSupplierOrganizations
 * @property \App\Model\Table\DeliveriesTable&\Cake\ORM\Association\BelongsTo $Deliveries
 * @property \App\Model\Table\ProdGasPromotionsTable&\Cake\ORM\Association\BelongsTo $ProdGasPromotions
 * @property \App\Model\Table\DesOrdersTable&\Cake\ORM\Association\BelongsTo $DesOrders
 *
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdersPactTable extends OrdersTable implements OrderTableInterface 
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

        $this->entityClass('App\Model\Entity\Order');
    }

    /*
     * ovveride
     *  estrare il produttor del GAS che ha la gestione el listino al produttore associato ad un organization PACT
     */ 
    public function getSuppliersOrganizations($user, $pact_id=0, $debug=false) {
        
        $results = [];

        /*
         * cerco l'organization PACT e il suop SuppliersOrganizations
         */
        $organizationsTable = TableRegistry::get('Organizations');
        $organizationResults = $organizationsTable->find() 
                                  //  ->select('id')
                                    ->where([
                                        'Organizations.type' => 'PACT',
                                        'Organizations.stato' => 'Y',
                                    ])
                                    ->contain(['SuppliersOrganizations'])
                                    ->first(); 
        // debug($organizationResults);
        if(!empty($organizationResults) && $organizationResults->has('suppliers_organization') && !empty($organizationResults->suppliers_organization)) {

            /*
             * cerco SuppliersOrganizations del GAS, che abbia dato la gestione del listino al produttte
             */
            $supplier_id = $organizationResults->suppliers_organization->supplier_id;
            $owner_organization_i            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $results = $suppliersOrganizationsTable->getOwnSupplierBySupplierId($user, $supplier_id, $owner_organization_id, $owner_supplier_organization_id, $owner_articles, $debug);d = $organizationResults->id;
            $owner_supplier_organization_id = $organizationResults->suppliers_organization->id;
            $owner_articles = 'SUPPLIER';
            

        }
        return $results;     
    } 

    /*
     * ovveride
     */ 
    public function getDeliveries($user, $pact_id=0, $debug=false) {
        
        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getsList($user);

        return $results;   
    } 
}
