<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class OrderPromotionsBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /*
     * https://book.cakephp.org/4/en/orm/saving-data.html#before-marshal
     * modify request data before it is converted into entities
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        // debug('OrderPromotionsBehavior beforeMarshal');

        /*
         * valor di default
         */
        if (!isset($data['prod_gas_promotion_id']) || empty($data['prod_gas_promotion_id'])) 
            $data['prod_gas_promotion_id'] = $data['parent_id']; 

        $data['type_draw'] = 'PROMOTION';

        // debug($data);
    }

    public function beforeSave(Event $event, EntityInterface $entity) {
    	
        // debug($entity);exit;
        // debug($entity->source());
        
        /*
         * il referente o superReferente accetta la promozione:
         * si importa il produttore (da Supplier a SuppliersOrganization)
         *
         * import Produttore   PER ORA IL PRODUTTORE E' GIA' ASSOCIATO AL GAS
         *
         * $organization_id = $entity->organization_id;
         * $prod_gas_promotion_id = $entity->prod_gas_promotion_id;
         *
         * $prodGasPromotionsOrganizationstable = TableRegistry::get('ProdGasPromotionsOrganizations');
         * $supplier_organization_id = $prodGasPromotionsOrganizationstable->importProdGasSupplier($this->user, 
         $prod_gas_promotion_id);
         */

        return true;
    }   

    /*
     * il referente / superReferente accetta la promozione:
     * si importa articoli in promozioni in articoli in ordine (da ProdGasArticlesPromotion a ArticlesOrders)
     * la ProdGasArticlesPromotion.qta diventera' ArticlesOrder.qta_minima_order e ArticlesOrder.qta_massima_order
     */  
    private function _importProdGasArticlesPromotions($organization_id, $prod_gas_promotion_id, $order_id) {

        $prodGasArticlesPromotionsTable = TableRegistry::get('ProdGasArticlesPromotions');
        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');

        $where = ['ProdGasArticlesPromotions.prod_gas_promotion_id' => $prod_gas_promotion_id];
                
        $results = $prodGasArticlesPromotionsTable->find()
                                ->where($where)
                                ->contain(['Articles'])
                                ->all();

        foreach ($results as $result) {
 
            $data = [];
            $data['organization_id'] = $organization_id;
            $data['order_id'] = $order_id;
            $data['article_organization_id'] = $result->article->organization_id;
            $data['article_id'] = $result->article->id;

            $data['name'] = $result->article->name;
            $data['prezzo'] = $result->prezzo_unita; 
            $data['qta_cart'] = 0;
            $data['pezzi_confezione'] = $result->article->pezzi_confezione;
            $data['qta_minima'] = $result->article->qta_minima;
            /*
             * ProdGasArticlesPromotion.qta = quantita' dell'offerta 
             */
            $data['qta_massima'] = $result->qta;
            $data['qta_minima_order'] = $result->qta;
            $data['qta_massima_order'] = $result->qta;
            
            $data['qta_multipli'] = $result->article->qta_multipli;
            $data['flag_bookmarks'] = 'N';
            $data['alert_to_qta'] = 0;
            $data['stato'] = 'Y';  

            $articlesOrder = $articlesOrdersTable->newEntity();
            $articlesOrder = $articlesOrdersTable->patchEntity($articlesOrder, $data);

           /*
             * workaround
             */
            $articlesOrder->organization_id = $organization_id;
            $articlesOrder->order_id = $order_id;
            $articlesOrder->article_organization_id = $result->article->organization_id;
            $articlesOrder->article_id = $result->article->id;

            if (!$articlesOrdersTable->save($articlesOrder)) {
                debug($articlesOrder->getErrors());
            }              
        }
    }

    /*
     * Associo l'ordine per il GAS con la promozione (ProdGasPromotionsOrganizations)
     */ 
    private function _updateProdGasPromotionsOrganizations($organization_id, $prod_gas_promotion_id, $order_id) {

        $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');

        $where = ['ProdGasPromotionsOrganizations.prod_gas_promotion_id' => $prod_gas_promotion_id,
                  'ProdGasPromotionsOrganizations.organization_id' => $organization_id];
        // debug($where);

        $results = $prodGasPromotionsOrganizationsTable->find()
                                ->where($where)
                                ->first();

        $data = [];
        $data['order_id'] = $order_id;
        $data['state_code'] = 'OPEN';

        $results = $prodGasPromotionsOrganizationsTable->patchEntity($results, $data);
        // debug($results);
        if(!$prodGasPromotionsOrganizationsTable->save($results)) {
            debug($results->getErrors());
        }
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options) {

        $order_id = $entity->id;
        $organization_id = $entity->organization_id;
        $prod_gas_promotion_id = $entity->prod_gas_promotion_id;
        
        $this->_importProdGasArticlesPromotions($organization_id, $prod_gas_promotion_id, $order_id);

        $this->_updateProdGasPromotionsOrganizations($organization_id, $prod_gas_promotion_id, $order_id);                      
    } 
}