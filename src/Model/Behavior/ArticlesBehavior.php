<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use App\Traits;

class ArticlesBehavior extends Behavior
{
    use Traits\SqlTrait;

	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * se va in conflitto, ex $articlesTable->removeBehavior('Articles');
     */
    public function beforeSave(Event $event, EntityInterface $entity) {
        if ($entity->isNew()) {
            
            $article_id_max = $this->_getMaxIdOrganizationId($entity->organization_id);
            $article_id_max++;
            $entity->id = $article_id_max;
        }
    }

    private function _getMaxIdOrganizationId($organization_id) {
        $where['organization_id'] = $organization_id;
        return $this->getMax('Articles', 'id', $where);
    }    
}