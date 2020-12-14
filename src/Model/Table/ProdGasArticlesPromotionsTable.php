<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProdGasArticlesPromotionsTable extends Table
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

        $this->setTable('k_prod_gas_articles_promotions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProdGasPromotions', [
            'foreignKey' => 'prod_gas_promotion_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['organization_id', 'article_id'],
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
            ->integer('qta')
            ->notEmptyString('qta');

        $validator
            ->numeric('prezzo_unita')
            ->notEmptyString('prezzo_unita');

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
        $rules->add($rules->existsIn(['prod_gas_promotion_id'], 'ProdGasPromotions'));
        $rules->add($rules->existsIn(['organization_id', 'article_id'], 'Articles'));

        return $rules;
    }

    public function getByProdGasPromotionId($user, $prod_gas_promotion_id, $debug=false) {

        if (empty($prod_gas_promotion_id)) {
            return null;
        }

        $results = $this->find()  
                        ->where([$this->getAlias().'.id' => $prod_gas_promotion_id])
                        ->contain(['Articles' => ['conditions' => ['Articles.stato' => 'Y']]])
                        ->all();

        return $results;      
    }    
}
