<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class ArticlesOrdersTable extends Table
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

        $this->setTable('k_articles_orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'article_organization_id', 'article_id', 'order_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => 'article_organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['article_organization_id', 'article_id'],
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
            ->integer('qta_cart')
            ->notEmptyString('qta_cart');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->numeric('prezzo')
            ->notEmptyString('prezzo');

        $validator
            ->integer('pezzi_confezione')
            ->requirePresence('pezzi_confezione', 'create')
            ->notEmptyString('pezzi_confezione');

        $validator
            ->integer('qta_minima')
            ->requirePresence('qta_minima', 'create')
            ->notEmptyString('qta_minima');

        $validator
            ->integer('qta_massima')
            ->requirePresence('qta_massima', 'create')
            ->notEmptyString('qta_massima');

        $validator
            ->integer('qta_minima_order')
            ->notEmptyString('qta_minima_order');

        $validator
            ->integer('qta_massima_order')
            ->requirePresence('qta_massima_order', 'create')
            ->notEmptyString('qta_massima_order');

        $validator
            ->integer('qta_multipli')
            ->requirePresence('qta_multipli', 'create')
            ->notEmptyString('qta_multipli');

        $validator
            ->integer('alert_to_qta')
            ->requirePresence('alert_to_qta', 'create')
            ->notEmptyString('alert_to_qta');

        $validator
            ->scalar('send_mail')
            ->notEmptyString('send_mail');

        $validator
            ->scalar('flag_bookmarks')
            ->notEmptyString('flag_bookmarks');

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
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['article_organization_id'], 'ArticleOrganizations'));
        $rules->add($rules->existsIn(['article_id'], 'Articles'));

        return $rules;
    }

    public function factory($user, $organization_id, $order_id) {

        $table_registry = '';

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                  'Orders.id' => $order_id];
        $results = $ordersTable->find()
                                ->where($where)
                                ->first();
        // debug($where);
        // debug($results);
        if(empty($results))
            return false;
        
        $order_type_id = $results->order_type_id;

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');  

        switch (strtoupper($order_type_id)) {
            case Configure::read('Order.type.des-titolare'):
            case Configure::read('Order.type.des'):
                $table_registry = 'ArticlesOrdersDes';
                break;
            case Configure::read('Order.type.gas'):
                $table_registry = 'ArticlesOrdersGas';
                break;
            case Configure::read('Order.type.promotion'):
                $table_registry = 'ArticlesOrdersPromotion';
                break;
            case Configure::read('Order.type.pact-pre'):
                $table_registry = 'ArticlesOrdersPactPre';
                break;
            case Configure::read('Order.type.pact'):
                $table_registry = 'ArticlesOrdersPact';
                break;
            
            default:
                die('OrdersTable order_type_id ['.$order_type_id.'] non previsto');
                break;
        }

        return TableRegistry::get($table_registry);
    }      
}
