<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiSupplierDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $suppliers)
    {
    	$results = [];
	    // debug($suppliers);

	    if($suppliers instanceof \Cake\ORM\ResultSet) {
			foreach($suppliers as $numResult => $supplier) {
				$results[$numResult] = $this->_decorate($user, $supplier);
			}
	    }
	    else 
	    if($suppliers instanceof \App\Model\Entity\Supplier) {
			$results = $this->_decorate($user, $suppliers);  	
	    }
        else {
            foreach($suppliers as $numResult => $supplier) {
                $results[$numResult] = $this->_decorate($user, $supplier);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $supplier) {

        // debug($supplier);
        
        if(isset($supplier->content)) {
        	if(isset($supplier->content->introtext)) 
	            $supplier->content->introtext = str_replace('{flike}', '', $supplier->content->introtext);
        	if(isset($supplier->content->fulltext)) 
	            $supplier->content->fulltext = str_replace('{flike}', '', $supplier->content->fulltext);
        }

        $supplier->img1 = $this->_getSupplierImg1($supplier);
        $supplier->address_full = $this->_getSupplierAddressFull($supplier);

        $html = '';
        if($supplier->voto>0) {
            
            $html .= '<ul class="ratings">';
            $html_items = str_repeat('<li class="star" aria-hidden="true"></li>', ($supplier->voto + 1));
            $html .= $html_items.'</ul>';
        }
        $supplier->voto_html = $html;

        /*
         * hasOrganization
         * utente autenticato, controllo se il produttore e' gia' associato al GAS
         */
        if($user!==null) {
            
            $organization_id = $user->organization->id; // gas scelto

            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

            $suppliersOrganizationsResults = $suppliersOrganizationsTable->find()
                    ->where(['SuppliersOrganizations.supplier_id' => $supplier->id,
                             'SuppliersOrganizations.organization_id' => $organization_id])
                    ->first();                
            
            if(!empty($suppliersOrganizationsResults)) {
                $supplier->hasOrganization = true;
            }
            else
                $supplier->hasOrganization = false;
        } // if($user!==null)

        // debug($supplier);
        return $supplier;
    }

	function name() {
		return $this->results;
	}
}