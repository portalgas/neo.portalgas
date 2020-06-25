<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class PriceTypeComponent extends Component {

	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = []) {
	}

	/*
	 * se il form ha degli errori di validazione, recupero i dati della priceTypes e creo una variabile js che vue con il metodo getRows() recupera
	 */
	public function jsonToRequest($user, $request, $debug=false) {
		
		if($debug) debug($request);

		$results = '';
		if(isset($request['priceTypes_name'])) {
			$results .= '[';
			foreach ($request['priceTypes_name'] as $index => $value) {
				
				$results .= '{';
				
				$results .= 'name: "'.$request['priceTypes_name'][$index].'",';
				$results .= 'descri: "'.$request['priceTypes_descri'][$index].'",';
				$results .= 'type: "'.$request['priceTypes_type'][$index].'",';
				$results .= 'value: "'.$request['priceTypes_value'][$index].'",';
				$results .= 'sort: "'.$request['priceTypes_sort'][$index].'"';
				$results .= '},';
	
			}
			$results = substr($results, 0, (strlen($results)-1));
			$results .= ']';		
		}

		if($debug) debug($results);
		
		return $results;	
	}
}