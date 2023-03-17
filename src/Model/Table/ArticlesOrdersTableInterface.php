<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

  /* 
   * gestione associazione articoli all'ordine
   * 
   * return
   *  proprietario listino: per gestione permessi di modifica
   *  article_orders: articoli gia' associati
   *  articles: articoli da associare
   * 
   * richiamato in ajax da ArticleOrders::index
   *  App\Controller\Admin\Api\ArticleOrdersController\getAssociateToOrder 
   */
  public function getAssociateToOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false);

	/*
   * front-end - estrae gli articoli associati ad un ordine ed evenuali acquisti per user  
   *
   * richiamato in 
   * /admin/api/orders/user-cart-gets
   * /admin/api/orders/user-cart-gets/9
   * stampa carrello   
   * elenco ordini con acquisti dell'utente x fe 
   *  App\Controller\Component\OrderComponent\userCartGets
   *  App\Controller\Component\SocialMarketComponent\userCartGets
   *
   * richiamato in  
   * /admin/api/orders/getArticlesOrdersByOrderId 
   *  App\Controller\Component\OrderComponent\getArticlesOrdersByOrderId
   *
	 * options: sort, offset, page
   * ricerca articoli di un ordine ed eventuali acquisti di uno user
	 */ 
	public function getCartsByUser($user, $organization_id, $user_id, $order, $where=[], $options=[], $debug=false);

  /* 
   * ricerca articoli di un ordine ed eventuali acquisti di tutti gli users
   *
   * estrae gli articoli associati ad un ordine ed evenuuali acquisti di tutti gli users
   * ArticlesOrders.article_id              = Articles.id
   * ArticlesOrders.article_organization_id = Articles.organization_id
   *
   * nell'associazione articoli con ordine articles-orders/index/order_type_id/order_id
   */
  public function getCartsByArticles($user, $organization_id, $order, $where=[], $options=[], $debug=false);

  /*
   * dato un ordine estrare tutti gli acquisti di tutti gli utenti
   * 
   * per le stampe
   */
  public function getCartsByOrder($user, $organization_id, $order, $where=[], $options=[], $debug=false);

  public function gets($user, $organization_id, $order, $where=[], $options=[], $debug=false);

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