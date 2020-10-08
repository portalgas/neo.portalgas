<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CartsTable extends Table
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

        $this->setTable('k_carts');
        $this->setDisplayField('organization_id');
        $this->setPrimaryKey(['organization_id', 'user_id', 'order_id', 'article_organization_id', 'article_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ArticlesOrders', [
            'foreignKey' => ['organization_id', 'article_organization_id', 'article_id', 'order_id'],
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER'
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
            ->integer('qta')
            ->notEmptyString('qta');

        $validator
            ->scalar('deleteToReferent')
            ->notEmptyString('deleteToReferent');

        $validator
            ->integer('qta_forzato')
            ->notEmptyString('qta_forzato');

        $validator
            ->numeric('importo_forzato')
            ->greaterThanOrEqual('importo_forzato', 0)
            ->notEmptyString('importo_forzato');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->scalar('inStoreroom')
            ->notEmptyString('inStoreroom');

        $validator
            ->scalar('stato')
            ->notEmptyString('stato');

        $validator
            ->dateTime('date')
            ->notEmptyDateTime('date');

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
        $rules->add($rules->existsIn(['organization_id', 'order_id'], 'Orders'));
        $rules->add($rules->existsIn(['article_organization_id', 'article_id'], 'Articles'));
        $rules->add($rules->existsIn(['organization_id', 'article_organization_id', 'article_id', 'order_id'], 'ArticlesOrders'));

        return $rules;
    }

    /*
     * $where = ['Carts.order_id' => $order_id];
     * $where = ['Carts.user_id' => $user_id];
     * $where = ['Orders.delivery_id' => $delivery_id];
     */    
    public function getTotImporto($user, $where=[], $options=[], $debug=false) {

        $importo_totale = 0;

        if(!empty($where)) 
        foreach ($where as $key => $value) {
            $where += [$key => $value];
        }
        $where += ['Carts.organization_id' => $user->organization->id];
            
        if($debug) debug($where);

        $cartsResults = $this->find()
                            ->select(['Carts.qta', 'Carts.qta_forzato', 'Carts.importo_forzato', 'ArticlesOrders.prezzo'])
                            ->where($where) 
                            ->contain(['Orders', 'ArticlesOrders'])
                            ->all();
        if(!empty($cartsResults) && $cartsResults->count()>0) {
            foreach($cartsResults as $cartsResult) {

                // if($debug) debug($cartsResult);

                /*
                 * importo
                 */
                if($cartsResult->importo_forzato==0) {
                    if($cartsResult->qta_forzato>0)
                        $importo = ($cartsResult->qta_forzato * $cartsResult->articles_order->prezzo);
                    else
                        $importo = ($cartsResult->qta * $cartsResult->articles_order->prezzo);
                }
                else
                    $importo = $cartsResult->importo_forzato;
                
                $importo_totale += $importo;                
            }
        }

        if($debug) debug($importo_totale);

        return $importo_totale;
    }

    public function getByIds($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $debug=false) {
                            
        $where = ['Carts.organization_id' => $organization_id,
                  'Carts.order_id' => $order_id,
                  'Carts.user_id' => $user_id,
                  'Carts.article_organization_id' => $article_organization_id,
                  'Carts.article_id' => $article_id];
        // debug($where);

        $results = $this->find()
                        ->contain([
                            'Articles' => ['conditions' => ['Articles.stato' => 'Y']], 
                            'ArticlesOrders' => ['conditions' => ['ArticlesOrders.stato != ' => 'N']]
                        ])
                        ->where($where)
                        ->first();

        return $results;
    }

    /*
     * front-end - estrae gli articoli associati ad un ordine filtrati per user  
     *  ArticlesOrders.article_id              = Articles.id
     *  ArticlesOrders.article_organization_id = Articles.organization_id
     */
    public function getByOrder($user, $organization_id, $order_id, $user_id, $where=[], $order=[], $debug=false) {
                            
        $where = array_merge(['Carts.organization_id' => $organization_id,
                              'Carts.order_id' => $order_id,
                              'Carts.user_id' => $user_id,
                              'Carts.deleteToReferent' => 'N'], 
                              $where);
        // debug($where);
        $order = ['ArticlesOrders.name'];

        $results = $this->find()
                        ->contain([
                            'Articles' => ['conditions' => ['Articles.stato' => 'Y']], 
                            'ArticlesOrders' => ['conditions' => ['ArticlesOrders.stato != ' => 'N']]
                        ])
                        ->where($where)
                        ->order($order)
                        ->all();
   
        return $results;
    }    
}
