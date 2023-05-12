<?php
declare(strict_types=1);

namespace App\Decorator\Export;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use App\Decorator\AppDecorator;

class OrdersGasParentDecorator extends AppDecorator {
	
	public $serializableAttributes = ['id', 'name'];
	public $results; 

    public function __construct($order)
    {  
		if(empty($order)) 
			return [];
			
		$order->suppliers_organization->supplier->img1 = $this->_getSupplierImg1($order->suppliers_organization->supplier);
		$order->suppliers_organization->supplier->address_full = $this->_getSupplierAddressFull($order->suppliers_organization->supplier);

        $this->results = $order;
    }

	function name() {
		return $this->results;
	}
}