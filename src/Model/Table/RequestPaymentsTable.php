<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;

/**
 * RequestPayments Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\RequestPayment get($primaryKey, $options = [])
 * @method \App\Model\Entity\RequestPayment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RequestPayment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RequestPayment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RequestPayment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RequestPayment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RequestPayment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RequestPayment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RequestPaymentsTable extends Table
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

        $this->setTable('k_request_payments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create');

        $validator
            ->nonNegativeInteger('num')
            ->allowEmptyString('num');

        $validator
            ->scalar('stato_elaborazione')
            ->notEmptyString('stato_elaborazione');

        $validator
            ->date('stato_elaborazione_date')
            ->requirePresence('stato_elaborazione_date', 'create')
            ->notEmptyDate('stato_elaborazione_date');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->date('data_send')
            ->requirePresence('data_send', 'create')
            ->notEmptyDate('data_send');

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
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

	public  function getRequestPaymentByOrderId($user, $order_id, $debug=false) {
        
        $requestPaymentsOrdersTable = TableRegistry::get('RequestPaymentsOrders');
        
		$where = ['RequestPaymentsOrders.organization_id' => $user->organization->id,
				  'RequestPaymentsOrders.order_id' => $order_id];
		$requestPaymentsOrderResults = $requestPaymentsOrdersTable->find()
                                        ->contain(['RequestPayments'])
                                        ->first();		
		
		return $requestPaymentsOrderResults;
	}
	
	public  function getRequestPaymentIdByOrderId($user, $order_id, $debug=false) {
        
		$requestPaymentsOrder = $this->getRequestPaymentByOrderId($user, $order_id, $debug);		
		$request_payment_id = $requestPaymentsOrder->request_payment->id;

		return $request_payment_id;
	}
	
	public  function getRequestPaymentNumByOrderId($user, $order_id, $debug=false) {
        
		$requestPaymentsOrder = $this->getRequestPaymentByOrderId($user, $order_id, $debug);		
		$request_payment_num = $requestPaymentsOrder->request_payment->num;

		return $request_payment_num;
	}
}
