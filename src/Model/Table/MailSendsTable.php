<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MailSends Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 *
 * @method \App\Model\Entity\MailSend get($primaryKey, $options = [])
 * @method \App\Model\Entity\MailSend newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MailSend[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MailSend|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MailSend saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MailSend patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MailSend[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MailSend findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MailSendsTable extends Table
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

        $this->setTable('mail_sends');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
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
            ->nonNegativeInteger('tot_users')
            ->requirePresence('tot_users', 'create')
            ->notEmptyString('tot_users');

        $validator
            ->scalar('file_sh')
            ->maxLength('file_sh', 100)
            ->requirePresence('file_sh', 'create')
            ->notEmptyFile('file_sh');

        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        $validator
            ->scalar('cron')
            ->maxLength('cron', 100)
            ->requirePresence('cron', 'create')
            ->notEmptyString('cron');

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
        /*
         * se non trovo user al quale inviare mail organization_id = 0
         * $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        */

        return $rules;
    }
}
