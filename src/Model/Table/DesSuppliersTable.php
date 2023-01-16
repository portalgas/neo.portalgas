<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use \App\Model\Entity\SuppliersOrganization;
/**
 * DesSuppliers Model
 *
 * @property \App\Model\Table\DesTable&\Cake\ORM\Association\BelongsTo $Des
 * @property \App\Model\Table\SuppliersTable&\Cake\ORM\Association\BelongsTo $Suppliers
 * @property \App\Model\Table\OwnOrganizationsTable&\Cake\ORM\Association\BelongsTo $OwnOrganizations
 *
 * @method \App\Model\Entity\DesSupplier get($primaryKey, $options = [])
 * @method \App\Model\Entity\DesSupplier newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DesSupplier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DesSupplier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesSupplier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DesSupplier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DesSupplier[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DesSupplier findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DesSuppliersTable extends Table
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

        $this->setTable('k_des_suppliers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Des', [
            'foreignKey' => 'des_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OwnOrganizations', [
            'foreignKey' => 'own_organization_id',
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
        $rules->add($rules->existsIn(['des_id'], 'Des'));
        $rules->add($rules->existsIn(['supplier_id'], 'Suppliers'));
        $rules->add($rules->existsIn(['own_organization_id'], 'OwnOrganizations'));

        return $rules;
    }

    /*
     * in base ad un SuppliersOrganization 
     * ricava i ruoli DES dello user 
     *
     * produttore
     * owner_articles				  SUPPLIER / REFERENT / DES / PACT
     * owner_organization_id          organization_id di chi gestisce il listino
     * owner_supplier_organization_id supplier_organization_id di chi gestisce il listino
     */
    public function getDesACL($user, SuppliersOrganization $suppliers_organization, $debug=false) {
        
        // $debug = true;

        $results = [];
        $results['isDes'] = false;

        if($user->organization->paramsConfig['hasDes']=='N')
            return false;

        /*
        * ctrl se potrebbe essere un ordine DES
        */
        $results['isOwnGasTitolareDes'] = false;
        $results['isTitolareDes'] = false;
        $results['isReferenteDes'] = false;
        $results['isSuperReferenteDes'] = false;
                
        $des_id = $user->des_id;
        $supplier_id = $suppliers_organization->supplier_id;
        if($debug) debug('des_id ['.$des_id.'] supplier_id ['.$supplier_id.']');

        // Inizio ctrl se produttore DES
        if(empty($des_id)) {
            if($debug) debug("Non ho ancora scelto il mio DES");

            /*
            * se e' associato ad un solo DES lo ricavo
            */
            $desOrganizationsTable = TableRegistry::get('DesOrganizations');
            $desOrganizations = $desOrganizationsTable
                                    ->find()
                                    ->where(['organization_id' => $user->organization->id]) // non ho scelto il DES, ctrl solo se il suo GAS e' titolare
                                    ->all(); 
            if($debug) debug("GAS associato a ".$desOrganizations->count()." DES");
            if($desOrganizations->count()==1) {
                /*
                * e' associato a 1 solo DES
                * non capita + perche' in AppController cerco se ne ha solo 1
                */	
                foreach($desOrganizations as $desOrganization) {
                    $des_id = $desOrganization->des_id;
                    break;
                }

                if($debug) debug("il GAS e' associato ad un solo DES => ricavo des_id  ".$des_id);
            }
            else {
                /*
                 * non ho scelto il DES ma e' associato a + DES, ctrl solo se il suo GAS e' titolare
                 */
                $where = ['DesSuppliers.supplier_id' => $supplier_id, 
                          'DesSuppliers.own_organization_id' => $user->organization->id];    
                if($debug) debug($where);
                $desSuppliers = $this->find()
                                        ->where($where)
                                        ->first();
                if($debug) debug($desSuppliers);                        
                if(!empty($desSuppliers)) {
                    if($debug) debug("il GAS e' associato a + DES => NON posso ricavare des_id ma il suo GAS e' titolare del produttore");
                    $results['isOwnGasTitolareDes'] = true;
                    $results['isDes'] = true;
                }
                else {
                    if($debug) debug("il GAS e' associato a + DES => NON posso ricavare des_id ma il suo GAS NON e' titolare del produttore");
                }                                        
            }  // end if($desOrganizations->count()==1)            
        } // end if(empty($des_id)) 

        if(!empty($des_id)) {
            if($debug) debug("Ho già scelto il mio DES, des_id [".$des_id."]"); 

            /*
            * ho scelto il DES
            */ 
            $where = ['DesSuppliers.supplier_id' => $supplier_id, 
                      'DesSuppliers.des_id' => $des_id];    
 
            $desSuppliers = $this->find()
                                    ->where($where)
                                    ->first();
            if(!empty($desSuppliers)) {
                if($debug) debug("Il produttore fa parte dei produttori DES");

                $results['isDes'] = true;

                /*
                * ctrl se lo user e' associato al produttore come REFERENTE DES o TITOLARE DES 
                */ 
                $desSuppliersReferentsTable = TableRegistry::get('DesSuppliersReferents');
                
                $where = ['DesSuppliersReferents.des_id' => $des_id,
                            'DesSuppliersReferents.organization_id' => $user->organization->id,
                            'DesSuppliersReferents.user_id' => $user->id,
                            'DesSuppliers.des_id' => $des_id,
                            'DesSuppliers.id' => $desSuppliers->id];
                $desOrganization = $desSuppliersReferentsTable->find()
                                        ->contain(['DesSuppliers'])
                                        ->where($where)
                                        ->first(); 
                if(!empty($desOrganization)) {
                    if($desOrganization->group_id == Configure::read('group_id_titolare_des_supplier')) {
                        $results['isTitolareDes'] = true;
                        if($debug) debug("sono TITOLARE DES del produttore");
                    }
                    else
                    if($desOrganization->group_id == Configure::read('group_id_referent_des')) {
                        $results['isReferenteDes'] = true;
                        if($debug) debug("sono REFERENTE DES del produttore");
                    }
                } 
                else {
                    /*
                     * ctrl se lo user e' SUPER-REFERENTE DES 
                     */	
                    if($user->acl['isSuperReferenteDes'])  {
                        $results['isSuperReferenteDes'] = true;
                        if($debug) debug("sono SUPER-REFERENTE DES del produttore");
                    }
                }                   
            }
            else {
                // Il produttore NON fa parte dei produttori DES
                $results['isDes'] = false;
            }
        } // if(empty($des_id))

        /*
         * genero msg per modal 
         */
        if($results['isDes']) {

            $config = Configure::read('Config');
            $portalgas_bo_url = $config['Portalgas.bo.url']; 

            $msgOrderDesLink = '<p>Se desideri gestire l\'<b>ordine condiviso</b> (D.E.S.) ';
            $msgOrderDesLink .= '<a class="btn btn-sm btn-primary" href="'.$portalgas_bo_url.'/administrator/index.php?option=com_cake&amp;controller=DesOrders&amp;action=index">clicca qui</a></p>';
            $msgOrderDes = '<div class="alert alert-danger" role="alert">Ordine D.E.S. o normale?</div>';
            
            if($results['isOwnGasTitolareDes']) {
                $msgOrderDes .= "<p>Il tuo G.A.S. è titolare degli ordini D.E.S. del produttore</p>";
                $msgOrderDes .= $msgOrderDesLink;
            }
            else
            if($results['isTitolareDes']) {
                $msgOrderDes .= "<p>Sei titolare degli ordini D.E.S. del produttore!</p>";
                $msgOrderDes .= $msgOrderDesLink;
            }
            else 
            if($results['isReferenteDes']) {
                $msgOrderDes .= "<p>Sei il referente degli ordini D.E.S. del produttore</p>";
                $msgOrderDes .= $msgOrderDesLink;
            }
            else 
            if($results['isSuperReferenteDes']) {
                $msgOrderDes .= "<p>Sei super-referente degli ordini D.E.S. del produttore</p>";
                $msgOrderDes .= $msgOrderDesLink;
            }

            $results['msg'] = $msgOrderDes;
        }
        else 
            return false; // end if($results['isDes'])
      
        
        return $results; 
    }
}
