<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class TreeCustomBehavior extends TreeBehavior
{
	protected $config = [];

    protected $_defaultConfig = [
        'implementedFinders' => [
            'path' => 'findPath',
            'children' => 'findChildren',
            'treeList' => 'findTreeList',
        ],
        'implementedMethods' => [
            'childCount' => 'childCount',
            'moveUp' => 'moveUp',
            'moveDown' => 'moveDown',
            'recover' => 'recover',
            'removeFromTree' => 'removeFromTree',
            'getLevel' => 'getLevel',
            'formatTreeList' => 'formatTreeList',
            'getLevelFirst' => 'getLevelFirst',
            'getLevelLast' => 'getLevelLast',
        ],
        'parent' => 'parent_id',
        'left' => 'lft',
        'right' => 'rght',
        'scope' => null,
        'level' => null,
        'recoverOrder' => null,
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);

    	$this->config = $config;
    }

    public function getLevelFirst() {

        $results = [];
        
        $options['conditions'] = ['parent_id is ' => null];
        $results = $this->_table->find('list', $options);
        
        //debug($results);

        return $results;
    }
    
    public function getLevelLast($results, $id, $order=[], $debug=false) {
        
        /*
        debug($this->_table);
        debug($this->_table->associations()->keys()[1]);
        debug($this->_table->getTable());
        debug($this->_table->getAlias());
        */
        
        if($debug) echo "<pre>TreeCustomBehavior::getLevelLast() id ".$id."</pre>";
                
        $children = $this->_table
            ->find('children', ['for' => $id, 'order' => $order])
            ->order($order)
            ->find('threaded')
            ->contain($this->_table->associations()->keys()[1]) // 'ParentOfferTypes' 'ParentOfferVersions'
            ->toArray()
            ;
 
        if(empty($children)) {
            /*
             * non ha figli => preso lo stesso
             */
            $result = $this->_table->get($id);
            if($debug) echo "<pre>{$result->name} {$id} non ha figli</pre>";
            array_push($results, $result);
        }
        else {
            foreach ($children as $child) {
                if(count($child->children)==0) {
                    if($debug) echo "<pre>{$child->name} has " . count($child->children) . " direct children</pre>";
                    array_push($results, $child);
                }
                else {
                    $results = $this->getLevelLast($results, $child->id, $order);
                    if($debug) echo "<pre>{$child->name} has <b>" . count($child->children) . "</b> direct children</pre>";
                }
            }           
        }

        return $results;
    }  
}