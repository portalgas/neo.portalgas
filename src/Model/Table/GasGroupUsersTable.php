<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * GasGroupUsers Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\GasGroupsTable&\Cake\ORM\Association\BelongsTo $GasGroups
 *
 * @method \App\Model\Entity\GasGroupUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\GasGroupUser newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GasGroupUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GasGroupUser|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GasGroupUser saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GasGroupUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GasGroupUser[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GasGroupUser findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GasGroupUsersTable extends Table
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

        $this->setTable('gas_group_users');
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
        $this->belongsTo('GasGroups', [
            'foreignKey' => 'gas_group_id',
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
        $rules->add($rules->existsIn(['gas_group_id'], 'GasGroups'));

        return $rules;
    }

    /* 
     * elenco users associati ad un gasGroups 
    */
    public function getUsers($user, $organization_id, $gas_group_id, $where=[]) {

        $results = [];

        $where_gasuser = ['GasGroupUsers.gas_group_id' => $gas_group_id, 
                  'GasGroupUsers.organization_id' => $organization_id, 
                 ];    
         
        // escludo dispensa@gas.portalgas.it	                 
        $where_user = ['Users.block' => 0, 
                        'Users.username NOT LIKE' => 'dispensa@%'];                   
        if(!empty($where))
            $where_user = array_merge($where_user, $where);		 
            
        $users = $this->find()->where($where_gasuser)
                                ->contain(['Users' => [
                                    'conditions' => [$where_user],
                                    'sort' => ['Users.name' => 0]
                                ]])
                                ->all();
        return $users;
    }

    /* 
     * utenti da associare al gruppo
     */
    public function getUsersToAssocitateList($user, $organization_id, $gas_group_id) {

        $results = []; 
         
        $usersTable = TableRegistry::get('Users');
        
        $gas_group_users = $this->getUsers($user, $organization_id, $gas_group_id);
        if($gas_group_users->count()>0) {
            foreach($gas_group_users as $gas_group_user) {
                array_push($results, $gas_group_user->user->id);
            }
            $where = ['Users.id NOT IN ' => $results];
            $results = $usersTable->getList($user, $user->organization->id, $where);
        }
        else {
            $results = $usersTable->getList($user, $user->organization->id);
        }

        return $results;
    }

    /* 
     * utenti gia' associati al gruppo
     */
    public function getUsersAssocitateList($user, $organization_id, $gas_group_id) {

        $results = []; 
                 
        $gas_group_users = $this->getUsers($user, $organization_id, $gas_group_id);
        if($gas_group_users->count()>0) {
            foreach($gas_group_users as $gas_group_user) {
                $results[$gas_group_user->user->id] = $gas_group_user->user->name;
            }
        }
        return $results;
    }
}
