<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class OrganizationsGasTable extends OrganizationsTable implements OrganizationTableInterface 
{
   private $_type = 'GAS';
    
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\Organization');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {   
        // debug('OrdersGasTable buildRules');
        $rules = parent::buildRules($rules);

        return $rules;
    }

    /*
     * implement
     */ 
    public function gets($user=null, $where=[], $debug=false) {

        $results = [];
        $where = array_merge([$this->getAlias().'.type' => $this->_type], $where);
        $results = $this->find()
                        ->where($where)
                        ->order([$this->getAlias().'.name'])
                        ->all();

        return $results;        
    }

    /*
     * implement
     */ 
    public function getById($organization_id, $where=[], $debug=false) {
        
        $results = [];
        $where = array_merge([$this->getAlias().'.type' => $this->_type, 
                             $this->getAlias().'.organization_id' => $organization_id], $where);
        $results = $this->find()
                        ->where($where)
                        ->first();
                        
        return $results;         
    }
    
    /*
     * implement
     */     
    public function getsList($where=[], $debug=false) {
        $results = [];
        $where = array_merge([$this->getAlias().'.type' => $this->_type], $where);        
        $results = $this->find('list', ['conditions' => $where]);

        return $results; 
    }   
}