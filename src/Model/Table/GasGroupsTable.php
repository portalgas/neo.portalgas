<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * GasGroups Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property &\Cake\ORM\Association\HasMany $GasGroupDeliveries
 * @property \App\Model\Table\GasGroupUsersTable&\Cake\ORM\Association\HasMany $GasGroupUsers
 *
 * @method \App\Model\Entity\GasGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\GasGroup newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GasGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GasGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GasGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GasGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GasGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GasGroup findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GasGroupsTable extends Table
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

        $this->setTable('gas_groups');
        $this->setDisplayField('name');
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
        $this->hasMany('GasGroupDeliveries', [
            'foreignKey' => 'gas_group_id',
        ]);
        $this->hasMany('GasGroupUsers', [
            'foreignKey' => 'gas_group_id',
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
            ->scalar('name')
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

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

        return $rules;
    }

    /* 
     * utenti da associare al gruppo
     */
    public function findMyLists($user, $organization_id, $user_id) {

        $results = []; 
         
        $where = ['user_id' => $user_id, 
                  'organization_id' => $organization_id, 
                    ];
        $results = $this->find('list', ['conditions' => $where, 'order' => ['name']]);
        return $results;
    }  
    
	public function getsById($user, $organization_id, $gas_group_id) {

        $options = [];
        $options['conditions'] = ['GasGroup.organization_id' => $organization_id,
                                'GasGroup.id' => $gas_group_id];
        $options['recursive'] = -1;

        $results = $this->find('first', $options);
  
      return $results;		
  }

  public function getsByUser($user, $organization_id, $user_id) {

      if($user->organization->paramsConfig['hasGasGroups']=='N')
        return [];

      $where = ['GasGroups.organization_id' => $organization_id,
                'GasGroups.user_id' => $user_id];

      $results = $this->find()->where($where)->all();
  
      return $results;		
  }

  public function getsByIdsUser($user, $organization_id, $user_id) {

      if($user->organization->paramsConfig['hasGasGroups']=='N')
        return [];

      $ids = [];

      $results = $this->getsByUser($user, $organization_id, $user_id);
      if(!empty($results))
      foreach($results as $result) {
          $ids[$result->id] = $result->id;
      }
  
      return $ids;		
  }  
}
