<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class ProdGasPromotionComponent extends Component {

	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = []) {
	}

	public function getOrderDefault($user, $debug=false) {
		
        $order = new \stdClass();
        $order->order_state_code = new \stdClass();
        $order->order_type = new \stdClass();

        $order->id = Configure::read('OrderIdPromotionGasUsers');
        $order->state_code = 'OPEN';
        $order->order_state_code->code = 'OPEN';
        $order->type_draw = 'PROMOTION';
        $order->order_type->code = 'PROMOTION_GAS_USERS';
		
		return $order;	
	}
}