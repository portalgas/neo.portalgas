<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Traits;

class TotalComponent extends Component {

	use Traits\SqlTrait;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	/*
	 * se autenticato creo l'oggetto user ma persistere in Session
	 *
	 * $organization_id = gas scelto o gas dello user
	 */
	public function totUsers($user, $where, $debug=false) {

		$model = TableRegistry::get('Users');
        $results = $this->getCount($model, $where, $debug);

		// debug($results);
		
		return $results;
	}


	public function totOrdersByYear($user, $organization_id, $year, $where=[], $debug=false) {

		$ordersTable = TableRegistry::get('Orders');
		
		$where = ['Orders.organization_id' => $organization_id];
		
		/*
		 * Se e' l'anno corrente prendo anche le consegne "da definire" 
		 */            
        if($year==date('Y')) {
        	$where += ['OR' => [
	        					['Deliveries.data >=' => $year.'-01-01', 
	        					 'Deliveries.data <=' => $year.'-12-31'],
	        					['Deliveries.sys' => 'Y']
        					]
        			  ];
        }
        else
        	$where += ['Deliveries.data >=' => $year.'-01-01', 'Deliveries.data <=' => $year.'-12-31'];
        if($debug) debug($where);
        
        $tot_orders = $ordersTable->find()
        			->contain(['Deliveries'])
                    ->where($where)
					->count();

		/*
	 	 *  storico in statistiche
	 	 */
		$statOrdersTable = TableRegistry::get('StatOrders');
		
		$where = ['StatOrders.organization_id' => $organization_id];

        if(!empty($year)) 
        	$where += ['StatDeliveries.data >=' => $year.'-01-01', 
                       'StatDeliveries.data <=' => $year.'-12-31'];
        if($debug) debug($where);
        
        $tot_stat_orders = $statOrdersTable->find()
        			->contain(['StatDeliveries'])
                    ->where($where)
					->count();

		/* 
		 * Backup se eliminato ordine
		 */
		$backupOrdersOrdersTable = TableRegistry::get('BackupOrdersOrders');
		
		$where = ['BackupOrdersOrders.organization_id' => $organization_id];
		if(!empty($year)) 
        	$where += ['BackupOrdersOrders.data_inizio >=' => $year.'-01-01', 
        			   'BackupOrdersOrders.data_inizio <=' => $year.'-12-31'];
        if($debug) debug($where);
        
        $tot_backuo_orders = $backupOrdersOrdersTable->find()
                    ->where($where)
					->count();

		$tot = ($tot_orders + $tot_stat_orders + $tot_backuo_orders);
		if($debug) debug($tot);

		return $tot;

	}

	public function totSuppliersOrganizations($user, $organization_id, $where=[], $debug=false) {

		$suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
		
		$where = ['SuppliersOrganizations.organization_id' => $organization_id];
        if($debug) debug($where);
        
        $tot = $suppliersOrganizationsTable->find()
                    ->where($where)
					->count();

		if($debug) debug($tot);

		return $tot;
	}

	public function totArticlesOrganizations($user, $organization_id, $where=[], $debug=false) {

		$articlesTable = TableRegistry::get('Articles');
		
		$where = ['Articles.organization_id' => $organization_id];
        if($debug) debug($where);
        
        $tot = $articlesTable->find()
                    ->where($where)
					->count();

		if($debug) debug($tot);

		return $tot;
	}

	
}