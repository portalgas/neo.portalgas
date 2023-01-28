<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Traits;
use Cake\Log\Log;
use Cake\Datasource\ConnectionManager;

/**
 * SummaryDesOrders Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\DesOrdersTable&\Cake\ORM\Association\BelongsTo $DesOrders
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 *
 * @method \App\Model\Entity\SummaryDesOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\SummaryDesOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SummaryDesOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SummaryDesOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryDesOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SummaryDesOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryDesOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SummaryDesOrder findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SummaryDesOrdersTable extends Table
{
    use Traits\UtilTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_summary_des_orders');
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
            ->numeric('importo_orig')
            ->notEmptyString('importo_orig');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

        $validator
            ->numeric('importo_pagato')
            ->notEmptyString('importo_pagato');

        $validator
            ->scalar('modalita')
            ->notEmptyString('modalita');

        $validator
            ->scalar('nota')
            ->requirePresence('nota', 'create')
            ->notEmptyString('nota');

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

        return $rules;
    }

	/* 
	 *  estrae tutti i summarDesOrder di un des_supplier
	 */
	public function select_to_des_order($user, $des_order_id, $organization_id=0, $debug=false) {
		
		$where = ['SummaryDesOrders.des_id' => $user->des_id,
				  'SummaryDesOrders.des_order_id' => $des_order_id];
		if($organization_id>0) 
			$where += ['SummaryDesOrders.organization_id' => $organization_id];	

		$results = $this->find()->contain(['Organizations'])
                                ->where($where)
                                ->order(['Organizations.name'])
                                ->all()
                                ->toArray();

        if(empty($results))
            return [];

		/*
		 * per ogni ordine calcolo il totale importo
		 */
		$ordersTable = TableRegistry::get('Orders');
        $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');
		
		/*
		 * calcolo il totale degli importi degli acquisti dell'ordine per ogno GAS 
		 * perchè potrebbe essere modificato, se REFERENT-WORKING i referenti apportano modifiche
		*/
		foreach($results as $numResult => $result) {
		
			$organization_id = $result->organization_id;
			$des_order_id = $result->des_order_id;
			
			/*
			 * estraggo gli ordini associati
			 */
            $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');		
	    	$where = [];
	    	$where = ['DesOrdersOrganizations.des_id' => $user->des_id,
                    'DesOrdersOrganizations.organization_id' => $organization_id,
                    'DesOrdersOrganizations.des_order_id' => $des_order_id];
	    	$desOrdersOrganizationResults = $desOrdersOrganizationsTable->find()
                                                            ->select(['DesOrdersOrganizations.order_id'])
                                                            ->where($where)->first();
			
			$order_id = $desOrdersOrganizationResults->order_id;

			/*
			 * per ogni ordine calcolo il totale importo
			 */
			$tmp_user = $this->createObjUser(['organization_id' => $organization_id]);

			$importo_totale = $ordersTable->getTotImporto($tmp_user, $organization_id, $order_id, $debug);
			
			try {
				$sql = "UPDATE ".Configure::read('DB.prefix')."summary_des_orders  
						SET importo_orig = $importo_totale
						WHERE 
							des_id = ".(int)$user->des_id."
					    	and organization_id = ".(int)$organization_id."
					    	and des_order_id = ".(int)$des_order_id;
				if($debug)
					echo "<br />".$sql;
                $connection = ConnectionManager::get('default');
				$sqlResults = $connection->execute($sql);
			}
			catch (Exception $e) {
				Log::error($e->getMessage());
				return false;
			}

			$results[$numResult]->importo_orig = $importo_totale;
			$results[$numResult]->importo_orig_ = number_format($importo_totale,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
			$results[$numResult]->importo_orig_e = $results[$numResult]->importo_orig_.' &euro;';
			
		} // loop Order.importo_totale
					
		return $results;
	}
}
