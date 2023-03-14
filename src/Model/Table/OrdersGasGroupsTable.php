<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrdersGasGroupsTable extends OrdersTable implements OrderTableInterface 
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

        $this->belongsTo('GasGroups', [
            'foreignKey' => 'gas_group_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ParentDeliveries', [
            'class' => 'Deliveries',
            'foreignKey' => 'parent_id',
            'joinType' => 'INNER',
        ]);        
    }

    public function validationDefault(Validator $validator)
    {
        $validator->setProvider('orderGasGroups', \App\Model\Validation\OrderGasGroupsValidation::class);
       
        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'totArticles' => [
                   'rule' => ['totArticles'],
                   'provider' => 'orderGasGroups',
                   'message' => 'Il produttore scelto non ha articoli che si possono associare ad un ordine'
                ]
            ]);

        $validator
            ->notEmpty('data_fine')
            ->add('data_fine', [
                'totArticles' => [
                   'rule' => ['dateFine'],
                   'provider' => 'orderGasGroups',
                   'message' => "La data di chiusura non può essere posteriore o uguale alla data di chiusura dell'ordine titolare"
                ]
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {   
        // debug('OrdersGasTable buildRules');
        $rules = parent::buildRules($rules);

        return $rules;
    }

    /*
     * implement 
     * 
     * il parent (ordine principale) gli passa il supplier_organization_id
     */ 
    public function getSuppliersOrganizations($user, $organization_id, $user_id, $where=[], $debug=false) {

        $results = [];

        // lo eredita dal parent (ordine principale)
        if(empty($where) && !isset($where['supplier_organization_id']))
            return $results;
            
        $where2 = [];
        $where2['SuppliersOrganizations'] = ['SuppliersOrganizations.id' => $where['supplier_organization_id']];
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $results = $suppliersOrganizationsTable->ACLgets($user, $organization_id, $user_id, $where2);

        return $results;

        /* 
         * estraggo i produttori con orders.order_type_id' => Configure::read('Order.type.gas_parent_groups')
         * con consegne ancora aperte
         * e produttori profilati
         * 
         */        
        $suppliers_organizations_ids = [];

        $ordersTable = TableRegistry::get('Orders');
        $where = [];
        $where['Orders'] = ['Orders.order_type_id' => Configure::read('Order.type.gas_parent_groups')];
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                                'Deliveries.stato_elaborazione' => 'OPEN',
                                'DATE(Deliveries.data) >= CURDATE()'];
        $orders = $ordersTable->gets($user, $organization_id, $where);
        if($orders->count()) {
            foreach($orders as $order) {
                array_push($suppliers_organizations_ids, $order->supplier_organization_id);
            }
        }

        if(!empty($suppliers_organizations_ids)) {
            $where = [];
            $where['SuppliersOrganizations'] = ['SuppliersOrganizations.id IN ' => $suppliers_organizations_ids];
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $results = $suppliersOrganizationsTable->ACLgets($user, $organization_id, $user_id, $where);
        }

        return $results;
    } 

    /*
     * implement
     */ 
    public function getDeliveries($user, $organization_id, $where=[], $debug=false) {

        $results = [];

        if(!isset($where['gas_group_id']))
            return $results;

        $gas_group_id = $where['gas_group_id'];
        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');
        $results = $gasGroupDeliveriesTable->getsActiveList($user, $organization_id, $gas_group_id);

        return $results;
    }    

    /*
     * implement
     * dati promozione / order des
     */   
    public function getParent($user, $organization_id, $parent_id, $where=[], $debug=false) {
      
       if(empty($parent_id))
        $results = '';

        $ordersGasTable = TableRegistry::get('OrdersGas');

        $where = ['OrdersGas.organization_id' => $organization_id,
                  'OrdersGas.id' => $parent_id];

        $results = $ordersGasTable->find()
                                    ->where($where)
                                    ->contain(['OrderStateCodes', 'Deliveries', 'SuppliersOrganizations' => ['Suppliers']])
                                    ->first();

        return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */   
    public function afterAddWithRequest($user, $organization_id, $order, $request, $debug=false) {
        return parent::afterAddWithRequest($user, $organization_id, $order, $request, $debug);
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
       
        $results = [];
        $where_order = [];
        $where_delivery = [];

        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge([$this->getAlias().'.organization_id' => $organization_id,
                              $this->getAlias().'.isVisibleBackOffice' => 'Y'],
                              $where_order);
        if($debug) debug($where_order); 

        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id], $where_delivery);
                          
        if($debug) debug($where_delivery);
        $results = $this->find()
                                ->where($where)
                                ->contain([
                                  'OrderTypes' => ['conditions' => ['code' => 'GASGROUP']],
                                  'OrderStateCodes',
                                  'SuppliersOrganizations' => ['Suppliers'], 
                                  'Deliveries' => ['conditions' => $where_delivery]  
                                ])
                                ->order([$this->getAlias().'.data_inizio'])
                                ->all();
        // debug($results);
        
        return $results;
    }
    
    /*
     * implement
     */     
    public function getsList($user, $organization_id, $where=[], $debug=false) {
               
        $listResults = [];

        $results = $this->gets($user, $organization_id, $where);
        if(!empty($results)) {
            foreach($results as $result) {
                 /*
                  * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                  * key array non per id, nel json perde l'ordinamento della data
                  * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM Y');
                  */                  
                // debug($result);exit;
                $listResults[$result->id] = $result->suppliers_organization->name.' - '.$result->delivery->data->i18nFormat('eeee d MMMM').' - '.$result->delivery->luogo;
            }
        }

        // debug($listResults);
        return $listResults;
    }         
}