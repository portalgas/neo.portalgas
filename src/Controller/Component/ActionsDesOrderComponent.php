<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class ActionsDesOrderComponent extends CartSuperComponent {

    private $isToValidate = false;
	private $isCartToStoreroom = false;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	/*
	 * richiamato per ogni ordine per sapere se e' DES
	 */
	public function getDesOrderData($user, $order_id, $debug=false) {
		
		$results = [];
		
		$isTitolareDesSupplier = false;
		$des_order_id = 0;
		$results['des_order_id'] = 0;
		$results['desOrdersResults'] = [];
		$results['summaryDesOrderResults'] = [];
		
		if($user->organization->paramsConfig['hasDes'] == 'Y') {

			$desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');

			$desOrdersOrganizationResults = $desOrdersOrganizationsTable->getDesOrdersOrganization($user, $order_id, $debug);
			
			if (!empty($desOrdersOrganizationResults)) {

				$des_order_id = $desOrdersOrganizationResults->des_order_id;
				
				if (!empty($des_order_id)) {
					$isTitolareDesSupplier = $this->isTitolareDesSupplier($user, $desOrdersOrganizationResults->des_order);
			
					$desOrdersTable = TableRegistry::get('DesOrders');
			
					$desOrdersResults = $desOrdersTable->getDesOrder($user, $des_order_id, $debug);
					
					/*
					 * ctrl eventuali occorrenze di SummaryDesOrder
					 */
					$summaryDesOrdersTable = TableRegistry::get('SummaryDesOrders');
					$summaryDesOrderResults = $summaryDesOrdersTable->select_to_des_order($user, $des_order_id, $user->organization->id);
				
					$results['desOrdersResults'] = $desOrdersResults;
					$results['summaryDesOrderResults'] = $summaryDesOrderResults;
				}
			} // end if (!empty($desOrdersOrganizationResults))
					
		} // DES

		$results['isTitolareDesSupplier'] = $isTitolareDesSupplier;
		$results['des_order_id'] = $des_order_id;
		
		return $results;
	}

	/*
	 * isTitolareDesSupplier: lo user e' titolare del produttore
	 * results = $results['DesOrder']['des_supplier_id']
	 * results = $des_order_id	
	 */
	public function isTitolareDesSupplier($user, $des_order, $value_da_verificare=true) {
	
		$debug = false;
		
		$esitoIsTitolareDesSupplier = false;
		
		/*
		* ctrl se lo user e' nel gruppo Configure::read('group_id_titolare_des_supplier')
		*/
		if($user->id == 0 || !$user->acl['isTitolareDesSupplier']) 
			$esitoIsTitolareDesSupplier = false;
		else {
			$desSuppliersReferentsTable = TableRegistry::get('DesSuppliersReferents');
			
			$where = ['DesSuppliersReferents.des_id' => $user->des_id,
					'DesSuppliersReferents.organization_id' => $user->organization->id,
					'DesSuppliersReferents.user_id' => $user->id,
					'DesSuppliersReferents.group_id' => Configure::read('group_id_titolare_des_supplier'),
					'DesSuppliers.des_id' => $user->des_id,
					'DesSuppliers.own_organization_id' => $user->organization->id,
					'DesSuppliers.id' => $des_order->des_supplier_id];
			$totali = $desSuppliersReferentsTable->find()->contain(['DesSuppliers'])->where($where)->all();
			$totali = $totali->count();
			
			if($totali==0)
				$esitoIsTitolareDesSupplier = false;
			else 
				$esitoIsTitolareDesSupplier = true;
		}
		
		if($esitoIsTitolareDesSupplier==$value_da_verificare) {
			// Log::debug('isTitolareDesSupplier() esito SI - totali '.$totali);
			return true;
		}	
		else {
			// Log::debug('isTitolareDesSupplier() esito NO - totali '.$totali);
			return false;
		}	
	}	
}