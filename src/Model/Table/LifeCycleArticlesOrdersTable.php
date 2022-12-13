<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class LifeCycleArticlesOrdersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_articles_orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey(['organization_id', 'order_id', 'article_organization_id', 'article_id']);

        $this->addBehavior('Timestamp');     
    }

    /* 
     * ctrl e in base all'ordine lo user puo' modificare gli articoli associati 
     * all'ordine 
     */
    public function canEditByOrder($user, $organization_id, $order, $debug=false) {
        
        if($order->owner_articles=='REFERENT' && $order->owner_organization_id==$organization_id)
            return true;
        else 
            return false;
    }
}