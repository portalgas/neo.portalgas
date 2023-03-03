<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class HtmlMenusController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('ActionsOrder');
        $this->loadComponent('ActionsDesOrder');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }        
    }
  
    /* 
     * $view
     *      modal (se visualizzato nel modal del menu' ordini)
     *      menu (se visualizzato nel menu' di sinistra)
     */
    public function order($order_type_id, $order_id, $view='modal') {

        $debug = false;
        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $ordersTable = TableRegistry::get('Orders');

        $ordersTable = $ordersTable->factory($this->_user, $this->_organization->id, $order_type_id);
        $ordersTable->addBehavior('Orders');
        
        $order = $ordersTable->getById($this->_user, $this->_organization->id, $order_id);

		$group_id = $this->ActionsOrder->getGroupIdToReferente($this->_user);
		$orderActions = $this->ActionsOrder->getOrderActionsToMenu($this->_user, $group_id, $order, $debug);
		$this->set('orderActions', $orderActions);

        $templatesOrdersStates = $this->ActionsOrder->getOrderStatesToLegenda($this->_user, $group_id, $debug);
		$this->set('templatesOrdersStates', $templatesOrdersStates);

        /*
         * D.E.S.
         */
		$desResults = $this->ActionsDesOrder->getDesOrderData($this->_user, $order->id, $debug);
		$des_order_id = $desResults['des_order_id'];
		$isTitolareDesSupplier = $desResults['isTitolareDesSupplier'];
		$this->set('des_order_id',$des_order_id);
		$this->set('isTitolareDesSupplier', $isTitolareDesSupplier);
		$this->set('desOrdersResults', $desResults['desOrdersResults']);
		$this->set('summaryDesOrderResults', $desResults['summaryDesOrderResults']);
        
		/*
         *  ctrl se e' una promozione
		 */
		if(!empty($order->prod_gas_promotion_id)) {
			
		}

		/*
         *  ctrl se e' un ordine per gruppi
		 */
		if(!empty($order->gas_group_id)) {
			$gasGroupsTable = TableRegistry::get('GasGroups');
			$gasGroup = $gasGroupsTable->getsById($this->_user, $this->_organization->id, $order->gas_group_id);
			$this->set(compact('gasGroup'));
		}
			

		/*
		 * $pageCurrent = ['controller' => '', 'action' => ''];
		 * mi serve per non rendere cliccabile il link corrente nel menu laterale
		$pageCurrent = $this->getToUrlControllerAction($_SERVER['HTTP_REFERER']);
		$this->set('pageCurrent', $pageCurrent);
		*/

        $this->set(compact('order'));

        $this->set('position_img', 'bgLeft');

        $this->viewBuilder()->setLayout('ajax');

        // view di default /Admin/Api/HtmlMenus/order
        switch($view) {
            case 'modal':
                $this->viewBuilder()->setTemplate('/Admin/Api/HtmlMenus/order_modal');
            break;
            case 'menu':
                $this->viewBuilder()->setTemplate('/Admin/Api/HtmlMenus/order_menu');
            break;    
        }
    } 
}