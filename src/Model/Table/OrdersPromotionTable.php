<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

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
        $validator = parent::validationDefault($validator);

        $validator->setProvider('orderPromotion', \App\Model\Validation\OrderPromotionValidation::class);

        $validator
            ->notEmpty('data_fine')
            ->add('data_fine', [
                'dateMaggiore' => [
                   // 'on' => ['create', 'update', 'empty']
                   'rule' => ['dateComparisonToParent'],
                   'provider' => 'orderPromotion',
                   'message' => 'La data di apertura non può essere posteriore alla data di chiusura della promozione'
                ],
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
    public function getSuppliersOrganizations($user, $organization_id, $user_id, $where=[], $debug=false) {
        
        $results = [];

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
    
        $where = array_merge(['SuppliersOrganizations.organization_id' => $organization_id,
                              'SuppliersOrganizations.stato' => 'Y',
                              // 'SuppliersOrganizations.owner_articles' => 'REFERENT',
                              'SuppliersOrganizations.can_promotions' => 'Y',], $where);
        // debug($where);
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

        $where_delivery = ['Deliveries.isVisibleFrontEnd' => 'Y',
            'Deliveries.stato_elaborazione' => 'OPEN',
            'Deliveries.sys' => 'N',
            'DATE(Deliveries.data) >= CURDATE()'];

        if(isset($where['Deliveries']))
            $where['Deliveries'] = array_merge($where_delivery, $where['Delivery']);
        else
            $where['Deliveries'] = $where_delivery;
        // debug($where);

        $results = $prodGasPromotionsOrganizationsDeliveriesTable->getsList($user, $organization_id, $where);

        return $results;        
    }

    /*
     * implement
     * dati promozione / order des / gas_groups
     */   
    public function getParent($user, $organization_id, $prod_gas_promotion_id, $where=[], $debug=false) {

       if(empty($parent_id))
        $results = '';

       $prodGasPromotionsTable = TableRegistry::get('ProdGasPromotions');

       $results = $prodGasPromotionsTable->getProdGasPromotion($user, $prod_gas_promotion_id, $organization_id, $debug);

       return $results;
    }

    /*
     * implement
     * ..behaviour afterSave() ha l'entity ma non la request
     */   
    public function afterSaveWithRequest($user, $organization_id, $request, $debug=false) {
        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');

        $where = ['ProdGasPromotionsOrganizations.prod_gas_promotion_id' => $request['parent_id'],
                  'ProdGasPromotionsOrganizations.organization_id' => $organization_id];
                
        $results = $prodGasPromotionsOrganizationsTable->find()
                                ->where($where)
                                ->first();

        $data = [];
        if(isset($request['nota_user']))
            $data['nota_user'] = $request['nota_user'];
        $data['user_id'] = $user->id;

        $results = $prodGasPromotionsOrganizationsTable->patchEntity($results, $data);
        if(!$prodGasPromotionsOrganizationsTable->save($results)) {
            debug($results->getErrors());
        }
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
