<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ArticlesOrdersGasTable extends ArticlesOrdersTable implements ArticlesOrdersTableInterface 
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
     * estrae gli articoli associati / da associare  ad un ordine
     * ArticlesOrders.owner_articles                 = 
     * ArticlesOrders.owner_organization_id          = Articles.organization_id
     * ArticlesOrders.owner_supplier_organization_id = Articles.supplier_organization_id
     */
    public function gets($user, $organization_id, $order_id, $where, $order, $debug=false) {
    
        $ordersTable = TableRegistry::get('Orders');
        $orderResults = $ordersTable->getById($user, $organization_id, $order_id, $debug);

        $owner_articles = $orderResults->owner_articles;
        $owner_organization_id = $orderResults->owner_organization_id;
        $owner_supplier_organization_id = $orderResults->owner_supplier_organization_id;

        $articlesTable = TableRegistry::get('Articles');

        $where = ['Articles.organization_id' => $owner_organization_id,
                  'Articles.supplier_organization_id' => $owner_supplier_organization_id];
        // debug($where);
        $order = ['Articles.name'];

        $results = $this->find()
                        ->where($where)
                        ->order($order)
                        ->all();
        return $results;
    }    
}
