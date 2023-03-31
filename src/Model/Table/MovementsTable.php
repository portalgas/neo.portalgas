<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use App\Traits;

/**
 * Movements Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\MovementTypesTable&\Cake\ORM\Association\BelongsTo $MovementTypes
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SupplierOrganizationsTable&\Cake\ORM\Association\BelongsTo $SupplierOrganizations
 *
 * @method \App\Model\Entity\Movement get($primaryKey, $options = [])
 * @method \App\Model\Entity\Movement newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Movement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Movement|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Movement saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Movement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Movement[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Movement findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MovementsTable extends Table
{
    const PAYMENT_TYPE_CONTANTI = 'Contanti';
    const PAYMENT_TYPE_BONIFICO = 'Bonifico';
    const PAYMENT_TYPE_SATISPAY = 'Satispay';
    const PAYMENT_TYPE_CASSA = 'Cassa';
    const PAYMENT_TYPE_ALTRO = 'Altro';

    use Traits\SqlTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('movements');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'payment_type' => [
                'strategy' => 'const',
                'prefix' => 'PAYMENT_TYPE'
            ],
        ]]);

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MovementTypes', [
            'foreignKey' => 'movement_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('SuppliersOrganizations', [
            'foreignKey' => ['organization_id', 'supplier_organization_id'],
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
            ->nonNegativeInteger('year')
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->scalar('name')
            ->maxLength('name', 75)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->numeric('importo')
            ->notEmptyString('importo');

        $validator
            // ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date');

        $validator
            ->scalar('payment_type')
            ->allowEmptyString('payment_type');

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
        $rules->add($rules->existsIn(['movement_type_id'], 'MovementTypes'));
        // $rules->add($rules->existsIn(['user_id'], 'Users'));
        // $rules->add($rules->existsIn(['supplier_organization_id'], 'SupplierOrganizations'));

        return $rules;
    }

    public function getYears($user, $organization_id) {
        $where = ['organization_id' => $organization_id];
        $min = $this->getMin($this, 'year', $where);        
        if($min==0 || $min==date('Y'))
            $years[date('Y')] = date('Y');
        else {
            $years = [];
            for($min; $min<=date('Y'); $min++) {
                $years[$min] = $min;
            }
        }
        return $years;
    }

    /* 
     * $datas= request daform add / edit 
     * */
    public function decorateMovementType($datas) {

        switch($datas['movement_type_id']) {
            case '1': // Spesa del G.A.S.
            case '2': // Entrata del G.A.S.
            case '5': // Pagamento fattura a fornitore
                $datas['user_id'] = null;
                $datas['supplier_organization_id'] = null;
            break;
            case '3': // Sconto al fornitore
            case '4': // Accredito dal fornitore
                $datas['user_id'] = null;
            break;
            case '6': // Rimborso Gasista
            case '7': // Movimento di cassa
                $datas['supplier_organization_id'] = null;
            break;
        }

        return $datas;
    }
}
