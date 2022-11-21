<?php
namespace App\Model\Table;

interface OrderTableInterface {

	// BO produttori per creare l'ordine
	public function getSuppliersOrganizations($user, $organization_id, $where=[], $debug=false);

	// BO consegne per creare l'ordine
	public function getDeliveries($user, $organization_id, $where=[], $debug=false);

	/*
	 * BO consegne per creare l'ordine
	 * dati promozione / order des
	 * $parent_id = prod_gas_promotion_id / des_order_id / order_id (gas_groups)
	 */
	public function getParent($user, $organization_id, $parent_id, $where=[], $debug=false);

    /*
     * ..behaviour afterSave() ha l'entity ma non la request
     */ 
	public function afterSaveWithRequest($user, $organization_id, $request, $debug=false);

    /*
     * get() gia' Cake\ORM\Table::get($primaryKey, $options = Array)
     */   
	public function getById($user, $organization_id, $order_id, $debug=false);

	public function gets($user, $organization_id, $where=[], $debug=false);
	
	public function getsList($user, $organization_id, $where=[], $debug=false);
}