<?php
namespace App\Model\Table;


use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use App\Decorator\ApiSuppliersOrganizationsReferentDecorator;

class OrdersDesTable extends OrdersTable implements OrderTableInterface
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

        $this->belongsTo('DesOrders', [
            'foreignKey' => 'des_order_id',
            'joinType' => 'INNER',
        ]);
        /*
         * ordine DES del proprio GAS
         */
        $this->belongsTo('DesOrdersOrganizations', [
            'foreignKey' => ['id'],        // fields Orders
            'bindingKey' => ['order_id'],  // fields DesOrdersOrganizations
            'joinType' => 'INNER',
        ]);
        /*
         * ordini DES di tutti i GAS
         */        
        $this->hasMany('AllDesOrdersOrganizations', [
            'className' => 'DesOrdersOrganizations',
            'foreignKey' => ['des_order_id'],   // fields Orders
            'bindingKey' => ['des_order_id'],   // fields DesOrdersOrganizations
            'joinType' => 'INNER',
        ]);        
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
        $rules->add($rules->existsIn(['des_order_id'], 'DesOrders'));

        return $rules;
    }
    
    /*
     * implement
     */ 
    public function getSuppliersOrganizations($user, $organization_id, $where=[], $debug=false) {

    }
    
    /*
     * implement
     */ 
    public function getDeliveries($user, $organization_id, $where=[], $debug=false) {

    }

    /*
     * implement
     * dati promozione / order des
     */   
    public function getParent($user, $organization_id, $des_order_id, $where=[], $debug=false) {

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

        if (empty($order_id)) {
            return null;
        }

        $results = $this->find()  
                        ->where([
                            $this->getAlias().'.organization_id' => $organization_id,
                            $this->getAlias().'.id' => $order_id
                        ])
                        ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries', 
                                    'SuppliersOrganizations' => ['Suppliers'],
                                    'DesOrdersOrganizations' => ['Des', 'DesOrders'], 
                                    'AllDesOrdersOrganizations' => ['Organizations'],
                                  /*
                                   * con Orders.owner_articles => chi gestisce il listino
                                   */
                                  'OwnerOrganizations', 'OwnerSupplierOrganizations'
                                  ])
                        ->first();        
        // debug($results);

        /*
         * produttori esclusi dal prepagato
         */
        if(!empty($results) && isset($user->organization->paramsConfig['hasCashFilterSupplier']) && $user->organization->paramsConfig['hasCashFilterSupplier']=='Y') {
            $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');
            $results->suppliers_organization->isSupplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->isSupplierOrganizationCashExcluded($user, $results->suppliers_organization->organization_id, $results->suppliers_organization->id);
        }
                     
        return $results; 
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