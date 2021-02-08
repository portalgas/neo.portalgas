<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrdersPactPreTable extends OrdersPactTable implements OrderTableInterface 
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
        
        $validator->setProvider('order', \App\Model\Validation\OrderPactValidation::class);

        $validator
            ->notEmpty('supplier_organization_id')
            ->add('supplier_organization_id', [
                'orderDuplicate' => [
                    'on' => ['create'], // , 'create', 'update',
                    'rule' => ['orderDuplicate'],
                    'provider' => 'order',
                    'message' => 'Esiste già un ordine del produttore sulla consegna scelta'
                ]
            ]);  

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
        
        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getDeliverySysList($user, $organization_id);

        return $results;   
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
        
    }
    
    /*
     * implement
     */     
    public function getsList($user, $organization_id, $where=[], $debug=false) {
        
    }     
}