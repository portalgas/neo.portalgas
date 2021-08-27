<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

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

        if(!empty($results)) {
            $results->importo_ = number_format($results->importo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
            $results->importo_e = number_format($results->importo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro;';          
        }

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
    
        // $debug=true;

        if($debug) debug($data);

        $organization_id = $data['organization_id'];
        $user_id = $data['user_id'];

        if(empty($organization_id) || empty($user_id))
            return false;

        if(isset($data['importo_da_pagare']) && $data['importo_da_pagare']==0) {
            if($debug) debug("importo_da_pagare = 0 => esco");
            return true;
        }
           
        /*
         * ricerco la cassa per lo user per persisterlo in cashes_histories 
         * solo se ho gia' occorrenz in Cashs
         */
        $options = [];
        $cashResults = $this->getByUser($user, $organization_id, $user_id, $options, $debug);
        if(empty($cashResults))
            $cash_history_save = false;
        else
            $cash_history_save = true;

        if($debug) {
            if(isset($cashResults['importo']))
                debug("cash importo before ".$cashResults['importo']);
            else
                debug("cash importo before 0");
        }
            
         /*
         * valori della cash corrente da persistere in k_cashes_histories
         */             
        $cash_importo = 0;            
        $cash_nota = '';             
        $cash_modified = '';            
        if(isset($data['importo_da_pagare'])) {
            
            /*
             * lo devo calcolare: importo_da_pagare - importo in cassa
             */ 
            if(isset($cashResults['importo']))
                $cash_importo = $cashResults['importo'];
            else
                $cash_importo = 0;            
 
             /*
             * recupero la nota della cash corrente per persisterla in k_cashes_histories
             */ 
            if(isset($cashResults['nota']))
                $cash_nota = $cashResults['nota'];
            else
                $cash_nota = ''; 

            if(isset($cashResults['modified']))
                $cash_modified = $cashResults['modified'];
            
            $importo_new = $this->getNewImport($user, $data['importo_da_pagare'], $cash_importo, $debug);   
            $data['importo'] = $importo_new;
        }
                    
        if(!isset($data['importo'])) 
            $data['importo'] = 0;

        /*
         * se non ho voci di cassa e importo = 0 esco
         */
        if(empty($cashResults) && $data['importo']==0) {
            if($debug) debug("Non ho voci di cassa e importo = 0 => esco");
            return true;
        }

        if(empty($cashResults))
            $cashResults = $this->newEntity();

        $cash = $this->patchEntity($cashResults, $data);
        if($debug) debug("cash importo after ".$cash->importo);
        if (!$this->save($cash)) {
            debug($cash->getErrors());
            return false;
        }
        else {

            /*
             * la prima volta che inserisco in Cashes non creo CashesHistories
             */
            if($cash_history_save) {
                $id = $cash->id;

               // $cash = $this->Cashes->get($id);

                $data = [];
                $data['id'] = null;
                $data['cash_id'] = $id;
                $data['organization_id'] = $cash->organization_id;
                $data['user_id'] = $cash->user_id;
                $data['nota'] = $cash_nota;
                $data['importo'] = $cash_importo; // importo precedente al salvataggio
                if(!empty($cash_modified))
                    $data['modified'] = $cash_modified;
                
                if($debug) debug($data);
                
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

    /*
     *  calcolare il totale in cassa
    */
    public function getTotaleCash($user, $debug=false) {

        $organization_id = $user->organization->id;

        $where = ['Cashes.organization_id' => $organization_id];
        $results = $this->find()
                        ->fields(['totale_importo' => 'SUM(Cashes.importo)'])
                        ->select(['sum' => 'SUM(Cashes.importo)'])
                        ->where($where)
                        ->first();

        if($debug) debug($results);

        return $results;
    }
    
    /*
     *      calcolare il totale in cassa di un utente
     *      le voci di cassa generiche (user_id=0) possono avere + occorrenze
     *      le voci di cassa degli utenti hanno una sola occorrenza
    */
    public function getTotaleCashToUser($user, $user_id, $debug = false) {
    
        $results = [];

        if(!isset($user->organization))
            return 0;
        
        $organization_id = $user->organization->id;

        $where = ['Cashes.organization_id' => $organization_id,
                  'Cashes.user_id' => $user_id];

        if($user_id!=0) {
            $results = $this->find()
                            ->select(['sum' => 'SUM(Cashes.importo)'])
                            ->where($where)
                            ->first();

            $results = $results['sum'];
        }
        else {
            $results = $this->find()
                            ->select(['Cashes.importo'])
                            ->where($where)
                            ->first();

            $results = $results['importo'];
        }

        if($debug) debug($results);

        return $results;
    }    
}