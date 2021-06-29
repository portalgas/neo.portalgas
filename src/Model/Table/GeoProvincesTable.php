<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Cache\Cache;

/**
 * GeoProvinces Model
 *
 * @property \App\Model\Table\GeoRegionsTable&\Cake\ORM\Association\BelongsTo $GeoRegions
 *
 * @method \App\Model\Entity\GeoProvince get($primaryKey, $options = [])
 * @method \App\Model\Entity\GeoProvince newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GeoProvince[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GeoProvince|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GeoProvince saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GeoProvince patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GeoProvince[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GeoProvince findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GeoProvincesTable extends Table
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

        $this->setTable('k_geo_provinces');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GeoRegions', [
            'foreignKey' => 'geo_region_id',
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
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('sigla')
            ->maxLength('sigla', 2)
            ->allowEmptyString('sigla');

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
        $rules->add($rules->existsIn(['geo_region_id'], 'GeoRegions'));

        return $rules;
    }

    public function getList($geo_region_id=0, $debug=false) {
        
        $geoProvinces = [];
        $where = [];

        $provinces = Cache::read('provinces_'.$geo_region_id);
        if ($provinces !== false) {
            $geoProvinces = $provinces;
        }
        else {
            if(!empty($geo_region_id))
                $where = ['geo_region_id' => $geo_region_id];

            $results = $this->find()
                            ->where($where)
                            ->order(['name' => 'asc'])
                            ->all();

            if($results->count()>0) {
                foreach($results as $result) {
                    $geoProvinces[$result->sigla] = $result->name.' ('.$result->sigla.')';
                }
            }

            Cache::write('provinces_'.$geo_region_id, $geoProvinces);
        }

        return $geoProvinces;   
    }

    /*
     * estarggo tuttle le province di una regione 
     */
    public function getByIdGeoRegion($geo_region_id, $debug=false) {
        
        $where = ['geo_region_id' => $geo_region_id];

        $results = $this->find()
                        ->where($where)
                        ->order(['name' => 'asc'])
                        ->all();

        return $results;
    }

    /*
     * estarggo tuttle le sigle di una regione 
     */
    public function getSiglaByIdGeoRegion($geo_region_id, $debug=false) {
        
        $geoProvinces = [];
        
        $results = $this->getByIdGeoRegion($geo_region_id);
        if(!empty($results)) {
            foreach($results as $result) {
                array_push($geoProvinces, $result->sigla);
            }
        }

        return $geoProvinces;
    } 
}
