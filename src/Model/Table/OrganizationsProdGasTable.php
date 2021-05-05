<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class OrganizationsProdGasTable extends OrganizationsTable implements OrganizationTableInterface 
{
    private $_type = 'PRODGAS';

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
        return $rules;
    }

    /*
     * implement
     */ 
    public function gets($where=[], $debug=false) {

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

    public function create($supplier, $debug=false) {
        
        $esito = true;
        $code = '200';
        $msg = '';
        $results = []; 

        $data = [];
        $data['name'] = $supplier->name;
        $data['template_id'] = 0;
        $data['j_group_registred'] = 0;
        $data['j_page_category_id'] = 0;
        $data['type'] = 'PRODGAS';
        $data['paramsConfig'] = Configure::read('OrganizationProdGas.paramsConfig.default');
        $data['paramsFields'] = '{"hasFieldArticleCodice":"Y","hasFieldArticleIngredienti":"Y","hasFieldArticleCategoryId":"Y"}';
        $data['descrizione'] = $supplier->descrizione;
        $data['indirizzo'] = $supplier->indirizzo;
        $data['localita'] = $supplier->localita;
        $data['cap'] = $supplier->cap;
        $data['provincia'] = $supplier->provincia;
        $data['www2'] = $supplier->www2;
        $data['sede_logistica_1'] = '';
        $data['sede_logistica_2'] = '';
        $data['sede_logistica_3'] = '';
        $data['sede_logistica_4'] = '';
        $data['banca_iban'] = '';
        $data['lat'] = $supplier->lat;
        $data['lng'] = $supplier->lng;
        $data['img1'] = $supplier->img1;
        $data['j_seo'] = '';        
        $data['paramsPay'] = '{}';
        $data['hasMsg'] = 'N';
        $data['stato'] = 'Y';

        $organization = $this->newEntity();
        $organization = $this->patchEntity($organization, $data);
        // debug($organization);
        if (!$this->save($organization)) {
            $esito = false;
            $code = '500';
            $msg = '';
            $results = $organization->getErrors(); 

            debug($results); exit;
        }
        else 
            $results = $organization;

        $results = ['esito' => $esito, 'code' => $code, 'msg' => $msg, 'results' => $results];

        return $results; 
    }       
}