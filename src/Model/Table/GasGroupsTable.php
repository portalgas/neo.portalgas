<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

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
        $this->belongsTo('Users', [ // chi l'ha creato
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

	public function getsById($user, $organization_id, $gas_group_id) {

        $results = $this->find()
                        ->where(['GasGroups.organization_id' => $organization_id,
                                 'GasGroups.id' => $gas_group_id,
                                 'GasGroups.is_active' => true])
                        ->first();
  
        return $results;		
    } 

    /* 
     * utenti da associare al gruppo
     * GasGroups.user_id e' chi ha creato il gruppo, ma se filtro per user_id altri referenti non potranno vederlo
     * filtro 
     *      a GasGroupUsers.user_id quindi chi gestisce il gruppo deve parteciparvi
     *      GasGroups.user_id (chi ha creato il gruppo) se no appena creato non lo vedo non avendo utenti associati
     */
    public function findMy($user, $organization_id, $user_id) {

        $results = []; 
        if($user->organization->paramsConfig['hasGasGroups']=='N')
            return $results;

        /*
         * gruppi creati dall'utente (GasGroups.user_id)
         * se no appena creato non lo vedo non avendo utenti associati
         */
        $gas_groups_1 = $this->find()->contain([
                                'GasGroupDeliveries', 
                                'Users', // chi l'ha creato
                                'GasGroupUsers' => [
                                    'conditions' => [
                                        'GasGroupUsers.organization_id' => $organization_id]]])
                            ->where(['GasGroups.organization_id' => $organization_id,
                                    'GasGroups.user_id' => $user_id,
                                    'GasGroups.is_active' => true])
                            ->order(['GasGroups.name'])
                            ->all();
        if($gas_groups_1->count()>0)
        foreach($gas_groups_1 as $gas_group) {
            $results[$gas_group->id] = $gas_group;
            if(!empty($gas_group->gas_group_users)) 
                $results[$gas_group->id]['gas_group_users'] = $gas_group->gas_group_users;
        }

        /* 
         * gruppi al quale l'utente appartiene
         */
        $gas_groups_2 = $this->find()->contain([
                            'GasGroupDeliveries', 
                            'Users', // chi l'ha creato
                            'GasGroupUsers' => [
                                'conditions' => [
                                    'GasGroupUsers.user_id' => $user_id,
                                    'GasGroupUsers.organization_id' => $organization_id]]])
                        ->where(['GasGroups.organization_id' => $organization_id])
                        ->order(['GasGroups.name'])
                        ->all();

        // debug('user_id '.$user_id);                                       
        if($gas_groups_2->count()>0)
        foreach($gas_groups_2 as $gas_group) {
            // debug($gas_group->gas_group_users);
            if(isset($results[$gas_group->id]))
                continue;
                
            /*
             * escludo i gruppi dove l'utente non e' associato
             */
            if(!empty($gas_group->gas_group_users)) {
                /* 
                 * per ogni gruppo estraggo gli utenti associati
                 */
                $where = ['GasGroupUsers.gas_group_id' => $gas_group->id,
                          'GasGroupUsers.organization_id' => $gas_group->organization_id];
                $gasGroupUsersTable = TableRegistry::get('GasGroupUsers');
                $gas_group_users = $gasGroupUsersTable->find()->where($where)->all();

                $results[$gas_group->id] = $gas_group;
                $results[$gas_group->id]['gas_group_users'] = $gas_group_users;
            }
        }

        return $results;
    }

    /* 
     * utenti da associare al gruppo
     * GasGroups.user_id e' chi ha creato il gruppo, ma se filtro per user_id altri referenti non vederlo
     * filtro a GasGroupUsers.user_id quindi chi gestisce il gruppo deve parteciparvi
     * 
     *  $where += ['Orders.gas_group_id IN ' => array_keys($gasGroups)];
     */
    public function findMyLists($user, $organization_id, $user_id) {

        $results = []; 
        if(!isset($user->organization->paramsConfig['hasGasGroups']) || $user->organization->paramsConfig['hasGasGroups']=='N')
            return $results;

        $gas_groups = $this->findMy($user, $organization_id, $user_id);
        foreach($gas_groups as $gas_group) {
            $results[$gas_group->id] = $gas_group->name;
        }
      
        return $results;
    }    
}
