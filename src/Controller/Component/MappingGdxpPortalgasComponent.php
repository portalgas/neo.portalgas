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
			/*
			 * creazione Supplier
			 */
			$vatNumber = $datas['piva'];

			$suppliersTable = TableRegistry::get('Suppliers');

			$where = ['Suppliers.piva' => $vatNumber];
	        $supplier = $suppliersTable->find()
			            ->where($where)
						->first();

			if(!empty($supplier))  {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierExists - supplier exist with Suppliers.piva= ['.$vatNumber.'] => not insert';
		        $action = false; // not insert
		        $results = $supplier;

		        $esito = $this->createSupplierAccount($supplier, $organization_id);
			}
			else {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierExists - supplier not exist with Suppliers.piva = ['.$vatNumber.'] => insert';
		        $action = true; // insert

		        /*
		         * createSupplierAccount() richiamato da afterSave
		         */
			}
		}

		$results = ['action' => $action, 'esito' => $esito, 'code' => $code, 'msg' => $msg, 'results' => $results];

		return $results;
	}

	/*
	 * creo se non esiste un account da produttore:
	 *	 Organization e un SuppliersOrganization associato (quello che gestirà il listino)
	 *	 in supplierOrganizationsExists creero' un SuppliersOrganization per il GAS (non gestira' il listino)
	 */
	public function createSupplierAccount($supplier, $organization_id) {

		$debug = false;

        $esito = true;
        $action = false;
        $code = 200;
        $msg = '';
        $results = [];

		if($debug) debug($supplier);

		// ctrl se gia' non esiste
		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
		// gia' non associato $suppliersOrganizationsTable->organizations->removeBehavior('OrganizationsParams');

		$where = ['SuppliersOrganizations.supplier_id' => $supplier->id,
				  'SuppliersOrganizations.owner_articles' => 'REFERENT'];
        $suppliersOrganization = $suppliersOrganizationsTable->find()
        			->contain(['Organizations' => ['conditions' => ['type' => 'PRODGAS']]])
		            ->where($where)
					->first();
		if($debug) debug($where);
		if($debug) debug($suppliersOrganization);

		if(empty($suppliersOrganization)) {

			/*
			 * prima creo eventuale Organization per ottenere organization_id
			 */
			$organizationTable = TableRegistry::get('Organizations');

			/* 
			 * ctrl se gia' esiste ma non puo' essere
			 */
			$organizationTable = $organizationTable->factory(Configure::read('Organization.type.prodgas'));	
			$where = [$organizationTable->getAlias().'.name' => $supplier->name];
    		$organizations = $organizationTable->gets($where);	
    		// debug($organizations);
    		if($organizations->count()==0) {
			 	$results = $organizationTable->create($supplier);
				if(!$results['esito']) {
			        $esito = $results['esito'];
			        $code = $results['code'];
			        $msg = $results['msg'];
			        $results = $results['results'];

			        debug($results); exit;
				}   
				else {
					$organization = $results['results'];
				}
    		} // if($organizations->count()==0)
    		else {
    			$organization = $organizations->first();
    		}

			if($esito) {
				
				if($debug) debug($organization);

				/*
				 * creo SuppliersOrganization per il produttore (Organization.type = PRODGAS)
				 */				
				$data_override = [];
				$data_override['owner_articles'] = 'REFERENT';
		        // $data_override['owner_supplier_organization_id'] una volta creato lo associa con l'id creato
		        $data_override['owner_organization_id'] = $organization->id;
				$results = $suppliersOrganizationsTable->create($organization->id, $supplier, $data_override);
				if(!$results['esito']) {
			        $esito = $results['esito'];
			        $code = $results['code'];
			        $msg = $results['msg'];
			        $results = $results['results'];

			        debug($results); exit;
				}
				else {
			        $esito = true;
			        $code = 200;
		
			        /*
			         * aggiorno Supplier.owner_organization_id
			         */
			        $suppliersTable = TableRegistry::get('Suppliers');

			        $data = [];
			        $data['owner_organization_id'] = $organization->id;
	                $entity = $suppliersTable->patchEntity($supplier, $data);
	                // debug($entity);
	                if (!$suppliersTable->save($entity)) {
	                    $esito = false;
	                    $code = '500';
	                    $msg = '';
	                    $results = $entity->getErrors();             
	                }  

				}	 
			}
		} // end if(empty($suppliersOrganization))

		$results = ['esito' => $esito, 'code' => $code, 'msg' => $msg, 'results' => $results];

		return $results;		
	}

	/*
	 * definito in queue_tables. SupplierOrganizations.before_save
	 * salto _save se $action = false
	 * 
	 * dopo il salvataggio o se esiste gia' => aggiorno 
	 * 	owner_articles da REFERENT a SUPPLIER
	 *  owner_supplier_organization_id a id del produttore
     *  owner_organization_id a organization_id del produttore
	 */
	public function supplierOrganizationsExists($datas, $organization_id) {

		$debug = false;
        $esito = true;
        $action = false;
        $code = 200;
        $msg = '';
        $results = [];

        $suppliersTable = TableRegistry::get('Suppliers'); 

		$supplier = null;

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
	        $suppliersOrganization = $suppliersOrganizationsTable->find()
			            ->where($where)
						->first();
			if($debug) debug($where);
			if($debug) debug($suppliersOrganization);

			if(!empty($suppliersOrganization))  {
		        $esito = true;
		        $code = 200;
		        $msg = 'supplierOrganizationsExists - supplier exist with supplier_id = ['.$supplier_id.'], organization_id = ['.$organization_id.'] => not insert';
		        $action = false;
			}
			else {

				/*
				 * creo SuppliersOrganizations ma il listino lo gestisce il produttore
				 */
		        $supplier = $suppliersTable->find()
		        						->contain(['SuppliersOrganizations' =>  
		        								  	['Organizations' => ['conditions' => ['type' => 'PRODGAS']]]])
							            ->where(['Suppliers.id' => $supplier_id])
										->first();
				// debug($supplier);
				$data_override = [];
				$data_override['owner_articles'] = 'SUPPLIER';
		        $data_override['owner_organization_id'] = $supplier->owner_organization_id;
		        $data_override['owner_supplier_organization_id'] = $supplier->suppliers_organizations[0]->id;
				// debug($data_override);

				$results = $suppliersOrganizationsTable->create($organization_id, $supplier, $data_override);	
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

	/*
	 * estraggo SupplierOrganization del produttore, e' lui che gestisce il listino articoli
	 */
	public function getSupplierOrganizationIdByVatNumber($organization_id, $vatNumber) {

        $supplier_organization_id = 0;

		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

		$where = ['Suppliers.piva' => $vatNumber,
				  'SuppliersOrganizations.organization_id' => $organization_id];
        $results = $suppliersOrganizationsTable->find()
        						->contain(['Suppliers'])
					            ->where($where)
								->first();
		// debug($where);
	    // debug($results);
		if(!empty($results)) {
			$supplier_organization_id = $results->owner_supplier_organization_id;		
		}

		return $supplier_organization_id;
	}

	/*
	 * estraggo SupplierOrganization del produttore, e' lui che gestisce il listino articoli
	 */
	public function getSupplierOrganizationOrganizationIdByVatNumber($organization_id, $vatNumber) {

		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

		$where = ['Suppliers.piva' => $vatNumber,
				  'SuppliersOrganizations.organization_id' => $organization_id];
        $results = $suppliersOrganizationsTable->find()
        						->contain(['Suppliers'])
					            ->where($where)
								->first();
		// debug($where);
		// debug($results);								
		if(!empty($results)) {
			$organization_id = $results->owner_organization_id;		
		}

		return $organization_id;
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