<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * SocialmarketCarts Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\UserOrganizationsTable&\Cake\ORM\Association\BelongsTo $UserOrganizations
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\SocialmarketCart get($primaryKey, $options = [])
 * @method \App\Model\Entity\SocialmarketCart newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SocialmarketCart[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SocialmarketCart|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SocialmarketCart saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SocialmarketCart patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SocialmarketCart[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SocialmarketCart findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SocialmarketCartsTable extends Table
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

        $this->setTable('socialmarket_carts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        /*
         * e' sempre Configure::write('social_market_organization_id', 142);
         */
        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('UserOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => 'user_organization_id',
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
            ->scalar('article_name')
            ->maxLength('article_name', 255)
            ->requirePresence('article_name', 'create')
            ->notEmptyString('article_name');

        $validator
            ->numeric('article_prezzo')
            ->greaterThanOrEqual('article_prezzo', 0)
            ->notEmptyString('article_prezzo');

        $validator
            ->nonNegativeInteger('cart_qta')
            ->notEmptyString('cart_qta');

        $validator
            ->numeric('cart_importo_finale')
            ->greaterThanOrEqual('cart_importo_finale', 0)
            ->notEmptyString('cart_importo_finale');

        $validator
            ->scalar('nota')
            ->allowEmptyString('nota');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->existsIn(['user_organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }

    /*
     * estrae l'ordine di default aperto su un GAS, ogni GAS per il produttore ne ha solo 1
     */
    public function getOrder($user, $organization_id, $own_organization_id) {
        $ordersTable = TableRegistry::get('Orders');

        $results = $ordersTable->find()
            ->where(['Orders.organization_id' => $organization_id,
                     'Orders.owner_organization_id' => $own_organization_id])
            ->contain(['OrderStateCodes', 'Deliveries'])
            ->first();

        return $results;
    }
}
