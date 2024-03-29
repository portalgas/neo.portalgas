<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UserUsergroupMapTable extends Table
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

        $this->setTable('j_user_usergroup_map');
        $this->setDisplayField('user_id');
        $this->setPrimaryKey(['user_id', 'group_id', 'gas_group_id']);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('UserGroups', [
            'className' => 'App\Model\Table\UsergroupsTable',
            'foreignKey' => 'group_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('GasGroups', [
            'foreignKey' => 'gas_group_id',
            'joinType' => 'LEFT'
        ]);        
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['group_id'], 'UserGroups'));

        return $rules;
    }

    public function getUsersByGroups($user, $organization_id, $groups=[], $where=[])
    {
        $where_user = ['Users.organization_id' => $organization_id,
                       'Users.block' => 0];
        $where_group = ['UserUsergroupMap.group_id IN' => $groups];

        if(isset($where['Users']))
            $where_user = array_merge($where_user, $where['Users']);
        if(isset($where['UserUsergroupMap']))
            $where_group = array_merge($where_group, $where['UserUsergroupMap']);
                   
        $results = $this->find()
                        ->contain(['Users' => ['conditions' => $where_user]])
                        ->where($where_group)
                        ->all();
        return $results;
    }    
}
