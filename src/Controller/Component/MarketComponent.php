<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class MarketComponent extends Component {

	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = []) {
	}

	/*
	 * organization_id del produttore
	 *
	 * order_id = $prod_gas_promotion_id
	 * order_id necessario perche' key carts / articles_orders
	 * poi in CartProdGasPromotionGasUserComponent disabilito buildRules per $rules->existsIn(['organization_id', 'order_id'], 'Orders')
	 */
	public function getOrderDefault($user, $organization_id, $prod_gas_promotion_id, $debug=false) {
		
        $order = new \stdClass();
        $order->order_state_code = new \stdClass();
        $order->order_type = new \stdClass();

        $order->organization_id = $organization_id;
        $order->id = $prod_gas_promotion_id;
        $order->prod_gas_promotion_id = $prod_gas_promotion_id;
        $order->state_code = 'OPEN';
        $order->order_state_code->code = 'OPEN';
        $order->type_draw = 'PROMOTION';
        $order->order_type->code = 'PROMOTION_GAS_USERS';
		
		return $order;	
	}
}