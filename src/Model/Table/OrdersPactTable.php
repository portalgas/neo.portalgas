<?php
namespace App\Model\Table;


use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

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

        $this->setEntityClass('App\Model\Entity\Order');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);

        $validator->setProvider('orderPact', \App\Model\Validation\OrderPactValidation::class);

        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'orderDuplicate' => [
                    'on' => ['create'], // , 'create', 'update',
                    'rule' => ['orderDuplicate'],
                    'provider' => 'orderPact',
                    'message' => 'Esiste già un ordine del produttore sulla consegna scelta'
                ]
            ]);  

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        // debug('OrdersPactTable buildRules');
        $rules = parent::buildRules($rules);

        return $rules;
    }

    /*
     * ovveride
     *  estrare il produttor del GAS che ha la gestione el listino al produttore associato ad un organization PACT
     */ 
    public function getSuppliersOrganizations($user, $organization_id, $pact_id=0, $where=[], $debug=false) {
        
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
            $owner_organization_id = $organizationResults->id;
            $owner_supplier_organization_id = $organizationResults->suppliers_organization->id;
            $owner_articles = 'PACT'; 
            /*
            debug('supplier_id '.$supplier_id);
            debug('owner_organization_id '.$owner_organization_id);
            debug('owner_supplier_organization_id '.$owner_supplier_organization_id);
            debug('owner_articles '.$owner_articles);
            */
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $results = $suppliersOrganizationsTable->getOwnSupplierBySupplierId($user, $supplier_id, $owner_organization_id, $owner_supplier_organization_id, $owner_articles, $debug);
        }
        return $results;     
    } 

    /*
     * implement
     */ 
    public function getDeliveries($user, $organization_id, $pact_id=0, $where=[], $debug=false) {
        
        $deliveriesTable = TableRegistry::get('Deliveries');
    
        $where = ['DATE(Deliveries.data) >= CURDATE()'];
        $deliveries = $deliveriesTable->getsList($user, $organization_id, $where);

        $sysDeliveries = $deliveriesTable->getDeliverySysList($user, $organization_id);

        $results = [];
        $results += $deliveries;
        $results += $sysDeliveries;

        return $results;   
    } 

    /*
     * implement
     * dati promozione / order des / gas_groups
     */   
    public function getParent($user, $organization_id, $promotion_id, $where=[], $debug=false) {

       if(empty($parent_id))
        $results = '';

       return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */   
    public function afterSaveWithRequest($user, $organization_id, $request, $debug=false) {

    }
    
    /*
     * implement
     */   
    public function getById($user, $organization_id, $order_id, $debug=false) {
       return parent::getById($user, $organization_id, $order_id, $debug);
    }
    
    /*
     * implement
     */      
    public function gets($user, $organization_id, $where=[], $debug=false) {
        
    }
    
    /*
     * implement
     */     
    public function getsList($user, $organization_id, $where=[], $debug=false) {
        
    }    
}
