<?php
namespace App\Model\Table;

interface OrderTableInterface {

	public function getSuppliersOrganizations($user, $organization_id, $where=[], $debug=false);

	public function getDeliveries($user, $organization_id, $where=[], $debug=false);

	public function gets($user, $organization_id, $where=[], $debug=false);
	
	public function getsList($user, $organization_id, $where=[], $debug=false);
}