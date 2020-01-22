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

class GdxpArticlesBehavior extends TreeBehavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * se va in conflitto, ex $entityTable->removeBehavior('GdxpArticles');
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)  {
        $results = $query->all();
		
        // debug($results);exit;
        foreach ($results as $key => $result) {
      	
			$result->sku = $result->codice;
			unset($result->codice);
			      	
			$result->category = $result->categories_article->name;
			unset($result->category_article_id);
			unset($result->categories_article);
			     
			$result->description = trim(strip_tags($result->nota).' '.strip_tags($result->ingredienti));
			unset($result->nota);
			unset($result->ingredienti);
			  	
			$result->orderInfo = [
				'packageQty' => $result->pezzi_confezione,
				'maxQty' => $result->qta_massima,
				'minQty' => $result->qta_minima,
				'mulQty' => $result->qta_multipli,
				'umPrice' => $result->um,
				'shippingCost' => $result->prezzo
			];
			unset($result->pezzi_confezione);
			unset($result->qta_massima);
			unset($result->qta_minima);
			unset($result->qta_multipli);
			unset($result->um);
			unset($result->prezzo);

			unset($result->id);
			unset($result->organization_id);
			unset($result->supplier_organization_id);
			unset($result->img1);
			unset($result->stato);
			unset($result->flag_presente_articlesorders);
			unset($result->category);
			unset($result->created);
			unset($result->modified);			
		}
    }
}