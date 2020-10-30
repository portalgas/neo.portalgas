<?php
namespace App\Model\Table;

interface ArticlesOrdersTableInterface {

	/* 
	 * options: sort, offset, page
	 */ 
	public function getCarts($user, $organization_id, $user_id, $orderResults, $where=[], $options=[], $debug=false);

    public function gets($user, $organization_id, $orderResults, $where=[], $options=[], $debug=false);

   /*
     * ids ['organization_id', 'order_id', 'article_organization_id', 'article_id']
     */
    public function getByIds($user, $organization_id, $ids, $debug=false);
}