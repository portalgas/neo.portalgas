<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class GdxpArticleOrdersBehavior extends Behavior
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
      	
			$result->sku = $result->article->codice;
			      	
			$result->category = "Non Specificato"; // $result->article->categories_article->name;
			     
			$result->description = trim(strip_tags($result->article->nota).' '.strip_tags($result->article->ingredienti));
			  	
			$result->um = $result->article->um;

			$result->active = true;
						  	
			$result->orderInfo = [
				'packageQty' => $result->article->pezzi_confezione,
				'maxQty' => $result->qta_massima,
				'minQty' => $result->qta_minima,
				'mulQty' => $result->qta_multipli,
				'umPrice' => $result->prezzo,
				'shippingCost' => 0.0
				//'vatRate' => '',
				//'availableQty' => ''
			];

			/*
			 * qta acquistata
			 */
            $result->bookingInfo = [
                "qta_cart" => $result->qta_cart
            ];

			unset($result->article);

			unset($result->organization_id);
			unset($result->order_id);
			unset($result->article_organization_id);
			unset($result->article_id);

			unset($result->qta_cart);
			unset($result->prezzo);
			unset($result->pezzi_confezione);
			unset($result->qta_massima);
			unset($result->qta_minima);
			unset($result->qta_multipli);
            unset($result->qta_minima_order);
            unset($result->qta_massima_order);

            unset($result->alert_to_qta);
			unset($result->flag_bookmarks);
			unset($result->send_mail);
			unset($result->stato);
			unset($result->created);
			unset($result->modified);			
		}
    }
}