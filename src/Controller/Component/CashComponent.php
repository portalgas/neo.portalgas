<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CashComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	public function getByUser($user, $organization_id, $user_id, $options=[], $debug=false) {

		$results = [];

        $cashesTable = TableRegistry::get('Cashes');

        $where = ['Cashes.organization_id' => $organization_id,
   				  'Cashes.user_id' => $user_id];
		if($debug) debug($where);

        $results = $cashesTable->find()
                                ->where($where)
                                ->first();
        
        if($debug) debug($results);
		
		return $results;
	}

    /* 
     * dato un importo, calcolo il nuovo valore di cassa di uno user
     * ex SummaryOrders.importo 
     */
    public function getNewImport($user, $importo_da_pagare, $cash_importo, $debug=false) {

        $results = ($cash_importo - $importo_da_pagare);       
                
        return $results;
    }
}