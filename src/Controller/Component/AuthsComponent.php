<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use Authentication\AuthenticationService;
use App\Traits;

class AuthsComponent extends Component {

    use Traits\SqlTrait;
    use Traits\UtilTrait;

    private $_order_type_ids = [];  // tipologie d'ordine abilitate

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();

        /* 
         * gestisco solo gruppi
         */
        $this->_order_type_ids = [
            Configure::read('Order.type.gas'),
            Configure::read('Order.type.des'),
            Configure::read('Order.type.des_titolare'),
            Configure::read('Order.type.gas_parent_groups'),
            Configure::read('Order.type.gas_groups')
        ];        
    }

    /*
     * verifica se un utente ha la gestione degli articoli sugli ordini
     * dipende da 
     *      - Organization.hasArticlesOrder
     *      - User.hasArticlesOrder
     * 
     * anche in AppHelper, AppModel
     */ 
    public function isUserPermissionArticlesOrder($user) {
        if (isset($user) && $user->organization->paramsConfig['hasArticlesOrder'] == 'Y' && $user->user->hasArticlesOrder == 'Y')
            return true;
        else
            return false;
    }

    /*
     * ctrl se la rotta e' abilitata
     */
    public function ctrlRoute($user, $request) {
        $results = [];

        $controller = strtolower($request->controller); 
        $action = strtolower($request->action); 
        $pass = $request->pass;
        
        switch($controller) {
            case 'orders':
            case 'articles-orders':
                if(empty($pass) || !isset($pass[0])) {
                    $results['msg'] = __('msg_error_param_order_type_id');
                    return $results;
                }    

                $order_type_id = $pass[0];

                if(!in_array($order_type_id, $this->_order_type_ids)) {
                    $results['msg'] = __('msg_error_param_order_type_id');
                    return $results;   
                }

                /* 
                * order_type_id 
                * se Configure::read('Order.type.gas_groups'); 
                * ctrl che lo user abbia creato un gruppo
                */
                switch($order_type_id) {
                    case Configure::read('Order.type.gas'):
                        if(!$user->acl['isReferentGeneric']  &&
                        !$user->acl['isSuperReferente']) { 
                            $results['msg'] = __('msg_not_permission');
                            return $results;
                        }             
                    break;
                    case Configure::read('Order.type.gas_groups'):
                    case Configure::read('Order.type.gas_parent_groups'):
                        if($user->organization->paramsConfig['hasGasGroups']=='N' || (
                            !$user->acl['isGasGroupsManagerParentOrders']  && 
                            !$user->acl['isGasGroupsManagerOrders'])) { 
                                $results['msg'] = __('msg_not_permission');
                                return $results;
                        } 
            
                        if($order_type_id==Configure::read('Order.type.gas_groups')) {
                            $gasGroupsTable = TableRegistry::get('GasGroups');
                            $gasGroups = $gasGroupsTable->findMyLists($user, $user->organization->id, $user->id);
                            if($gasGroups->count()==0) {
                                $results['msg'] = __('msg_not_permission');
                                $results['redirect'] = ['controller' => 'GasGroups', 'action' => 'index'];
                                return $results;
                            }    
                        }
                    break;
                    case Configure::read('Order.type.des'):
                    case Configure::read('Order.type.des_titolare'):
                        if($user->organization->paramsConfig['hasDes']=='N' || 
                        !$user->acl['isDes']) {
                            $results['msg'] = __('msg_not_permission');
                            return $results;
                        } 
                    break;
                }
            break;
        }
       
        return $results;
    }    
}