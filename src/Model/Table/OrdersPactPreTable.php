<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

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

        $this->entityClass('App\Model\Entity\Order');
    }

    public function validationDefault(Validator $validator)
    {
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
     * ovveride
     */ 
    public function getDeliveries($user, $pact_id=0, $debug=false) {
        
        $deliveriesTable = TableRegistry::get('Deliveries');
        $results = $deliveriesTable->getDeliverySysList($user);

        return $results;   
    } 
}