<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrdersSocialMarketTable extends OrdersTable implements OrderTableInterface
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
        
        $validator->setProvider('order', \App\Model\Validation\OrderValidation::class);

        return $validator;
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

        $results = [];
        $where_order = [];

        if(Configure::read('social_market_delivery_id')===false)
            return $results;

        if(isset($where['Orders']))
            $where_order = $where['Orders'];
        $where_order = array_merge([$this->getAlias().'.organization_id' => Configure::read('social_market_organization_id')],
            $where_order);
        if($debug) debug($where_order);

        $where_delivery = ['Deliveries.organization_id' => Configure::read('social_market_organization_id'),
                           'Deliveries.id' => Configure::read('social_market_delivery_id')];

        $results = $this->find()
            ->where($where_order)
            ->contain([
                'OrderTypes' => ['conditions' => ['code' => 'SOCIALMARKET']],
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
        
    }     
}