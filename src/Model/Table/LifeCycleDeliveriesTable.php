<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class LifeCycleDeliveriesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('k_deliveries');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['organization_id', 'id']);      
    }
}