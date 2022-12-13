<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ArticlesOrdersPactTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass('App\Model\Entity\ArticlesOrder');
    }

    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);
        
        return $validator;
    }

  /* 
   * implements
   * 
   * gestione associazione articoli all'ordine
   * return
   *  proprietario listino: per gestione permessi di modifica
   *  article_orders: articoli gia' associati
   *  articles: articoli da associare
   */    
     public function getAssociateToOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false) {
        parent::getAssociateToOrder($user, $organization_id, $order, $where, $options, $debug);
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
    public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false) {
        $this->_getOptions($options); // setta sort / limit / page
        $results = parent::getCartsByUser($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
    } 

    /*
     * implement
     */
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {
        $this->_getOptions($options); // setta sort
        $results = parent::getCartsByArticles($user, $organization_id, $orderResults, $where, $options, $debug);
    } 

    /*
     * implement
     */
    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false) {  
       $this->_getOptions($options); // setta sort / limit / page  
       return parent::gets($user, $organization_id, $orderResults, $where, $options, $debug);
    }

    /*
     * implement
     */
    public function getByIds($user, $organization_id, $ids, $debug=false) {    
       return parent::getByIds($user, $organization_id, $ids, $debug);
    }       
}
