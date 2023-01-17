<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Des Model
 *
 * @method \App\Model\Entity\KDe get($primaryKey, $options = [])
 * @method \App\Model\Entity\KDe newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KDe[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KDe|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KDe saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KDe patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KDe[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KDe findOrCreate($search, callable $callback = null, $options = [])
 */
class DesTable extends Table
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

        $this->setTable('k_des');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('DesOrganizations', [
            'foreignKey' => ['des_id'],
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
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
