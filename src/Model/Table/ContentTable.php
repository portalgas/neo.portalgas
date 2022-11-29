<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * JContent Model
 *
 * @property \App\Model\Table\AssetsTable&\Cake\ORM\Association\BelongsTo $Assets
 * @property \App\Model\Table\KSuppliersTable&\Cake\ORM\Association\HasMany $KSuppliers
 *
 * @method \App\Model\Entity\JContent get($primaryKey, $options = [])
 * @method \App\Model\Entity\JContent newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JContent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JContent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JContent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JContent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JContent[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JContent findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContentTable extends Table
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

        $this->setTable('j_content');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Assets', [
            'foreignKey' => 'asset_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Suppliers', [
            'foreignKey' => 'j_content_id',
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->notEmptyString('title');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 255)
            ->notEmptyString('alias');

        $validator
            ->scalar('title_alias')
            ->maxLength('title_alias', 255)
            ->notEmptyString('title_alias');

        $validator
            ->scalar('introtext')
            ->maxLength('introtext', 16777215)
            ->requirePresence('introtext', 'create')
            ->notEmptyString('introtext');

        $validator
            ->scalar('fulltext')
            ->maxLength('fulltext', 16777215)
            ->requirePresence('fulltext', 'create')
            ->notEmptyString('fulltext');

        $validator
            ->notEmptyString('state');

        $validator
            ->nonNegativeInteger('sectionid')
            ->notEmptyString('sectionid');

        $validator
            ->nonNegativeInteger('mask')
            ->notEmptyString('mask');

        $validator
            ->nonNegativeInteger('catid')
            ->notEmptyString('catid');

        $validator
            ->nonNegativeInteger('created_by')
            ->notEmptyString('created_by');

        $validator
            ->scalar('created_by_alias')
            ->maxLength('created_by_alias', 255)
            ->notEmptyString('created_by_alias');

        $validator
            ->nonNegativeInteger('modified_by')
            ->notEmptyString('modified_by');

        $validator
            ->nonNegativeInteger('checked_out')
            ->notEmptyString('checked_out');

        $validator
            ->dateTime('checked_out_time')
            ->notEmptyDateTime('checked_out_time');

        $validator
            ->dateTime('publish_up')
            ->notEmptyDateTime('publish_up');

        $validator
            ->dateTime('publish_down')
            ->notEmptyDateTime('publish_down');

        $validator
            ->scalar('images')
            ->requirePresence('images', 'create')
            ->notEmptyFile('images');

        $validator
            ->scalar('urls')
            ->requirePresence('urls', 'create')
            ->notEmptyString('urls');

        $validator
            ->scalar('attribs')
            ->maxLength('attribs', 5120)
            ->requirePresence('attribs', 'create')
            ->notEmptyString('attribs');

        $validator
            ->nonNegativeInteger('version')
            ->notEmptyString('version');

        $validator
            ->nonNegativeInteger('parentid')
            ->notEmptyString('parentid');

        $validator
            ->integer('ordering')
            ->notEmptyString('ordering');

        $validator
            ->scalar('metakey')
            ->requirePresence('metakey', 'create')
            ->notEmptyString('metakey');

        $validator
            ->scalar('metadesc')
            ->requirePresence('metadesc', 'create')
            ->notEmptyString('metadesc');

        $validator
            ->nonNegativeInteger('access')
            ->notEmptyString('access');

        $validator
            ->nonNegativeInteger('hits')
            ->notEmptyString('hits');

        $validator
            ->scalar('metadata')
            ->requirePresence('metadata', 'create')
            ->notEmptyString('metadata');

        $validator
            ->notEmptyString('featured');

        $validator
            ->scalar('language')
            ->maxLength('language', 7)
            ->requirePresence('language', 'create')
            ->notEmptyString('language');

        $validator
            ->scalar('xreference')
            ->maxLength('xreference', 50)
            ->requirePresence('xreference', 'create')
            ->notEmptyString('xreference');

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
        $rules->add($rules->existsIn(['asset_id'], 'Assets'));

        return $rules;
    }
}
