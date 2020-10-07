<?php
namespace App\Model\Table;

interface OrganizationTableInterface {

	public function gets($where=[], $debug=false);

	public function getById($organization_id, $where=[], $debug=false);
	
	public function getsList($where=[], $debug=false);
}