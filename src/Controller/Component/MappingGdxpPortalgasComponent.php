<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Traits;

class MappingGdxpPortalgasComponent extends Component {

    use Traits\SqlTrait;
    use Traits\UtilTrait;

	public function getEmpty($organization_id) {
		return ' ';  // se '' l'entity non viene valorizzato
	}

	/*
	 * definito in queue_tables. Supplier.before_save
	 * salto _save se $action = false
	 */
	public function supplierExists($datas, $organization_id) {

		$debug = true;
        $esito = true;
        $action = false;
        $code = 200;
        $msg = '';
        $results = [];

		if(empty($datas)) {
	        $esito = false;
	        $code = 500;
	        $msg = 'supplierExists - data required';
		}

		if($esito) {
			if(isset($datas[0]))
				$datas = current($datas);

			if(!isset($datas['piva'])) {
		        $esito = false;
		        $code = 500;
		        $msg = 'supplierExists - piva required';
			}
		}

		if($esito) {
			$vatNumber = $datas['piva'];

			$suppliersTable = TableRegistry::get('Suppliers');

			$where = ['Suppliers.piva' => $vatNumber];
	        $results = $suppliersTable->find()
			            ->where($where)
						->first();

			if(!empty($results))  {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierExists - supplier exist with Suppliers.piva= ['.$vatNumber.'] => not insert';
		        $action = false; // not insert
			}
			else {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierExists - supplier not exist with Suppliers.piva = ['.$vatNumber.'] => insert';
		        $action = true; // insert
			}
		}

		$results = ['action' => $action, 'esito' => $esito, 'code' => $code, 'msg' => $msg, 'results' => $results];

		return $results;
	}


	/*
	 * definito in queue_tables. SupplierOrganizations.before_save
	 * salto _save se $action = false
	 */
	public function supplierOrganizationsExists($datas, $organization_id) {

		$debug = false;
        $esito = true;
        $action = false;
        $code = 200;
        $msg = '';
        $results = [];

		if(empty($datas)) {
	        $esito = false;
	        $code = 500;
	        $msg = 'supplierOrganizationsExists - data required';
		}

		if($esito) {
			if(isset($datas[0]))
				$datas = current($datas);

			$supplier_id = $datas['supplier_id'];

			$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

			$where = ['SuppliersOrganizations.organization_id' => $organization_id,
					 'SuppliersOrganizations.supplier_id' => $supplier_id];
	        $results = $suppliersOrganizationsTable->find()
			            ->where($where)
						->first();
			if($debug) debug($where);
			if($debug) debug($results);

			if(!empty($results))  {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierOrganizationsExists - supplier exist with supplier_id = ['.$supplier_id.'], organization_id = ['.$organization_id.'] => not insert';
		        $action = false;
			}
			else {

				/*
				 * creo SuppliersOrganizations
				 */
				$suppliersTable = TableRegistry::get('Suppliers');
				$supplier = $suppliersTable->get($supplier_id);
				$results = $suppliersOrganizationsTable->create($organization_id, $supplier);	
				if(!$results['esito']) {
			        $esito = $results['esito'];
			        $code = $results['code'];
			        $msg = $results['msg'];
			        $results = $results['results'];
				}
				else {
			        $esito = true;
			        $code = 200;
			        $msg = 'supplierOrganizationsExists - supplier not exist with supplier_id = ['.$supplier_id.'], organization_id = ['.$organization_id.'] => insert';
				}
		        
		        /* 
		         * sempre a false perche' l'ho salvato prima
		         */
		        $action = false;
			}
		}

		$results = ['esito' => $esito, 'action' => $action,'code' => $code, 'msg' => $msg, 'results' => $results];
		if($debug) debug($results);
		
		return $results;
	}

	/*
	 * definito in queue_tables. SupplierOrganizations.before_save
	 * salto _save se $action = false
	 */
	public function articleNotArticleOrders($datas, $organization_id) {

        $esito = true;
        $action = true; // insert
        $code = 200;
        $msg = '';
        $results = [];

		if(empty($datas)) {
	        $esito = false;
	        $code = 500;
	        $msg = 'articleNotArticleOrders - data required';
		}

		if($esito) {
			if(isset($datas[0]))
				$datas = current($datas);

			$supplier_organization_id = $datas['supplier_organization_id'];

			$articlesTable = TableRegistry::get('Articles');

			$where = ['organization_id' => $organization_id,
					 'supplier_organization_id' => $supplier_organization_id];

			if(!$articlesTable->updateAll(
			        ['flag_presente_articlesorders' => 'N'],
			        [$where]
			    )) {
		        $esito = true;
		        $code = 200;
		        $msg = 'Articles.updateAll flag_presente_articlesorders = N non trovati records';	
		        $results = $where;			
			}
			else {
		        $esito = true;
		        $code = 200;
		        $msg = 'Articles.updateAll flag_presente_articlesorders = N => aggiornati records';
		        $results = $where;			
			}	
			
			$action = true; // insert
		}

		$results = ['esito' => $esito, 'action' => $action,'code' => $code, 'msg' => $msg, 'results' => $results];
		// debug($results);
		
		return $results;		
	}

	/*
	 * $vatNumber = piva
	 */
	public function getSupplierId($organization_id, $vatNumber) {

        $supplier_id = 0;

		$suppliersTable = TableRegistry::get('Suppliers');
		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

		$where = ['Suppliers.piva' => $vatNumber,
				  // 'SuppliersOrganizations.organization_id' => $organization_id
				 ];
        $results = $suppliersTable->find()
            ->where($where)
            // ->contain(['Suppliers'])
			->first();
		// debug($where);
		// debug($results);
		
		if(!empty($results)) {
			$supplier_id = $results->id;		
		}

		return $supplier_id;
	}

	public function getMaxArticleId($organization_id) {
		$where = ['organization_id' => $organization_id];
		$article_id_max = $this->getMax('Articles', 'id', $where);
		$article_id_max++;

		return $article_id_max;
	}

	public function getSupplierOrganizationIdByVatNumber($organization_id, $vatNumber) {

        $supplier_organization_id = 0;

		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

		$where = ['Suppliers.piva' => $vatNumber,
				  'SuppliersOrganizations.organization_id' => $organization_id
				 ];
        $results = $suppliersOrganizationsTable->find()
									            ->where($where)
									            ->contain(['Suppliers'])
												->first();
		if(!empty($results)) {
			$supplier_organization_id = $results->id;		
		}

		return $supplier_organization_id;
	}

	/*
	 * ENUM('PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT')
	 */ 
	public function translateArticleUm($organization_id, $um) {

		// debug('translateArticleUm BEFORE organization_id ['.$organization_id.'] um ['.$um.']');

		switch (strtolower($um)) {
			case 'sacchetti':
			case 'pz':
				$um = 'PZ';	
			break;
			case 'gr':
			case 'grammi':
				$um = 'GR';	
			break;
			case 'hg':
			case 'etti':
			case 'etto':
				$um = 'HG';	
			break;
			case 'kg':
			case 'chili':
			case 'chilo':
			case 'kilogrammi':
			case 'kilogrammo':
				$um = 'KG';	
			break;
			case 'ml':
			case 'milligrammi':
			case 'milligrammo':
				$um = 'ML';	
			break;
			case 'dl':
			case 'decilitri':
			case 'decilitro':
				$um = 'DL';	
			break;
			case 'lt':
			case 'litri':
			case 'litro':
				$um = 'LT';	
			break;
			default:
				$um = 'PZ';
			break;
		}
		
		// debug('translateArticleUm AFTER organization_id ['.$organization_id.'] um ['.$um.']');
			
		return $um;
	}
}