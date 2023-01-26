<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class ActionsOrderComponent extends CartSuperComponent {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function getGroupIdToReferente($user, $debug=false) {
		$group_id = 0;

        if($user->acl['isReferentGeneric'])
            $group_id = Configure::read('group_id_referent');
        else 
        if($user->acl['isSuperReferente'])
            $group_id = Configure::read('group_id_super_referent');
	
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
		
		$orderStates = $templatesOrdersStatesTable->find()
                        ->where($where)
                        ->order(['TemplatesOrdersStates.sort'])
                        ->all();
				
		return $orderStates;
	}

}