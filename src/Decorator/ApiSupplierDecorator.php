<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiSupplierDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($suppliers)
    {
    	$results = [];
	    // debug($suppliers);

	    if($suppliers instanceof \Cake\ORM\ResultSet) {
			foreach($suppliers as $numResult => $supplier) {
				$results[$numResult] = $this->_decorate($supplier);
			}
	    }
	    else 
	    if($suppliers instanceof \App\Model\Entity\Supplier) {
			$results = $this->_decorate($suppliers);  	
	    }
        else {
            foreach($suppliers as $numResult => $supplier) {
                $results[$numResult] = $this->_decorate($supplier);
            }
        }

		$this->results = $results;
    }

	private function _decorate($supplier) {

        // debug($supplier);

        
        if(isset($supplier->content) && isset($supplier->content->fulltext)) {
            $supplier->content->fulltext = str_replace('{flike}', '', $supplier->content->fulltext);
        }
        // debug($supplier);
        return $supplier;
    }

	function name() {
		return $this->results;
	}
}