<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class ActionsOrderComponent extends CartSuperComponent {

    public $components = ['ActionsDesOrder'];

    private $isToValidate = false;
	private $isCartToStoreroom = false;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	/*
	 * dato un Ordine restituisce le possibili Action per il menu (flag_menu = Y) in base 
	 * 		Organization.template_id
	 * 		User.group_id    (referente, cassiere, tesoriere)
	 * 		Order.state_code (OPEN, PROCESSED-BEFORE-DELIVERY ...)
	 * 
	 * return 	
	 * 			$result => ['OrdersAction']
	 *			$result['url'] = link gia' composto
	*/	
	public function getOrderActionsToMenu($user, $group_id, $order, $debug=false) {

        $orderActions=[];
		$urlBase = Configure::read('App.server').'/administrator/index.php?option=com_cake&';
		$RoutingPrefixes = Configure::read('Routing.prefixes');
		
		/*
		 * dati ordine
		 */
        $ordersTable = TableRegistry::get('Orders');
        
        if(!is_object($order))
            $order = $ordersTable->getById($user, $user->organization->id, $order, $debug);

		if(empty($order)) {
			return $orderActions;
		}

        $order->tot_importo = $ordersTable->getTotImporto($user, $user->organization->id, $order, $debug);

		/*
		 * home order di default
		 */
	    $orderActions[0]['id'] = '0';
	    $orderActions[0]['controller'] = 'Orders';
	    $orderActions[0]['action'] = 'home';
        $orderActions[0]['label'] = 'Order home';
        $orderActions[0]['label'] = __('Order home').'<br /> - <small>'.__('Importo_totale').' '.number_format($order->tot_importo,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' €</small>';
        $orderActions[0]['label_more'] = '';
	    $orderActions[0]['css_class'] = 'actionWorkflow';
	    $orderActions[0]['img'] = '';
	    $orderActions[0]['qs'] = ['delivery_id' => $order->delivery_id, 'order_id' => $order->id];
	    $orderActions[0]['url'] = 'controller=Orders&action=home&delivery_id='.$order->delivery_id.'&order_id='.$order->id;
		$orderActions[0]['neo_url'] = '/admin/orders/home/'.$order->order_type_id.'/'.$order->id;
		 /*
		  * per i TEST
		$order->state_code = 'PROCESSED-TESORIERE';
		$order->state_code = 'WAIT-PROCESSED-TESORIERE';
		*/
		
		/*
		 * actions possibili in base a
		 * 		Organization.template_id
		 * 		User.group_id
		 * 		Order.state_code
		*/
        $templatesOrdersStatesOrdersActionsTable = TableRegistry::get('TemplatesOrdersStatesOrdersActions');
		
		$where = ['TemplatesOrdersStatesOrdersActions.template_id' => $user->organization->template_id,
                'TemplatesOrdersStatesOrdersActions.state_code' => $order->state_code,
                'TemplatesOrdersStatesOrdersActions.group_id' => $group_id,
                'OrdersActions.flag_menu' => 'Y'];

		$templatesOrdersStatesOrdersActions = $templatesOrdersStatesOrdersActionsTable->find()
                        ->contain(['OrdersActions'])
                        ->where($where)
                        ->order(['TemplatesOrdersStatesOrdersActions.sort'])
                        ->all();
		
		/*
		 * ctrl per ogni action OrdersAction.permission e OrdersAction.permission_or
		 */
		$orderActions += $this->_ctrlACLOrdersAction($user, $order, $templatesOrdersStatesOrdersActions, $debug);

		return $orderActions;
	}	

	/*
	 * ctrl per ogni action OrdersAction.permission e OrdersAction.permission_or
	*/
	private function _ctrlACLOrdersAction($user, $order, $templatesOrdersStatesOrdersActions, $debug) {
		
		//$debug = true;

		/*
		 * controlli Custom
		*/
		$this->isToValidate = $this->_orderToValidate($user, $order);
		
		$this->isCartToStoreroom = $this->_orderIsCartToStoreroom($user, $order);
		
		$orderActions = [];
		$i=1; // parto da 1 perche' orderActions[0] e' Home Order
		foreach ($templatesOrdersStatesOrdersActions as $numResult => $templatesOrdersStatesOrdersAction) {
				
			$orders_action  = $templatesOrdersStatesOrdersAction->orders_action;

			$orderActionOk = true;
			
			/* 
			 * [
			 *  {"orgHasArticlesOrder":"Y","userHasArticlesOrder":"Y"},
			 *  {"orgHasArticlesOrder":"Y","userHasArticlesOrder":"N"}
			 * ]
			 * ogni riga e' in OR => valide le condizioni 1 riga OR valide le condizioni 2 riga
			 */
			if(!empty($orders_action->permissions)) {
				$permissions = json_decode($orders_action->permissions, true);

				$totale_permessi_da_validate = count($permissions); // in OR
				$orderActionOks = [];
				foreach($permissions as $numResult => $permission) {
					$esito = false;
					$orderActionOks[$numResult] = true;
					foreach($permission as $method_name => $value_da_verificare) {
						if($orderActionOks[$numResult]) {
							$esito = $this->{$method_name}($user, $order, $value_da_verificare);
						}
							
						if(!$esito) {
							$orderActionOks[$numResult] = false;
							// break; no perche' mi blocca il foreach superiore
						}
					}
				}

				foreach($orderActionOks as $orderActionOk) { 
					if($orderActionOk) { // e' sufficiente 1 a true, perche' sono in OR
						$orderActionOk = true;
						break;
					}
				}
			}

			/*
			 * gestione precedente 
			 * PERMISSION
			 * 		sono stati soddisfatti tutti i criteri per accedere alla risorsa => faccio vedere l'url		
			if(!empty($orders_action->permission)) {
				$permission = json_decode($orders_action->permission, true);

				$esito = false;
				$orderActionOk = true;
				foreach ($permission as $method_name => $value_da_verificare) {
					if($orderActionOk) {
						$esito = $this->{$method_name}($user, $order, $value_da_verificare);
					}
						
					if(!$esito) {
						$orderActionOk = false;
						// break; no perche' mi blocca il foreach superiore
					}
				}				
			} // end if(!empty($result->permission))
				
			// PERMISSION_OR
			if(!empty($orders_action->permission_or)) {
				$permission_or = json_decode($orders_action->permission_or, true);

				$esito = false;
				$orderActionOR_Ok = true;
				foreach ($permission_or as $method_name => $value_da_verificare) {
					if($orderActionOR_Ok) {
						$esito = $this->{$method_name}($user, $order, $value_da_verificare);
					}
	
					if(!$esito) {
						$orderActionOR_Ok = false;
						// break; no perche' mi blocca il foreach superiore
					}
				}

				// se nel controllo OrdersAction.permission era true, e' valido anche qui perche' sono in OR				
				if($orderActionOk || $orderActionOR_Ok) 
					$orderActionOk = true;
				
			} // if(!empty($result->permission_or))
			*/

			if($orderActionOk) {
	
                $urlBase = Configure::read('App.server').'/administrator/index.php?option=com_cake&';

				$orderActions[$i] = $orders_action;
				$orderActions[$i]['controller'] = $orders_action->controller;
				$orderActions[$i]['action'] = $orders_action->action;				
				$orderActions[$i]['qs'] = ['delivery_id' => $order->delivery_id, 'order_id' => $order->id];
				$orderActions[$i]['url'] = $urlBase.'controller='.$orders_action->controller.'&action='.$orders_action->action.'&delivery_id='.$order->delivery_id.'&order_id='.$order->id;
				if(!empty($orders_action->neo_url)) {
					$neo_url = $orders_action->neo_url;
					$neo_url = str_replace('{order_type_id}', $order->order_type_id, $neo_url);
					$neo_url = str_replace('{delivery_id}', $order->delivery_id, $neo_url);
					$neo_url = str_replace('{order_id}', $order->id, $neo_url);
					$neo_url = str_replace('{parent_id}', $order->parent_id, $neo_url);
					
					$orderActions[$i]['neo_url'] = $neo_url;
				}

				if(!empty($orders_action->query_string)) {
						
					switch ($orders_action->query_string) {
						case 'FilterArticleOrderId':
							$orderActions[$i]['qs'] += ['FilterArticleOrderId' => $order->id];
							$orderActions[$i]['url'] .= '&FilterArticleOrderId='.$order->id;
							break;
					}
				}
				$i++;
			}
	
		}
	
		return $orderActions;
	}
	
	/*
	 * gestisco solo referente perche' ha i medesimi permessi
	 */
    public function getGroupIdToReferente($user, $debug=false) {
		$group_id = 0;

		/*
        if($user->acl['isReferentGeneric'])
            $group_id = Configure::read('group_id_referent');
        else 
        if($user->acl['isSuperReferente'])
            $group_id = Configure::read('group_id_super_referent');
		*/
		if($user->acl['isReferentGeneric'] || $user->acl['isSuperReferente'])
			$group_id = Configure::read('group_id_referent');

		return $group_id;
	}

	/*
	 * estrae gli stati dell'ordine per la legenda profilata e per gli stati del Order.sotto_menu
	 * e aggiunge gli stati del Tesoriere
	 */
	public function getOrderStatesToLegenda($user, $group_id, $debug=false) {
		
		$orderState=[];
		$orderState2=[];
		
        $templatesOrdersStatesTable = TableRegistry::get('TemplatesOrdersStates');
		
		$where = ['TemplatesOrdersStates.template_id' => $user->organization->template_id,
                    'TemplatesOrdersStates.group_id' => $group_id,
                    'TemplatesOrdersStates.flag_menu' => 'Y'];
	
		$templatesOrdersStates = $templatesOrdersStatesTable->find()
                        ->where($where)
                        ->order(['TemplatesOrdersStates.sort'])
                        ->all();
				
		return $templatesOrdersStates;
	}

	/*
	 * metodi che controllano i permessi
	 * da OrdersAction.permission ho l'elenco dei metodi da controllare
	 */
	
	/*
	 * Controlli su Organization
	 */
	private function orgHasPayToDelivery($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->template->payToDelivery==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orgHasTemplateOrderForceClose($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($user->organization->template->orderForceClose==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
		
	private function orgHasArticlesOrder($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($user->organization->paramsConfig['hasArticlesOrder']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
		
		return $esito;
	}
	
	private function orgHasValidate($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasValidate']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}

	private function orgHasTrasport($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasTrasport']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}

	private function orgHasCostMore($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasCostMore']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orgHasCostLess($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasCostLess']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orgHasStoreroom($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasStoreroom']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orgHasDes($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($user->organization->paramsConfig['hasDes']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
		
		return $esito;			
	}

	private function orgHasOrdersGdxp($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($user->organization->paramsConfig['hasOrdersGdxp']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
		
		return $esito;			
	}

	private function orgHasGasGroups($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($user->organization->paramsConfig['hasGasGroups']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
		
		return $esito;
	}

	/*
	 * Controlli su User
	*/
	private function userHasArticlesOrder($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($user->organization->paramsConfig['hasArticlesOrder']==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	/*
	 * Controlli su Ordine del e' un ordine condiviso 
	*/
	private function orderIsDes($user, $order, $value_da_verificare) {
		$esito = false;
		
		$desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');
		
		$where = [// 'DesOrdersOrganization.des_id' => $user->des_id,  potrebbe non averlo valorizzato
                'DesOrdersOrganizations.organization_id' => $order->organization_id,
                'DesOrdersOrganizations.order_id' => $order->id];
		$desOrdersOrganization = $desOrdersOrganizationsTable->find()->where($where)->first();
		
        if(empty($desOrdersOrganization)) {
			if($value_da_verificare=='N')
				$esito = true;
			else
				$esito = false;
		}
		else {
			if($value_da_verificare=='Y')
				$esito = true;
			else
				$esito = false;		
		}

		return $esito;
	}

	private function orderHasTrasport($user, $order, $value_da_verificare) {
		$esito = false;
	
		if($order->hasTrasport==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}


	private function orderHasCostMore($user, $order, $value_da_verificare) {
		$esito = false;
	
		if($order->hasCostMore==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orderHasCostLess($user, $order, $value_da_verificare) {
		$esito = false;
	
		if($order->hasCostLess==$value_da_verificare)
			$esito = true;
		else
			$esito = false;
	
		return $esito;
	}
	
	private function orderTypeGest($user, $order, $value_da_verificare) {
		$esito = false;
	
		if($order->typeGest==$value_da_verificare)
			return true;
		else
			return false;
	}
	
	/*
	 * Controlli su Custom
	*/
	private function isTitolareDesSupplier($user, $order, $value_da_verificare) {
	
		$isTitolareDesSupplier = "N";
		
		/*
		 * estraggo des_supplier_id per sapere se e' titolare
		 */
        $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');

		$where = [// 'DesOrdersOrganization.des_id' => $user->des_id,  potrebbe non averlo valorizzato
                'DesOrdersOrganization.organization_id' => $order->organization_id,
                'DesOrdersOrganization.order_id' => $order->id];
		$options['recursive'] = 1;								
		$desOrdersOrganizationResults = $desOrdersOrganizationsTable->find()
                                    ->contains(['DesOrders'])->where($where)->first();
		
		if(!empty($desOrdersOrganizationResults)) {
			$des_id = $desOrdersOrganizationResults->des_order->des_id;
			$user->des_id = $des_id;
			
			if($this->ActionsDesOrder->isTitolareDesSupplier($user, $desOrdersOrganizationResults, $value_da_verificare))
				$isTitolareDesSupplier = "Y";	
		}
	
		if($isTitolareDesSupplier==$value_da_verificare)
			return true;
		else
			return false;
	}
	
	private function isToValidate($user, $results, $value_da_verificare) {
		$esito = false;
	
		if($this->isToValidate==$value_da_verificare)
			return true;
		else
			return false;
	}
	
	private function isCartToStoreroom($user, $results, $value_da_verificare) {
		$esito = false;
		
		if($this->isCartToStoreroom==$value_da_verificare)
			return true;
		else 
			return false;
	}

	/*
	 * controllo la tipologia di ordine
		Configure::write('Order.type.gas', 1);
		Configure::write('Order.type.des', 2);
		Configure::write('Order.type.des_titolare', 3);
		Configure::write('Order.type.promotion', 4);
		Configure::write('Order.type.pact_pre', 5); 
		Configure::write('Order.type.pact', 6);  
		Configure::write('Order.type.supplier', 7);
		Configure::write('Order.type.promotion_gas_users', 8);
		Configure::write('Order.type.socialmarket', 9);
		Configure::write('Order.type.gas_groups', 10);
		Configure::write('Order.type.gas_parent_groups', 11);
	*/
	private function isOrderTypes($user, $order, $value_da_verificare) {
		$esito = false;

		$order_type_ids = explode(',', $value_da_verificare);		
		if(in_array($order->order_type_id, $order_type_ids))
			$esito = true;
		else
			$esito = false;
		return $esito;
	}

	private function isProdGasPromotion($user, $order, $value_da_verificare) {
		$esito = false;
		$isProdGasPromotion = "N";

		if($order->prod_gas_promotion_id>0)
			$isProdGasPromotion = "Y";

		// echo '<br />prod_gas_promotion_id '.$results['Order']['prod_gas_promotion_id'].' - value_da_verificare '.$value_da_verificare;
		
		if($value_da_verificare==$isProdGasPromotion)
				$esito = true;
			else
				$esito = false;			
			
		return $esito;
	}
		
	/*
     * SUPPLIER / REFERENT / DES
	 */	 
	private function articlesOwner($user, $results, $value_da_verificare) {
		$esito = false;
		$articlesOwner = 'REFERENT';

        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

		$where = ['SuppliersOrganizations.organization_id' => $user->organization->id,
				   'SuppliersOrganizations.id' => $results->supplier_organization_id];
		$suppliersOrganizationResults = $suppliersOrganizationsTable->find()
                        ->select(['SuppliersOrganizations.owner_articles'])
                        ->where($where)
                        ->first();
		if(!empty($suppliersOrganizationResults)) {
			// REFERENT o REFERENT-TMP
			$articlesOwner = $suppliersOrganizationResults->owner_articles;
			if($articlesOwner=='REFERENT-TMP')
				$articlesOwner = 'REFERENT';
		}
		
		if($value_da_verificare==$articlesOwner)
				$esito = true;
			else
				$esito = false;			
			
		return $esito;
	}
	
	/*
	 *  D.E.S.
	 *  dal DesOrdersOrganization estraggo des_supplier_id
	*/
	private function _isTitolareDesSupplier($user, $results) {
		
		$isTitolareDesSupplier = false;
		
        $desOrdersOrganizationsTable = TableRegistry::get('DesOrdersOrganizations');

		$where = [// 'DesOrdersOrganizations.des_id' => $user->des_id,  potrebbe non averlo valorizzato
                    'DesOrdersOrganizations.organization_id' => $results->organization_id,
                    'DesOrdersOrganizations.order_id' => $results->id];
		$desOrdersOrganizationResults = $desOrdersOrganizationsTable->find()->where($where)->first();

		if(!empty($desOrdersOrganizationResults)) {
			$des_id = $desOrdersOrganizationResults->des_id;
			$user->des_id = $des_id;
			
			if($this->ActionsDesOrder->isTitolareDesSupplier($user, $desOrdersOrganizationResults))
				$isTitolareDesSupplier = true;	
		}
		
		return $isTitolareDesSupplier;
	}
	
	/*
	 * gestione dei colli (pezzi_confezione)
	 */
	private function _orderToValidate($user, $order) {
		
        $ordersTable = TableRegistry::get('Orders');
			
		if($ordersTable->isOrderToValidate($user, $order->id))
			$isToValidate = true;
		else
			$isToValidate = false;

		return $isToValidate;
	}

	/*
	 *  Storeroom, cerco eventuali articoli nel carrello dell'utente Dispensa,
	*
	*  se Order.state_code == PROCESSED-POST-DELIVERY / INCOMING-ORDER (merce arrivata) / PROCESSED-ON-DELIVERY 
	*  li copio dal carrello alla dispensa con cron
	*/
	private function _orderIsCartToStoreroom($user, $results, $debug=false) {
	
		$isCartToStoreroom = false;
		if($user->organization->paramsConfig['hasStoreroom']=='Y' && 
			($results->state_code=='PROCESSED-POST-DELIVERY' || 
			$results->state_code=='INCOMING-ORDER' || 
			$results->state_code=='PROCESSED-ON-DELIVERY')) {
				
            $storeroomsTable = TableRegistry::get('Storerooms');
			
			$storeroomResults = $storeroomsTable->getCartsToStoreroom($user, $results->id, $debug);
		
			if(count($storeroomResults)>0) $isCartToStoreroom = true;
		}

		return $isCartToStoreroom;
	}
	
	/*
	 * creo degli OrderActions di raggruppamento per controller (Orders, Carts, etc)
	 * 
	 * per Order::home e Order_home_simple
	*/
	public function getRaggruppamentoOrderActions($orderActions, $debug=false) {
		
		// $debug = true;
		
		$raggruppamentoDefault['Orders']['label'] = 'Edit Order';
		$raggruppamentoDefault['Orders']['img'] = 'modulo.jpg';
		$raggruppamentoDefault['ArticlesOrders']['label'] = 'Edit ArticlesOrder Short';
		$raggruppamentoDefault['ArticlesOrders']['img'] = 'legno-frutta-cassetta.jpg';
		$raggruppamentoDefault['Carts']['label'] = 'Gestisci gli acquisti';
		$raggruppamentoDefault['Carts']['img'] = 'legno-bancone.jpg';
		$raggruppamentoDefault['Docs']['label'] = 'Gestisci le stampe';
		$raggruppamentoDefault['Docs']['img'] = 'lista.jpg';
		$raggruppamentoDefault['Referente']['label'] = 'Gestisci la merce';
		$raggruppamentoDefault['Referente']['img'] = 'legno-frutta-cassetta.jpg';
		
		$raggruppamentoOrderActions = [];

		if(count($orderActions)==1)
			return $raggruppamentoOrderActions;
		
		$controller_old='';
		$tot_figli=0;
		$i=0;
		foreach($orderActions as $orderAction) {
		
			if($debug) debug($controller_old.' '.$orderAction->controller);
		
			if(empty($controller_old) || $controller_old==$orderAction->controller) {
				$tot_figli++;
		
				if($debug) debug('A) Per il controller '.$orderAction->controller.' finora trovati '.$tot_figli.' figli');
			}
			else {
				$raggruppamentoOrderActions[$i]['controller'] = $controller_old;
				$raggruppamentoOrderActions[$i]['tot_figli'] = $tot_figli;
				if(isset($raggruppamentoDefault[$controller_old])) {
					$raggruppamentoOrderActions[$i]['label'] = $raggruppamentoDefault[$controller_old]['label'];
					$raggruppamentoOrderActions[$i]['img'] = $raggruppamentoDefault[$controller_old]['img'];
				}
				else {
					$raggruppamentoOrderActions[$i]['label'] = '';
					$raggruppamentoOrderActions[$i]['img'] = '';					
				}
				
				$i++;
				$tot_figli = 1;
				
				if($debug) debug('B) Per il controller '.$orderAction->controller.' finora trovati '.$tot_figli.' figli');
			}
		
			$controller_old = $orderAction->controller;
		} // foreach($orderActions as $orderAction)
			
		$raggruppamentoOrderActions[$i]['controller'] = $controller_old;
		$raggruppamentoOrderActions[$i]['tot_figli'] = $tot_figli;
		if(isset($raggruppamentoDefault[$controller_old])) {
			$raggruppamentoOrderActions[$i]['label'] = $raggruppamentoDefault[$controller_old]['label'];
			$raggruppamentoOrderActions[$i]['img'] = $raggruppamentoDefault[$controller_old]['img'];
		}
		else {
			$raggruppamentoOrderActions[$i]['label'] = '';
			$raggruppamentoOrderActions[$i]['img'] = '';
		}
							
		return $raggruppamentoOrderActions;
	}
	
}