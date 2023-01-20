<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoopsOrders Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\LoopsDeliveriesTable&\Cake\ORM\Association\BelongsTo $LoopsDeliveries
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LoopsOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoopsOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoopsOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoopsOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoopsOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoopsOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoopsOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoopsOrder findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoopsOrdersTable extends Table
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

        $this->setTable('loops_orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('LoopsDeliveries', [
            'foreignKey' => 'loops_delivery_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => ['organization_id', 'order_id'],
            'joinType' => 'LEFT', // la prima volta non e' valorizzata
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
            ->requirePresence('supplier_organization_id')
            ->notEmptyString('supplier_organization_id'); 

        $validator
            ->nonNegativeInteger('gg_data_inizio')
            ->requirePresence('gg_data_inizio', 'create')
            ->notEmptyString('gg_data_inizio');

        $validator
            ->nonNegativeInteger('gg_data_fine')
            ->requirePresence('gg_data_fine', 'create')
            ->notEmptyString('gg_data_fine')
            ->add('gg_data_fine', [
                'ctrlGG' => [
                    'rule' => function ($value, $context) {
                        $gg_data_inizio = $context['data']['gg_data_inizio'];
                        $gg_data_fine = $value; 
              
                        if($gg_data_fine>$gg_data_inizio)
                            return false;
                        else
                            return true;                         
                    },
                    'message' => "I giorni precedenti alla chiusura non possono essere inferiori ai giorni precedenti all'apertura"
                ]
            ]);            
                    
        $validator
            ->scalar('flag_send_mail')
            ->allowEmptyString('flag_send_mail');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

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
        $rules->add($rules->existsIn(['loops_delivery_id'], 'LoopsDeliveries'));
        $rules->add($rules->existsIn(['organization_id', 'supplier_organization_id'], 'SuppliersOrganizations'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }   
}
