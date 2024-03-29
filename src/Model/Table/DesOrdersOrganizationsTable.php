<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DesOrdersOrganizations Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\DesOrdersTable&\Cake\ORM\Association\BelongsTo $DesOrders
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\DesOrdersOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DesOrdersOrganization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DesOrdersOrganizationsTable extends Table
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

        $this->setTable('k_des_orders_organizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DesOrders', [
            'foreignKey' => 'des_order_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
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
            ->scalar('luogo')
            ->maxLength('luogo', 225)
            ->allowEmptyString('luogo');

        $validator
            ->date('data')
            ->allowEmptyString('data');

        $validator
            ->time('orario')
            ->allowEmptyString('orario');

        $validator
            ->scalar('contatto_nominativo')
            ->maxLength('contatto_nominativo', 150)
            ->allowEmptyString('contatto_nominativo');

        $validator
            ->scalar('contatto_telefono')
            ->maxLength('contatto_telefono', 20)
            ->allowEmptyString('contatto_telefono');

        $validator
            ->scalar('contatto_mail')
            ->maxLength('contatto_mail', 100)
            ->allowEmptyString('contatto_mail');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

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
        $rules->add($rules->existsIn(['des_id'], 'Des'));
        $rules->add($rules->existsIn(['des_order_id'], 'DesOrders'));
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));

        return $rules;
    }

  	/*
  	 * dato un ordine estraggo l'eventuale DesOrder 
  	 */
  	function getDesOrdersOrganization($user, $order_id, $debug = false) {

		$where = ['DesOrdersOrganizations.organization_id' => $user->organization->id,
                  'DesOrdersOrganizations.order_id' => $order_id];
		if(!empty($user->des_id))
			$where += ['DesOrdersOrganizations.des_id' => $user->des_id];	    								   	   
							
		$desOrdersOrganization = $this->find()
                                        ->contain(['DesOrders'])
                                        ->where($where)->first();
						
		return $desOrdersOrganization;
	}
}
