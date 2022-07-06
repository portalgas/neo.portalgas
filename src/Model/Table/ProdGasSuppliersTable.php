<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ProdGasSuppliersTable extends Table
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

        $this->setTable('k_suppliers');
        $this->setAlias('Suppliers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CategoriesSuppliers', [
            'foreignKey' => 'category_supplier_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Organizations', [
            'foreignKey' => 'owner_organization_id',
            'joinType' => 'INNER',
            'conditions' => ['Organizations.type' => 'PRODGAS', 'Organizations.stato' => 'Y']
        ]);

        /* 
         * SuppliersOrganizations legato al produttore
         */
        $this->belongsTo('OwnerSupplierOrganizations', [
            'className' => 'OwnerSupplierOrganizations',
            'joinType' => 'INNER',
            'conditions' => ['OwnerSupplierOrganizations.stato' => 'Y'],
            //'foreignKey' => 'supplier_id'
            'foreignKey' => ['owner_organization_id', 'id'],     // fields Suppliers
            'bindingKey' => ['organization_id', 'supplier_id'],  // fields OwnSuppliersOrganizations
        ]);
        $this->hasMany('SuppliersOrganizations', [
            'foreignKey' => 'supplier_id'
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
        return $rules;
    }

    /*
     * dato un supplier_id estraggo l'eventuale ProdGasSupplier e
     * Organizations.type PRODGAS 
     * SupplierOrganization (Suppliers.owner_organization_id) associato
     */
    public function getBySupplierId($user, $supplier_id, $where = [], $debug = false)
    {

        $where_supplier = [];
        if (isset($where['Suppliers']))
            $where_supplier = $where['Suppliers'];
        $where_supplier = array_merge(['Suppliers.id' => $supplier_id,
            'Suppliers.stato IN ' => ['Y', 'T']],
            $where_supplier);
        if ($debug) debug($where_supplier);

        $supplier = $this->find()
            ->contain(['Organizations' => ['conditions' => ['Organizations.type' => 'PRODGAS', 'Organizations.stato' => 'Y']],
                'OwnerSupplierOrganizations'
            ])
            ->where($where_supplier)
            ->first();

        if ($debug) debug($supplier);

        return $supplier;
    }

    /*
     * dato un Organization PRODGAS estraggo il suo supplier
     *
     * filtersOwnerArticles SUPPLIER / REFERENT / DES non e' valorizzato prendo tutti i SuppliersOrganization.owner_articles => root
     * il prodGas ha filtrato per $SuppliersOrganization.owner_articles = SUPPLIER  
     */
    public function getOrganizationSupplier($user, $organization_id, $filters = [], $debug = false)
    {

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $results = $suppliersOrganizationsTable->find()
            ->where(['SuppliersOrganizations.organization_id' => $organization_id])
            ->contain(['Suppliers'])
            ->first();
        if ($debug) debug($results);

        if (!empty($results)) {

            /*
             * estraggo tutti i GAS che hanno il produttore
             */
            $where = [];
            $where = ['SuppliersOrganizations.supplier_id' => $results->supplier_id,
                'SuppliersOrganizations.organization_id != ' => $organization_id];
            if (isset($filters['ownerArticles']))
                $where += ['SuppliersOrganizations.owner_articles' => $filters['ownerArticles']];

            /*
             * estraggo solo il GAS corrente
             */
            if (isset($filters['organization_id']) && !empty($filters['organization_id'])) { // estraggo solo il GAS corrente
                $where += ['SuppliersOrganizations.organization_id' => $filters['organization_id']];

                $suppliersOrganizationResults = $suppliersOrganizationsTable->find()
                    ->where($where)
                    ->order(['SuppliersOrganizations.name' => 'asc'])
                    ->first();
            } else {
                $suppliersOrganizationResults = $suppliersOrganizationsTable->find()
                    ->where($where)
                    ->order(['SuppliersOrganizations.name' => 'asc'])
                    ->all();
            }
            if ($debug) debug($where);
            if ($debug) debug($suppliersOrganizationResults);

            $results->organizations = $suppliersOrganizationResults;


        } // end if(!empty($results))

        if ($debug) debug($results, $debug);

        return $results;
    }

    /*
     * estrae le consegne con un ordine del produttore
     *  solo se posso ancora modificare gli articoli 
     *   SELECT * FROM `k_templates_orders_states_orders_actions` WHERE `order_action_id` = 6 group by state_code
     *
     * se $article_id filtro gli ordini in cui c'e' l'articolo passato (ProdGasArticles::add)
     */
    public function getDeliveriesWhitOrders($user, $organization_id, $article_id = 0, $debug = false)
    {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.organization_id' => $organization_id,
            'SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id']];
        $options['field'] = ['SuppliersOrganization.id'];
        $options['recursive'] = -1;
        $suppliersOrganizationResults = $SuppliersOrganization->find('first', $options);

        App::import('Model', 'Order');
        $Order = new Order;

        $Order->unbindModel(array('belongsTo' => array('SuppliersOrganization',)));
        $options = [];
        $options['conditions'] = ['Delivery.organization_id' => $organization_id,
            'Delivery.isVisibleBackOffice' => 'Y',
            'Delivery.stato_elaborazione' => 'OPEN',
            'Order.organization_id' => $organization_id,
            'Order.supplier_organization_id' => $suppliersOrganizationResults['SuppliersOrganization']['id'],
            'Order.state_code' => ['OPEN', 'OPEN-NEXT', 'RI-OPEN-VALIDATE', 'PROCESSED-BEFORE-DELIVERY', 'PROCESSED-POST-DELIVERY', 'INCOMING-ORDER']];
        $options['order'] = array('Delivery.data asc, Delivery.id, Order.data_inizio asc');
        $options['recursive'] = 1;
        $results = $Order->find('all', $options);

        if (!empty($article_id) && !empty($results)) {
            /*
             * per ogni ordine ctrl se c'e' l'articolo del produttore
             */

            App::import('Model', 'ArticlesOrder');
            $ArticlesOrder = new ArticlesOrder;
            $ArticlesOrder->unbindModel(array('belongsTo' => array('Order', 'Cart')));

            $newResults = [];
            foreach ($results as $result) {

                $options = [];
                $options['conditions'] = ['ArticlesOrder.organization_id' => $organization_id,
                    'ArticlesOrder.order_id' => $result['Order']['id'],
                    'Article.article_id' => $article_id];
                $options['recursive'] = 1;
                $articlesOrderResults = $ArticlesOrder->find('all', $options);
                if (!empty($articlesOrderResults))
                    $newResults[] = $result;
            }

            $results = $newResults;

        } // loop Order
        /*
        echo "<pre>";
        print_r($results);
        echo "</pre>";
        */
        return $results;
    }

    /*
     * dato un produttore estraggo il suppliers_organization del GAS
     */
    public function getSuppliersOrganization($user, $organization_id, $debug = false)
    {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.organization_id' => $organization_id,
            'SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
            'SuppliersOrganization.stato' => 'Y'];
        $options['recursive'] = -1;
        $results = $SuppliersOrganization->find('first', $options);

        if ($debug) {
            echo "<pre>ProdGasSupplier::getSuppliersOrganization \n";
            print_r($options['conditions']);
            print_r($results);
            echo "</pre>";
        }

        return $results;
    }

    /*
     * estrae il SINGOLO GAS di un produttore
     * con $prod_gas_promotion_id estraggo il GAS inseriti nella promozione (ProdGasPromotionsOrganization)
     */
    public function getOrganizationAssociate($user, $organization_id, $prod_gas_promotion_id = 0, $debug = false)
    {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $SuppliersOrganization->unbindModel(array('belongsTo' => array('Supplier', 'CategoriesSupplier')));
        $SuppliersOrganization->unbindModel(array('hasMany' => array('Article', 'Order', 'SuppliersOrganizationsReferent')));

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.organization_id' => $organization_id,
            'SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
            'SuppliersOrganization.stato' => 'Y'];
        $options['order'] = ['SuppliersOrganization.name'];
        $options['recursive'] = 1;
        $results = $SuppliersOrganization->find('first', $options);

        if ($debug) {
            echo "<pre>";
            print_r($options);
            print_r($results);
            echo "</pre>";
        }

        /* 
         * ProdGasPromotionsOrganization per spese trasporto, costi aggiuntivi + Order
         */
        if ($prod_gas_promotion_id > 0 && !empty($results)) {
            App::import('Model', 'ProdGasPromotionsOrganization');


            $ProdGasPromotionsOrganization = new ProdGasPromotionsOrganization;

            $options = [];
            $options['conditions'] = ['ProdGasPromotionsOrganization.prod_gas_promotion_id' => $prod_gas_promotion_id,
                'ProdGasPromotionsOrganization.organization_id' => $result['SuppliersOrganization']['organization_id'] // e' quello del gas
            ];
            $options['recursive'] = -1;
            $prodGasPromotionsOrganizationResults = $ProdGasPromotionsOrganization->find('first', $options);

            if ($debug) {
                echo "<pre>ProdGasPromotion->getProdGasPromotion() dati del GAS \n";
                print_r($prodGasPromotionsOrganizationResults);
                echo "</pre>";
            }

            if (!empty($prodGasPromotionsOrganizationResults))
                $results['ProdGasPromotionsOrganization'] = $prodGasPromotionsOrganizationResults['ProdGasPromotionsOrganization'];
        }

        if ($debug) {
            echo "<pre>";
            print_r($results);
            echo "</pre>";
        }

        return $results;
    }

    /*
     * estrae i gas con la gestione degli articoli al produttore
     */
    public function getOrganizationsArticlesSupplierList($user, $debug = false)
    {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;


        $SuppliersOrganization->unbindModel(array('belongsTo' => array('Supplier', 'CategoriesSupplier')));
        $SuppliersOrganization->unbindModel(array('hasMany' => array('Article', 'Order', 'SuppliersOrganizationsReferent')));

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
            'SuppliersOrganization.stato' => 'Y',
            'SuppliersOrganization.owner_articles' => 'SUPPLIER'];
        $options['order'] = ['SuppliersOrganization.name'];
        $options['recursive'] = 1;
        $results = $SuppliersOrganization->find('all', $options);

        $newResults = [];
        foreach ($results as $result) {
            $newResults[$result['Organization']['id']] = $result['Organization']['name'];
        }

        return $newResults;
    }

    /*
     * estrae tutti i GAS di un produttore
     * con $prod_gas_promotion_id estraggo i GAS inseriti nella promozione (ProdGasPromotionsOrganization)
     */
    public function getOrganizationsAssociate($user, $prod_gas_promotion_id = 0, $debug = false)
    {

        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $SuppliersOrganization->unbindModel(array('belongsTo' => array('Supplier', 'CategoriesSupplier')));
        $SuppliersOrganization->unbindModel(array('hasMany' => array('Article', 'Order', 'SuppliersOrganizationsReferent')));

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
            'SuppliersOrganization.organization_id !=' => $user->organization['Organization']['id'], // escludo se stesso
            'SuppliersOrganization.stato' => 'Y'];
        $options['order'] = ['SuppliersOrganization.name'];
        $options['recursive'] = 1;
        $results = $SuppliersOrganization->find('all', $options);
        if ($debug) debug($results);

        /* 
         * ProdGasPromotionsOrganization per spese trasporto, costi aggiuntivi + Order
         */
        if ($prod_gas_promotion_id > 0 && !empty($results)) {
            App::import('Model', 'ProdGasPromotionsOrganization');

            foreach ($results as $numResult => $result) {
                $ProdGasPromotionsOrganization = new ProdGasPromotionsOrganization;

                $options = [];
                $options['conditions'] = ['ProdGasPromotionsOrganization.prod_gas_promotion_id' => $prod_gas_promotion_id,
                    'ProdGasPromotionsOrganization.organization_id' => $result['SuppliersOrganization']['organization_id']  // e' quello del gas
                ];
                $options['recursive'] = -1;
                $prodGasPromotionsOrganizationResults = $ProdGasPromotionsOrganization->find('first', $options);

                if ($debug) {
                    echo "<br /> Tratto " . $result['SuppliersOrganization']['name'] . ' (' . $result['SuppliersOrganization']['id'] . ') per il GAS ' . $result['SuppliersOrganization']['organization_id'];
                    echo "<pre>ProdGasPromotion->getProdGasPromotion() dati del GAS \n";
                    print_r($options['conditions']);
                    print_r($prodGasPromotionsOrganizationResults);
                    echo "</pre>";
                }

                if (!empty($prodGasPromotionsOrganizationResults))
                    $results[$numResult]['ProdGasPromotionsOrganization'] = $prodGasPromotionsOrganizationResults['ProdGasPromotionsOrganization'];
            }


        }

        if ($debug) debug($results);

        return $results;
    }

    /*
     * per i GAS associati e SuppliersOrganization.can_promotions = Y estraggo le eventuali consegne valide 
     */
    public function getOrganizationsAssociateWithDeliveries($user, $prod_gas_promotion_id = 0, $debug = false)
    {

        $results = $this->getOrganizationsAssociate($user, $prod_gas_promotion_id, $debug);
        if (!empty($results)) {

            App::import('Model', 'Delivery');

            App::import('Model', 'ProdGasPromotionsOrganizationsDelivery');

            foreach ($results as $numResult => $result) {
                if ($result['SuppliersOrganization']['can_promotions'] == 'Y') {

                    $Delivery = new Delivery;

                    $options['conditions'] = ['Delivery.organization_id' => (int)$result['Organization']['id'],
                        'Delivery.isVisibleBackOffice' => 'Y',
                        'Delivery.sys' => 'N',
                        'Delivery.stato_elaborazione' => 'OPEN',
                        'DATE(Delivery.data) >= CURDATE()'];
                    $options['fields'] = ['Delivery.id', 'Delivery.luogoData'];
                    $options['order'] = ['Delivery.data ASC'];
                    $options['recursive'] = -1;

                    $deliveryResults = $Delivery->find('list', $options);
                    if ($debug) {
                        echo "<pre>";
                        print_r($options);
                        print_r($deliveryResults);
                        echo "</pre>";
                    }
                    if (!empty($deliveryResults)) {
                        $results[$numResult]['Delivery'] = $deliveryResults;
                    }

                    /*
                     * associazione Gas + delivery
                     */
                    if (!empty($prod_gas_promotion_id)) {
                        $ProdGasPromotionsOrganizationsDelivery = new ProdGasPromotionsOrganizationsDelivery();
                        $options = [];
                        $options['conditions'] = ['ProdGasPromotionsOrganizationsDelivery.prod_gas_promotion_id' => $prod_gas_promotion_id,
                            'ProdGasPromotionsOrganizationsDelivery.organization_id' => $result['Organization']['id'] // e' quello del gas
                        ];
                        $options['recursive'] = -1;
                        $prodGasPromotionsOrganizationsDeliveryResults = $ProdGasPromotionsOrganizationsDelivery->find('all', $options);
                        if ($debug) {
                            echo "<pre>";
                            print_r($options);
                            print_r($prodGasPromotionsOrganizationsDeliveryResults);
                            echo "</pre>";
                        }
                        if (!empty($prodGasPromotionsOrganizationsDeliveryResults)) {
                            $results[$numResult]['ProdGasPromotionsOrganizationsDelivery'] = $prodGasPromotionsOrganizationsDeliveryResults;
                        }
                    }

                } // end if($result['SuppliersOrganization']['can_promotions']=='Y')
            }
        }

        /*
        echo "<pre>ProdGasSuplier::getOrganizationsAssociateWithDeliveries \n ";
        print_r($results);
        echo "</pre>";
        */

        return $results;

    }

    /*
     * estrae tutti i GAS non assocati di un produttore
     */
    public function getOrganizationsNotAssociate($user, $debug = false)
    {

        $organization_ids = '';

        /*
         * estraggo i organization_id dei GAS associati
         */
        App::import('Model', 'SuppliersOrganization');
        $SuppliersOrganization = new SuppliersOrganization;

        $options = [];
        $options['conditions'] = ['SuppliersOrganization.supplier_id' => $user->organization['Supplier']['Supplier']['id'],
            'SuppliersOrganization.organization_id !=' => $user->organization['Organization']['id'], // escludo se stesso
            'SuppliersOrganization.stato' => 'Y'];
        $options['fields'] = ['SuppliersOrganization.organization_id'];
        $options['order'] = ['SuppliersOrganization.id'];
        $options['recursive'] = -1;
        $results = $SuppliersOrganization->find('all', $options);

        /*
         * ids da escludere
         */

        if (!empty($results)) {
            foreach ($results as $result) {
                $organization_id = $result['SuppliersOrganization']['organization_id'];
                $organization_ids .= $organization_id . ',';
            }

            if (!empty($organization_ids)) {
                $organization_ids = substr($organization_ids, 0, strlen($organization_ids) - 1);

                /*
                 * estraggo i GAS non associati
                 */
                App::import('Model', 'Organization');
                $Organization = new Organization;

                $options = [];
                $options['conditions'] = ['Organization.type' => 'GAS',
                    'Organization.stato' => 'Y'];
                if (!empty($organization_ids))
                    $options['conditions'] += ['NOT' => ['Organization.id' => explode(',', $organization_ids)]];
                $options['order'] = ['Organization.name'];
                $options['recursive'] = -1;
                $results = $Organization->find('all', $options);

            } // if(!empty($organization_ids))

        } // end if(!empty($results))

        if ($debug) debug($results);

        return $results;
    }

    /* 
     * ottengo i dati del GAS, x es per sapere se Organization.hasDes
     */
    public function getUserOrganization($organization_id)
    {

        App::import('Model', 'Organization');
        $Organization = new Organization();

        $options = [];
        $options['conditions'] = ['Organization.stato' => 'Y',
            'Organization.id' => $organization_id];
        $options['recursive'] = -1;
        $organizationResults = $Organization->find('first', $options);
        if (empty($organizationResults)) {
            $this->Session->setFlash(__('msg_error_params'));
            $this->myRedirect(Configure::read('routes_msg_exclamation'));
        }

        $paramsConfig = json_decode($organizationResults['Organization']['paramsConfig'], true);
        $paramsFields = json_decode($organizationResults['Organization']['paramsFields'], true);

        $organizationResults['Organization'] += $paramsConfig;
        $organizationResults['Organization'] += $paramsFields;

        unset($organizationResults['Organization']['paramsConfig']);
        unset($organizationResults['Organization']['paramsFields']);

        $userOrganization = new \stdClass();
        $userOrganization->organization = $organizationResults;

        return $userOrganization;
    }

    /*
     * ctrl se il produttore scelto ha associato l'organizzazione Socialmarket
     * Configure::write('social_market_organization_id', 142);
     */
    public function hasOrganizationSocialmarket($user, $organization_id, $debug = false)
    {
        $filters['organization_id'] = Configure::read('social_market_organization_id');
        $supplier = $this->getOrganizationSupplier($user, $organization_id, $filters);

        if (!empty($supplier) && isset($supplier->organizations) && !empty($supplier->organizations) &&
            $supplier->organizations->organization_id==Configure::read('social_market_organization_id'))
                return true;
        else
            return false;
    }
}