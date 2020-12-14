<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class OrdersPromotionTable extends OrdersTable implements OrderTableInterface 
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

        $this->belongsTo('ProdGasPromotions', [
            'foreignKey' => 'prod_gas_promotion_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator->setProvider('orderPromotion', \App\Model\Validation\OrderPromotionValidation::class);

        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'orderDuplicate' => [
                    'on' => ['create'], // , 'create', 'update',
                    'rule' => ['orderDuplicate'],
                    'provider' => 'orderPromotion',
                    'message' => 'Esiste già un ordine del produttore sulla consegna scelta'
                ]
            ]);  


        return $validator;
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
        $rules->add($rules->existsIn(['prod_gas_promotion_id'], 'ProdGasPromotions'));
       
        return $rules;
    }

    /*
     * implement
     */ 
    public function getSuppliersOrganizations($user, $organization_id, $where=[], $debug=false) {
        
        $results = [];

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
    
        $where = ['SuppliersOrganizations.organization_id' => $organization_id,
                  'SuppliersOrganizations.stato' => 'Y',
                  'SuppliersOrganizations.can_promotions' => 'Y',
                  'SuppliersOrganizations.owner_articles' => 'REFERENT'];

        $results = $suppliersOrganizationsTable->find()
                                ->where($where)
                                ->contain(['Suppliers', 'CategoriesSuppliers'])
                                ->order(['SuppliersOrganizations.name'])
                                ->all();
        return $results;        
    } 

    /*
     * implement
     */ 
    public function getDeliveries($user, $organization_id, $where=[], $debug=false) {

        $prodGasPromotionsOrganizationsDeliveriesTable = TableRegistry::get('ProdGasPromotionsOrganizationsDeliveries');
    
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                                'Deliveries.stato_elaborazione' => 'OPEN',
                                'Deliveries.sys' => 'N',
                                'DATE(Deliveries.data) >= CURDATE()'];
        $results = $prodGasPromotionsOrganizationsDeliveriesTable->getsList($user, $organization_id, $where);

        return $results;        
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
                                    'ProdGasPromotions',
                                  /*
                                   * con Orders.owner_articles => chi gestisce il listino
                                   */
                                  'OwnerOrganizations', 'OwnerSupplierOrganizations'
                                  ])
                        ->first();        
        // debug($results);
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
