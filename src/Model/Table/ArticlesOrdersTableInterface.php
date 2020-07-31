<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

	public function gets($user, $organization_id, $order_id, $where, $order, $debug);
}