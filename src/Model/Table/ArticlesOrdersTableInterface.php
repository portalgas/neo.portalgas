<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

	/* 
	 * options: sort, offset, page
	 */ 
	public function getCarts($user, $organization_id, $user_id, $orderResults, $where, $options, $debug);
}