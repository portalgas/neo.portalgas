<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticlesOrdersPactPreTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->entityClass('App\Model\Entity\ArticlesOrder');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }
    
    /*
     * implement
    */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug=false) {
        return parent::aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug);
    }
        
    /*
     * implement
     */
    public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {
        $results = parent::getCarts($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
    } 

    /*
     * implement
     */
    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {    
       return parent::gets($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function getByIds($user, $organization_id, $ids, $debug=false) {    
       return parent::getByIds($user, $organization_id, $ids, $debug);
    }       
}
