<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

	public function getCarts($user, $organization_id, $user_id, $where, $order, $debug);
}