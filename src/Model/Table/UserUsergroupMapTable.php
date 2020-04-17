<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JUserUsergroupMap Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \App\Model\Entity\JUserUsergroupMap get($primaryKey, $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JUserUsergroupMap findOrCreate($search, callable $callback = null, $options = [])
 */
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
        $this->setPrimaryKey(['user_id', 'group_id']);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('UserGroups', [
            'class' => 'UserGroups',
            'foreignKey' => 'group_id',
            'joinType' => 'INNER'
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
}
