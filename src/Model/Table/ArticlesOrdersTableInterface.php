<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

	/* 
	 * options: sort, offset, page
     * ricerca articoli di un ordine ed eventuali acquisti di uno user
	 */ 
	public function getCartsByUser($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false);

    /* 
     * ricerca articoli di un ordine ed eventuali acquisti di tutti gli users
     */ 
    public function getCartsByArticles($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false);

    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false);

   /*
     * ids ['organization_id', 'order_id', 'article_organization_id', 'article_id']
     */
    public function getByIds($user, $organization_id, $ids, $debug=false);

    /*
     * Cart->managementCart()
     *
     * aggiorno la ArticlesOrder.qta_cart con Cart.qta + Cart.qta_forzato
     * ctrl se ArticlesOrder.qta_massima_order > 0, se SI controllo lo ArticlesOrder.stato
     * 
     * se Ordine e' DES 
     *      ArticlesOrder.qta_massima_order indica la somma delle ArticlesOrder.qta_cart di tutti i GAS dell'ordine DES
     *          cosi' a FE c'e' per tutti il blocco se raggiunta la qta_massima_order
     */
    public function aggiornaQtaCart_StatoQtaMax($user, $organization_id, $order, $article, $debug=false);    
}