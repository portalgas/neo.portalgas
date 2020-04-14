<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Cashes Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Cash get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cash newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cash[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cash|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cash saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cash patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cash[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cash findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CashesTable extends Table
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

        $this->setTable('k_cashes');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

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

    public function getByUser($user, $organization_id, $user_id, $options=[], $debug=false) {

        $results = [];

        $where = ['Cashes.organization_id' => $organization_id,
                  'Cashes.user_id' => $user_id];
        if($debug) debug($where);

        $results = $this->find()
                        ->where($where)
                        ->first();

        if($debug) debug($results);
        
        return $results;
    }

    /* 
     * dato un importo, calcolo il nuovo valore di cassa di uno user
     * ex SummaryOrders.importo 
     */
    public function getNewImport($user, $importo_da_pagare, $cash_importo, $debug=false) {

        $results = ($cash_importo - $importo_da_pagare);       
                
        return $results;
    }

    /*
     * il saldo precedente lo metto in cashes_histories 
     */
    public function insert($user, $data, $debug=false) {
        
        if($debug) debug($data);

        $user_cash_empty = false;
        $organization_id = $data['organization_id'];
        $user_id = $data['user_id'];

        if(empty($organization_id) || empty($user_id))
            return false;

        /*
         * ricerco la cassa per lo user per persisterlo in cashes_histories
         */
        $options = [];
        $cashResults = $this->getByUser($user, $organization_id, $user_id, $options, $debug);
        if(!empty($cashResults))
            $user_cash_empty = true;
debug($cashResults);
debug($user_cash_empty);
        if($debug) debug("cash importo before ".$cashResults->importo);

        if(isset($data['importo_da_pagare'])) {
            /*
             * lo devo calcolare: importo_da_pagare - importo in cassa
             */ 
            if(isset($cashResults['importo']))
                $cash_importo = $cashResults['importo'];
            else
                $cash_importo = 0;            
            
            $importo_new = $this->getNewImport($user, $data['importo_da_pagare'], $cash_importo, $debug);   
            $data['importo'] = $importo_new;
        }
                    
        if(!isset($data['importo'])) 
            $data['importo'] = 0;

        /*
         * lo user non ha una voce di cassa
         */
        if(!$user_cash_empty)
            $cashResults = $this->newEntity();

        $cash = $this->patchEntity($cashResults, $data);
        if($debug) debug("cash importo after ".$cashResults->importo);
        if (!$this->save($cash)) {
            debug($cash->getErrors());
            return false;
        }
        else {

            /*
             * la prima volta che inserisco in Cashes non creo CashesHistories
             */
            if($user_cash_empty) {
                $id = $cash->id;

               // $cash = $this->Cashes->get($id);

                $data = [];
                $data['id'] = null;
                $data['cash_id'] = $id;
                $data['organization_id'] = $cash->organization_id;
                $data['user_id'] = $cash->user_id;
                $data['nota'] = $cash->nota;
                $data['importo'] = $cash->importo;
                debug($data);
                /*
                 * CashesHistories
                 */ 
                $cashesHistoriesTable = TableRegistry::get('CashesHistories');
                $cashesHistories = $cashesHistoriesTable->newEntity();
                $cashesHistories = $cashesHistoriesTable->patchEntity($cashesHistories, $data);
                if (!$cashesHistoriesTable->save($cashesHistories)) {
                    debug($cashesHistories->getErrors());
                    return false;
                }                

            } // end if(!$user_cash_empty)
        }

        return true;
    }
}
