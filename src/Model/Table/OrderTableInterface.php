<?php
namespace App\Model\Table;

interface OrderTableInterface {

	/*
	 * id des_order_id / prod_gas_promotion_id / pact_id 
	 */

	public function getSuppliersOrganizations($user, $id=0, $debug=false);

	public function getDeliveries($user, $id=0, $debug=false);	
}