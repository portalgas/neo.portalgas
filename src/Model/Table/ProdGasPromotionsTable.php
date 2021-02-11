<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ProdGasPromotionsTable extends Table
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

        $this->setTable('k_prod_gas_promotions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ProdGasArticlesPromotions', [
            'foreignKey' => 'prod_gas_promotion_id'
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
            ->scalar('name')
            ->maxLength('name', 150)
            ->allowEmptyString('name');

        $validator
            ->scalar('img1')
            ->maxLength('img1', 50)
            ->allowEmptyString('img1');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->requirePresence('data_fine', 'create')
            ->notEmptyDate('data_fine');

        $validator
            ->numeric('importo_originale')
            ->notEmptyString('importo_originale');

        $validator
            ->numeric('importo_scontato')
            ->notEmptyString('importo_scontato');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('contact_name')
            ->maxLength('contact_name', 150)
            ->requirePresence('contact_name', 'create')
            ->notEmptyString('contact_name');

        $validator
            ->scalar('contact_mail')
            ->maxLength('contact_mail', 100)
            ->requirePresence('contact_mail', 'create')
            ->notEmptyString('contact_mail');

        $validator
            ->scalar('contact_phone')
            ->maxLength('contact_phone', 100)
            ->requirePresence('contact_phone', 'create')
            ->notEmptyString('contact_phone');

        $validator
            ->scalar('state_code')
            ->maxLength('state_code', 50)
            ->requirePresence('state_code', 'create')
            ->notEmptyString('state_code');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

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

        return $rules;
    }

    /*
     * ctrl se puo' gestire le promozioni
     *  Supplier.can_promotions - come produttore
     *  SuppliersOrganizaton.can_promotions - per il dato gas
     */ 
    public function canPromotions($user, $organization_id=0, $debug=false) {
    
        $canPromotions = false;
        
        /*
         * ctrl a livello di produttore 
         *
        App::import('Model', 'Supplier');
        $Supplier = new Supplier;
        
        $options = [];
        $options['conditions'] = ['Supplier.id' => $user->organization['Supplier']['Supplier']['id']];
        $options['fields'] = ['Supplier.can_promotions'];
        $options['recursive'] = -1;
        $supplierResults = $Supplier->find('first', $options);
        if($debug) debug($options);
        if($debug) debug($supplierResults);
        if(empty($supplierResults))
            return false;
         */
            
        if($user->organization['hasPromotionGas']=='N' && $user->organization['hasPromotionGasUsers']=='N')
            return false;

        if($debug) debug($user->organization['Supplier']);
        if($user->organization['Supplier']['Supplier']['can_promotions']!='Y') 
            return false;
                    
        /*
         * ctrl a livello di GAS 
         */
         if(!empty($organization_id)) {
            App::import('Model', 'SuppliersOrganization');
            $SuppliersOrganization = new SuppliersOrganization;
            
            $options = [];
            $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'], 
                                      'SuppliersOrganization.organization_id' => $organization_id];
            $options['fields'] = ['SuppliersOrganization.can_promotions'];
            $options['recursive'] = -1;
            $suppliersOrganizationResults = $SuppliersOrganization->find('first', $options);
            if($debug) debug($options);
            if($debug) debug($suppliersOrganizationResults);         
            if(empty($suppliersOrganizationResults))
                return false;
                
            if($suppliersOrganizationResults['SuppliersOrganization']['can_promotions']=='N')
                return false;        
         }      

         return true;
    }
    
    /*
     * estrae tutti i dati di una promozione
     * ProdGasPromotion / Supplier 
     *
     * organization_id filtra per la promozione per il GAS passato
     */
    public function getProdGasPromotion($user, $prod_gas_promotion_id, $organization_id=0, $debug=false) {
    
       // $debug=true;
        $results = [];
        
        /*
         * dati promozione 
         */
        $results = $this->get($prod_gas_promotion_id);        
        if($debug) debug($results);
    
        /*
         * dati produttore 
         */
        $prodGasSuppliersTable = TableRegistry::get('ProdGasSuppliers');        
        $filters['organization_id'] = $organization_id;
        $supplierResults = $prodGasSuppliersTable->getOrganizationSupplier($user, $results->organization_id, $filters);
        if($debug) debug($supplierResults); 

        $results->suppliersOrganization = $supplierResults;
            
        /* 
         * ProdGasPromotionsOrganization per spese trasporto, costi aggiuntivi + Order
         */
        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');
    
        $where = [];
        $where = ['ProdGasPromotionsOrganizations.prod_gas_promotion_id' => $prod_gas_promotion_id];                           
        if(!empty($organization_id)) {
            $where += ['ProdGasPromotionsOrganizations.organization_id' => $organization_id];

            $prodGasPromotionsOrganizationResults = $prodGasPromotionsOrganizationsTable->find() 
                                                                                        ->where($where)
                                                                                        ->first(); 
            
            $results->prodGasPromotionsOrganizations = $prodGasPromotionsOrganizationResults;
        }
        else {
            $prodGasPromotionsOrganizationResults = $prodGasPromotionsOrganizationsTable->find() 
                                                                                        ->where($where)
                                                                                        ->all(); 

            $results->prodGasPromotionsOrganizations = $prodGasPromotionsOrganizationResults;
        }
        
        if($debug) debug($prodGasPromotionsOrganizationResults);    

        /* 
         * articoli i promozione
         */
        $prodGasArticlesPromotionsTable = TableRegistry::get('ProdGasArticlesPromotions');

        $prodGasArticlesPromotionResults = $prodGasArticlesPromotionsTable->getByProdGasPromotionId($user, $results->organization_id, $prod_gas_promotion_id);
        if($debug) debug($prodGasArticlesPromotionResults);     
     
        $results->prodGasArticlesPromotions = $prodGasArticlesPromotionResults;
         
        return $results;
    }
         
    /*
     * estrae tutti i GAS di un produttore
     * con $prod_gas_promotion_id estraggo i GAS inseriti nella promozione (ProdGasPromotionsOrganization)
     */
    public function getOrganizationsAssociate($user, $prod_gas_promotion_id=0, $debug=false) {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;
        
        $SuppliersOrganization->unbindModel(['belongsTo' => ['Supplier', 'CategoriesSupplier']]);
        $SuppliersOrganization->unbindModel(['hasMany' => ['Article', 'Order', 'SuppliersOrganizationsReferent']]);
        
        $options = [];
        $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
                                  'SuppliersOrganization.stato' => 'Y'];
        $options['order'] = ['SuppliersOrganization.name'];
        $options['recursive'] = 1;
        $results = $SuppliersOrganization->find('all', $options);
        
        /* 
         * ProdGasPromotionsOrganization per spese trasporto, costi aggiuntivi + Order
         */
        if($prod_gas_promotion_id>0) {
            App::import('Model', 'ProdGasPromotionsOrganization');

            foreach($results as $numResult => $result) {
                $ProdGasPromotionsOrganization = new ProdGasPromotionsOrganization;

                $options = [];
                $options['conditions'] = ['ProdGasPromotionsOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
                                          'ProdGasPromotionsOrganization.prod_gas_promotion_id' => $prod_gas_promotion_id,
                                          'ProdGasPromotionsOrganization.organization_id' => $result['SuppliersOrganization']['organization_id']];
                $options['recursive'] = -1;
                $prodGasPromotionsOrganizationResults = $ProdGasPromotionsOrganization->find('first', $options);

                if($debug) debug($prodGasPromotionsOrganizationResults); 
                
                if(!empty($prodGasPromotionsOrganizationResults)) 
                    $results[$numResult]['ProdGasPromotionsOrganization'] = $prodGasPromotionsOrganizationResults['ProdGasPromotionsOrganization'];
            }           


        }
        
        if($debug) debug($results);  
        
        return $results;
    }
    
    /*
     * estrae tutti i GAS non assocati di un produttore
     */
    public function getOrganizationsNotAssociate($user, $debug=false) {
        
        $organization_ids = '';
        
        /*
         * estraggo i organization_id dei GAS associati
         */
        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;
        
        $options = [];
        $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
                                   'SuppliersOrganization.stato' => 'Y'];
        $options['fields'] = ['SuppliersOrganization.organization_id'];
        $options['order'] = ['SuppliersOrganization.id'];
        $options['recursive'] = -1;
        $results = $SuppliersOrganization->find('all', $options);
        
        /*
         * ids da escludere
         */
        
        if(!empty($results)) {
            foreach($results as $result) {
                $organization_id = $result['SuppliersOrganization']['organization_id'];
                $organization_ids .= $organization_id.',';
            }
            
            if(!empty($organization_ids)) {
                $organization_ids = substr($organization_ids, 0, strlen($organization_ids)-1);

                /*
                 * estraggo i GAS non associati
                 */
                App::import('Model', 'Organization');
                $Organization = new Organization;
                
                $options = [];
                $options['conditions'] = ['Organization.id NOT IN ("'.$organization_ids .'")',
                                          'Organization.type' => 'GAS',
                                          'Organization.stato' => 'Y'];
                $options['order'] = ['Organization.name'];
                $options['recursive'] = -1;
                $results = $Organization->find('all', $options);
                
            } // if(!empty($organization_ids))
            
        } // end if(!empty($results))
        
        if($debug) debug($results);                  
        
        return $results;    
    }    
}
