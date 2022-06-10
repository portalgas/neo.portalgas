<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class CashesUsersTable extends Table
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

        $this->setTable('k_cashes_users');
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
            ->numeric('limit_after')
            ->notEmptyString('limit_after');

        $validator
            ->scalar('limit_type')
            ->allowEmptyString('limit_type');

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

    /*
     * somma di quanto un gasista ha acquistato e non ancora saldato
     * - tutti gli acquisti di ordini non associati a summary_orders (ordini aperti, prima della consegna etc)
     * - tutti gli acquisti di ordini associati a summary_orders con saldato_a IS NULL (non ancora saldato)
     *
     * escludo gli acquisti effettuati da produttori in supplier_organization_cash_excludeds
     */
    public function getTotImportoAcquistato($user, $organization_id, $user_id, $debug=false) {

        $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
        
        $stateCodeUsersCash = $lifeCycleOrdersTable->getStateCodeUsersCash($user);
        $stateCodeUsersCash = "'".implode("','", $stateCodeUsersCash)."'";
        
        $tot_importo = '0.00';
        $zero = floatval(0);
        
        /*
         * escludo gli acquisti effettuati da produttori in supplier_organization_cash_excludeds
         */
        $supplierOrganizationCashExcludedResults = $this->getSupplierOrganizationCashExcludedIds($user, $organization_id, $debug);
        $sql_supplier_organization_cash_excluded = '';
        if(!empty($supplierOrganizationCashExcludedResults)) {
            foreach($supplierOrganizationCashExcludedResults as $supplierOrganizationCashExcludedResult) {
                $sql_supplier_organization_cash_excluded .= $supplierOrganizationCashExcludedResult.',';
            }
            if(!empty($sql_supplier_organization_cash_excluded))
                $sql_supplier_organization_cash_excluded = substr($sql_supplier_organization_cash_excluded, 0, (strlen($sql_supplier_organization_cash_excluded)-1));

                $sql_supplier_organization_cash_excluded = ' AND `Order`.supplier_organization_id NOT IN ('.$sql_supplier_organization_cash_excluded.')';
        } // end if(!empty($supplierOrganizationCashExcludedResults))
        if($debug) debug($sql_supplier_organization_cash_excluded);

        $sql = "SELECT `Order`.id, ArticlesOrder.prezzo, Cart.qta_forzato, Cart.qta, Cart.importo_forzato
                FROM
                    ".Configure::read('DB.prefix')."articles_orders as ArticlesOrder, ".Configure::read('DB.prefix')."orders as `Order`,
                    ".Configure::read('DB.prefix')."carts as Cart
                     LEFT JOIN ".Configure::read('DB.prefix')."summary_orders as SummaryOrder ON 
                    (SummaryOrder.organization_id = $organization_id and SummaryOrder.user_id = Cart.user_id and SummaryOrder.order_id = Cart.order_id and SummaryOrder.saldato_a is null)
                WHERE
                    ArticlesOrder.organization_id = $organization_id
                    and `Order`.organization_id = $organization_id
                    and Cart.organization_id = $organization_id
                    and Cart.user_id = $user_id
                    and Cart.order_id = `Order`.id  
                    and Cart.article_organization_id = ArticlesOrder.article_organization_id
                    and Cart.article_id = ArticlesOrder.article_id  
                    and ArticlesOrder.order_id = `Order`.id  
                    and Cart.deleteToReferent = 'N' 
                    and `Order`.isVisibleBackOffice = 'Y'
                    and `Order`.state_code not in ($stateCodeUsersCash)";
        if(!empty($sql_supplier_organization_cash_excluded))
            $sql .= $sql_supplier_organization_cash_excluded;
        if($debug) debug($sql); 
        $conn = ConnectionManager::get('default');
        $results = $conn->execute($sql);

        /*
         * memorizzo tutti gli order_id per calcolare eventuali costi di trasporto / costi aggiuntivi / sconti
         */
        $order_ids = [];
        foreach($results as $numResult => $result) {
            
            $order_ids[$result['id']] = $result['id'];

            // debug($result); 

            $prezzo = floatval($result['prezzo']);
            $qta_forzato = floatval($result['qta_forzato']);
            
            if($qta_forzato > $zero) {
                $qta = $qta_forzato;
            }
            else {
                $qta = floatval($result['qta']);
            }

            $importo_forzato = floatval($result['importo_forzato']);
                
            if($importo_forzato==$zero) {
                if($qta_forzato>$zero) 
                    $importo = ($qta_forzato * $prezzo);
                else {
                    $importo = (floatval($result['qta']) * $prezzo);
                }
            }
            else {
                $importo = $importo_forzato;
            }
            
            $tot_importo = ($tot_importo + $importo);
            if($debug) debug('CashesUser::getTotImportoAcquistato - tot_importo '.$tot_importo);
        } // end foreach($results as $numResult => $result)

        // debug($order_ids);
        if(!empty($order_ids)) {
            foreach($order_ids as $order_id) {
                
                $importo_trasport = 0;
                $importo_cost_less = 0;
                $importo_cost_more = 0;

                $summaryOrderTrasportsTable = TableRegistry::get('SummaryOrderTrasports');

                $summaryOrderTrasportResults = $summaryOrderTrasportsTable->getByUserByOrder($user, $organization_id, $user_id, $order_id);
                if(!empty($summaryOrderTrasportResults)) 
                    $importo_trasport = $summaryOrderTrasportResults['importo_trasport'];

                $summaryOrderCostLessesTable = TableRegistry::get('SummaryOrderCostLesses');

                $summaryOrderCostLessResults = $summaryOrderCostLessesTable->getByUserByOrder($user, $organization_id, $user_id, $order_id);
                if(!empty($summaryOrderCostLessResults))
                    $importo_cost_less = $summaryOrderCostLessResults['importo_cost_less'];

                $summaryOrderCostMoresTable = TableRegistry::get('SummaryOrderCostMores');

                $summaryOrderCostMoreResults = $summaryOrderCostMoresTable->getByUserByOrder($user, $organization_id, $user_id, $order_id);
                if(!empty($summaryOrderCostMoreResults))
                    $importo_cost_more = $summaryOrderCostMoreResults['importo_cost_more'];

                $tot_importo += ($importo_trasport + $importo_cost_less + $importo_cost_more);
            }

        } // if(!empty($order_ids))
                
        if($debug) debug('CashesUser::getTotImportoAcquistato - RESULTS '.$tot_importo);

        return floatval($tot_importo);
    }

    /* 
     * estrae i dati caricati alla login dell'utente in AppController
     */
    public function getUserData($user) {
        
        $results = [];
        
        /*
         * configurazione Organization
         */
        $results['organization_cash_limit'] = $user->organization_cash_limit;
        $results['organization_cash_limit_label'] = $user->organization_cash_limit_label;
        $results['organization_limit_cash_after'] = floatval($user->organization_limit_cash_after);
        $results['organization_limit_cash_after_'] = $user->organization_limit_cash_after_;
        $results['organization_limit_cash_after_e'] = $user->organization_limit_cash_after_e;

    
        /*
         * configurazione CashesUser
         */                 
        $results['user_limit_type'] = $user->user_limit_type;
        $results['user_limit_type_label'] = __('FE-'.$user->user_limit_type);
        $results['user_limit_after'] = floatval($user->user_limit_after);
        $results['user_limit_after_'] = $user->user_limit_after_;
        $results['user_limit_after_e'] = $user->user_limit_after_e;
            
        /*
         * totale cassa
         */
        $results['user_cash'] = floatval($user->user_cash);
        $results['user_cash_'] = $user->user_cash_;
        $results['user_cash_e'] = $user->user_cash_e;
          
        return $results;
    }

    /*
     * elenco ids produttori esclusi dalla gestione della cassa dell'utente
     */ 
    public function getSupplierOrganizationCashExcludedIds($user, $organization_id, $debug=false) {

        $results = [];

        if(!isset($user->organization->paramsConfig) || $user->organization->paramsConfig['hasCashFilterSupplier']=='N')
            return $results;

        $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');

        $where = ['SupplierOrganizationCashExcludeds.organization_id' => $organization_id];
        $supplierOrganizationCashExcludedResults = $supplierOrganizationCashExcludedsTable->find()
                                                ->where($where)
                                                ->all();

        if($supplierOrganizationCashExcludedResults->count() > 0) {
            foreach($supplierOrganizationCashExcludedResults as $supplierOrganizationCashExcludedResult) {
                $results[] = $supplierOrganizationCashExcludedResult->supplier_organization_id;
            }
        }

        if($debug) debug($results);
        return $results;
    } 

    /*
     * ctrl se il produttore dell'ordine ha la gestione della cassa dell'utente
     */ 
    public function isSupplierOrganizationCashExcluded($user, $organization_id, $supplier_organization_id, $debug=false) {

        if($user->organization->paramsConfig['hasCashFilterSupplier']=='N')
            return false;

        $supplierOrganizationCashExcludedsTable = TableRegistry::get('SupplierOrganizationCashExcludeds');

        $where = ['SupplierOrganizationCashExcludeds.supplier_organization_id' => $supplier_organization_id,
                  'SupplierOrganizationCashExcludeds.organization_id' => $organization_id];
        $supplierOrganizationCashExcluded = $supplierOrganizationCashExcludedsTable->find()
                                                ->where($where)
                                                ->first();

        if(empty($supplierOrganizationCashExcluded)) 
            return false;
        else
            return true;
    } 

    /* 
     * dato un acquisto ctrl se lo user puo' acquistarlo
     */
    public function ctrlLimitCart($user, $organization_id, $supplier_organization_id, $qta_prima_modifica, $qta, $prezzo, $debug=false) {

       if($this->isSupplierOrganizationCashExcluded($user, $organization_id, $supplier_organization_id, $debug))
            return true;

            $this->isSupplierOrganizationCashExcluded($user, $organization_id, $supplier_organization_id, $debug);

        $results = [];  
        $results = $this->getUserData($user);

        if($debug) debug('organization_cash_limit '.$results['organization_cash_limit']);
        
        if($results['organization_cash_limit']=='LIMIT-NO')
            return true;
            
        if($results['organization_cash_limit']=='LIMIT-CASH-USER' && $results['user_limit_type']=='LIMIT-NO')
            return true;
            
        /*
         * quanto e' stato acquistato
         * devo calcolare come qta solo da differenza, se avava acquistato 2 e aumenta a 4 => 4 - 2 
         */
        $zero = floatval(0);
        $qta = floatval($qta);
        $qta_prima_modifica = floatval($qta_prima_modifica);
        if($qta > $qta_prima_modifica)
            $qta_diff = ($qta - $qta_prima_modifica);
        else 
            $qta_diff = ($qta);
    
        $prezzo = floatval($prezzo);
        $cart_importo = ($qta_diff * $prezzo);
                        
         /*
          * totale importo acquisti
          */
        $results['user_tot_importo_acquistato'] = $this->getTotImportoAcquistato($user, $organization_id, $user->id /*, $debug */);
        
        if($debug) {
            debug('user_cash '.$results['user_cash']);
            debug('user_tot_importo_acquistato '.$results['user_tot_importo_acquistato']);
            debug('cart_importo (qta_diff * prezzo) '.' ('.$qta_diff.' * '.$prezzo.') '.$cart_importo);
        }
                
        if($results['organization_cash_limit']=='LIMIT-CASH') {
            if(($results['user_cash'] - ($results['user_tot_importo_acquistato'] + $cart_importo)) < 0 )
                return false;
        }
            
        if($results['organization_cash_limit']=='LIMIT-CASH-AFTER') {
            if((($results['user_cash'] + $results['organization_limit_cash_after'] ) - ($results['user_tot_importo_acquistato'] + $cart_importo)) < 0 )
                return false;
            else            
                return true;
        }

        if($results['organization_cash_limit']=='LIMIT-CASH-USER') {
        
            if($debug) debug('user_limit_type '.$results['user_limit_type']);
        
            if($results['user_limit_type']=='LIMIT-CASH') {
            
                $delta = ($results['user_cash'] - ($results['user_tot_importo_acquistato'] + $cart_importo));
                
                if($debug) debug('ctrlLimitCart => delta '.$delta);

                if($delta < $zero)
                    return false;
                else            
                    return true;
            }
            
            if($results['user_limit_type']=='LIMIT-CASH-AFTER') {

                if($debug) debug('user_limit_after '.$results['user_limit_after']);

                $delta = (($results['user_cash'] + $results['user_limit_after'] ) - ($results['user_tot_importo_acquistato'] + $cart_importo));
                
                if($debug) debug('delta '.$delta);

                if($delta < $zero) {
                    if($debug) debug('FALSE delta < zero');
                    return false;   
                }   
                else {
                    if($debug) debug('TRUE delta > zero');      
                    return true;
                }       
            }
            
            $delta = ($results['user_cash'] - ($results['user_tot_importo_acquistato'] + $cart_importo)); 
            if($delta < $zero)
                return false;
        }       

        if($debug) debug('ctrlLimitCart OK');

        return true;        
    }

    public function ctrlLimit($user, $organization_cashLimit, $organization_limitCashAfter=0, $cashesUser, $tot_importo_cash=0, $tot_importo_acquistato=0, $debug=false) {
        
        $organization_id = $user->organization->id;
        $results = [];
        
        if($debug) {
            debug("organization_cashLimit ".$organization_cashLimit);
            debug("organization_limitCashAfter ".$organization_limitCashAfter);
            debug("tot_importo_cash ".$tot_importo_cash);
            debug("tot_importo_acquistato ".$tot_importo_acquistato);
            debug("cashesUser");
            debug($cashesUser);
        }

        $results['organization_id'] = $organization_id;

         /*
          * totale importo acquisti
          */
        $user_tot_importo_acquistato = $this->getTotImportoAcquistato($user, $organization_id, $user->id);
        $user_tot_importo_acquistato_ = number_format($user_tot_importo_acquistato ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
        if($user_tot_importo_acquistato==0)
            $results['fe_msg_tot_acquisti'] = 'Non hai ancora effettuato acquisti';
        else
            $results['fe_msg_tot_acquisti'] = 'Hai acquistato per '.$user_tot_importo_acquistato_.'&nbsp;&euro;';

        // 
        switch($organization_cashLimit) {
            case "LIMIT-NO":
                $results['importo'] = 0; // (floatval($tot_importo_cash) - floatval($tot_importo_acquistato));
                $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                $results['stato'] = 'GREEN';
                $results['fe_msg'] = 'Nessun limite per gli acquisti';
                $results['fe_msg_tot_acquisti'] = '';

                $results['has_fido'] = false;
            break;
            case "LIMIT-CASH":
                $results['importo'] = (floatval($tot_importo_cash) - floatval($tot_importo_acquistato));
                $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                if($results['importo']<0) {
                    $results['stato'] = 'RED';
                    $results['fe_msg'] = 'Hai esaurito il credito di cassa! ('.$results['importo_e'].')';
                }
                else
                if($results['importo']>0) {
                    $results['stato'] = 'GREEN';
                    $results['fe_msg'] = 'Puoi fare acquisti per '.$results['importo_e']; 
                }
                else
                if($results['importo']==0) {
                    $results['stato'] = 'YELLOW';
                    $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa!'; 
                }

                $results['has_fido'] = false;
            break;
            case "LIMIT-CASH-AFTER":
                $results['importo'] = (floatval($tot_importo_cash) - floatval($tot_importo_acquistato) + floatval($organization_limitCashAfter));
                $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                if($results['importo']<0) {
                    $results['stato'] = 'RED';
                    $results['fe_msg'] = 'Hai esaurito il credito di cassa! ('.$results['importo_e'].')';
                }
                else
                if($results['importo']>0) {
                    $results['stato'] = 'GREEN';
                    $results['fe_msg'] = 'Puoi fare acquisti per '.$results['importo_e']; 
                }
                else
                if($results['importo']==0) {
                    $results['stato'] = 'YELLOW';
                    $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa!';
                } 

                $results['has_fido'] = true;
                $results['importo_fido'] = $organization_limitCashAfter;
                $results['importo_fido_'] = number_format($organization_limitCashAfter ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $results['importo_fido_e'] = number_format($organization_limitCashAfter,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')) .'&nbsp;&euro;';             
            break;
            case "LIMIT-CASH-USER":
            
                /*
                 * puo' essere se vuoto se si e' scelto "Limite per ogni gasista" ma poi non ho salvato
                 */
                if(empty($cashesUser))
                    $cashesUser['limit_type'] = 'LIMIT-NO';
                    
                /*
                 * singolo User
                 */
                switch($cashesUser['limit_type']) {
                    case "LIMIT-NO":
                        $results['importo'] = 0; // (floatval($tot_importo_cash) - floatval($tot_importo_acquistato));
                        $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                        $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                        $results['stato'] = 'GREEN';
                        $results['fe_msg'] = 'Nessun limite per gli acquisti';
                        $results['fe_msg_tot_acquisti'] = '';

                        $results['has_fido'] = false;
                    break;
                    case "LIMIT-CASH":
                        $results['importo'] = (floatval($tot_importo_cash) - floatval($tot_importo_acquistato));
                        $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                        $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                        if($results['importo']<0) {
                            $results['stato'] = 'RED';
                        $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa! ('.$results['importo_e'].')';
                        }
                        else
                        if($results['importo']>0) {
                            $results['stato'] = 'GREEN';
                            $results['fe_msg'] = 'Puoi fare acquisti per '.$results['importo_e']; 
                        }
                        else
                        if($results['importo']==0) {
                            $results['stato'] = 'YELLOW';
                            $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa!';
                        }

                        $results['has_fido'] = false;
                    break;
                    case "LIMIT-CASH-AFTER":
                        $results['importo'] = (floatval($tot_importo_cash) - floatval($tot_importo_acquistato) + floatval($cashesUser['limit_after']));
                        $results['importo_'] = number_format($results['importo'] ,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                        $results['importo_e']  = $results['importo_'] .'&nbsp;&euro;'; 

                        if($results['importo']<0) {
                            $results['stato'] = 'RED';
                            $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa! ('.$results['importo_e'].')';
                        }
                        else
                        if($results['importo']>0) {
                            $results['stato'] = 'GREEN';
                            $results['fe_msg'] = 'Puoi fare acquisti per '.$results['importo_e']; 
                        }
                        else
                        if($results['importo']==0) {
                            $results['stato'] = 'YELLOW'; 
                            $results['fe_msg'] = 'Hai esaurito il tuo credito in cassa!'; 
                        }       
           
                        $results['has_fido'] = true;
                        $results['importo_fido'] = $cashesUser['limit_after'];
                        $results['importo_fido_'] = $cashesUser['limit_after_'];
                        $results['importo_fido_e'] = $cashesUser['limit_after_e']; 
                    break;
                }                
            break;
        }

        if($debug) debug($results);
          
        /*
         * custom btns per gas
         */     
        switch($organization_id) {
            case 15: // ivrea
                unset($results['has_fido']);
                unset($results['importo_fido']);
                unset($results['importo_fido_']);
                unset($results['importo_fido_e']);
                unset($results['fe_msg']); // puoi fare acquisti
            break;
        } 

        return $results;
    }         
}