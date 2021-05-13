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

class GdxpArticleOrdersBehavior extends Behavior
{
	use Traits\UtilTrait;

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
		
		$cartsTable = TableRegistry::get('Carts');

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
             * calcolo totale qta per ogni articolo
			 *
			 * non posso prendere il totale della qta acquistata da ArticlesOrders.qta_cart perche' se ordine DES e' la somma di tutti i GAS
			 * sommo qta / qta_forzato da Carts
			*/
			$user = $this->createObjUser();
			$organization_id = $result->organization_id;
			$order_id = $result->order_id;
			$article_organization_id = $result->article_organization_id;
			$article_id = $result->article_id;
			
            $qta_cart = $cartsTable->getQtaCartByArticle($user, $organization_id, $order_id, $article_organization_id, $article_id); 
            
            $result->bookingInfo = [
                // "totalQty" => $result->qta_cart, // ArticlesOrders.qta_cart
                "totalQty" => $qta_cart
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