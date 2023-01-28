<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\Log\Log;
use Cake\Datasource\ConnectionManager;
/**
 * KStorerooms Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\DeliveriesTable&\Cake\ORM\Association\BelongsTo $Deliveries
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 * @property \App\Model\Table\ArticleOrganizationsTable&\Cake\ORM\Association\BelongsTo $ArticleOrganizations
 *
 * @method \App\Model\Entity\KStoreroom get($primaryKey, $options = [])
 * @method \App\Model\Entity\KStoreroom newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\KStoreroom[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KStoreroom|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KStoreroom saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KStoreroom patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KStoreroom[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\KStoreroom findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StoreroomsTable extends Table
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

        $this->setTable('k_storerooms');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Deliveries', [
            'foreignKey' => ['organization_id', 'delivery_id'],
            // 'joinType' => 'INNER',  // se delivery_id = 0 perche' non ancora associato a gasista
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Articles', [
            'foreignKey' => ['article_organization_id', 'article_id'],
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArticleOrganizations', [
            'className' => 'Organizations',
            'foreignKey' => 'article_organization_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('qta')
            ->requirePresence('qta', 'create')
            ->notEmptyString('qta');

        $validator
            ->numeric('prezzo')
            ->requirePresence('prezzo', 'create')
            ->notEmptyString('prezzo');

        $validator
            ->scalar('stato')
            ->requirePresence('stato', 'create')
            ->notEmptyString('stato');

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
        $rules->add($rules->existsIn(['organization_id', 'delivery_id'], 'Deliveries'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['organization_id', 'article_id'], 'Articles'));
        $rules->add($rules->existsIn(['article_organization_id'], 'ArticleOrganizations'));

        return $rules;
    }

    public function gets($user, $organization_id, $where=[], $debug=false) {

        $results = [];
        $where_delivery = [];
        $where_user = [];
        $where_article = [];

        if(isset($where['Deliveries']))
            $where_delivery = $where['Deliveries'];
        $where_delivery = array_merge(['Deliveries.organization_id' => $organization_id],
                              $where_delivery);
        if($debug) debug($where_delivery); 

        if(isset($where['Users']))
            $where_user = $where['Users'];
        $where_user = array_merge(['Users.organization_id' => $organization_id], $where_user);                  
        if($debug) debug($where_user);

        if(isset($where['Articles']))
            $where_article = $where['Articles'];
        $where_article = array_merge(['Articles.organization_id' => $organization_id], $where_article);                  
        if($debug) debug($where_article);

        $where = ['Storerooms.organization_id' => $organization_id];

        $results = $this->find()
                                ->where($where)
                                ->contain([
                                  'Deliveries' => ['conditions' => $where_delivery, 'joinType' => 'INNER'],
                                  'Users' => ['conditions' => $where_user],
                                  'Articles' => ['conditions' => $where_article]  
                                ])
                                ->order(['Articles.name'])
                                ->all();
        // debug($results);
       
        return $results;
    } 
    
	/*
	 * ottieni lo user che gestisce la dispensa
	 * dev'essere solo 1
	 * */
	public function getStoreroomUser($user) {

		$storeroomUser = [];
		
		if($user->organization->paramsConfig['hasStoreroom']=='Y') {
			
			$sql = "SELECT User.organization_id, User.id, User.name, User.username, User.email 
					FROM
						".Configure::read('DB.portalPrefix')."user_usergroup_map m,
						".Configure::read('DB.portalPrefix')."usergroups g,
						".Configure::read('DB.portalPrefix')."users User 
					WHERE
						m.user_id = User.id
						and m.group_id = g.id
						and m.group_id = ".Configure::read('group_id_storeroom')."
						and User.block = 0
						and User.organization_id = ".(int)$user->organization->id." LIMIT 0,1";
			try {
                $connection = ConnectionManager::get('default');
                $storeroomUser = $connection->execute($sql)->fetch('assoc');
			}
			catch (Exception $e) {
				Log::error($sql);
				Log::error($e);
			}
		}
        
		return $storeroomUser;		
	}

	/*
	 * ottengo tutti gli acquisti della dispensa in un ordine
	 */
	public function getCartsToStoreroom($user, $order_id, $debug=false) {
		
		$results = [];
		
		$storeroomUser = $this->getStoreroomUser($user);
		if(!empty($storeroomUser)) {

            $cartsTable = TableRegistry::get('Carts');
			
			$where = ['Carts.user_id' => $storeroomUser['id'],
                    'Carts.order_id' => $order_id,
                    'Carts.organization_id' => $user->organization->id,
                    'ArticlesOrders.stato != ' => 'N',
                    'Articles.stato' => 'Y'];

			$results = $cartsTable->find()
                ->contain(['ArticlesOrders', 'Articles'])
                ->where($where)
                ->all();
		}
				
		return $results;
	}    
}
