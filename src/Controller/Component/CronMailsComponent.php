<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Traits;
Use Cake\Mailer\Email;
use Cake\I18n\Time;

class CronMailsComponent extends Component {

    use Traits\SqlTrait;
    use Traits\UtilTrait;

    private $_from; // info@portalgas.it
    private $_debug = false; // se true invio 1 email a francesco.actis@gmail.com

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();

        date_default_timezone_set('Europe/Rome');
        $this->_from = Configure::read('SOC.mail');
    }

    /*
     * $debug = true perche' quando e' richiamato dal Cron deve scrivere sul file di log
     * invio mail 
     *      ordini che si aprono oggi
     *      ctrl data_inizio con data_oggi
     *      mail_open_send = Y (perche' in Order::add data_inizio = data_oggi)
     */    
    public function mailUsersOrdersOpen($organization_id, $debug=false) {

        $_user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($_user)) 
            return; 

        if($debug) echo "Estraggo gli ordini che apriranno tra ".(Configure::read('GGMailToAlertOrderOpen')+1)." giorni o con mail_open_send = Y \n";
 
        /*
        * estraggo ordini
        */
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $_user->organization->id,
                  'Orders.isVisibleFrontEnd' => 'Y',     
                  'Orders.order_type_id NOT IN ' => [Configure::read('Order.type.gas_parent_groups')],     
                  'Orders.state_code NOT IN ' => ['CREATE-INCOMPLETE', 'CLOSE'],
                  'OR' =>
                    [
                        'Orders.data_inizio = CURDATE() - INTERVAL '.Configure::read('GGMailToAlertOrderOpen').' DAY',
                        // 'Orders.data_inizio' => 'CURDATE() - INTERVAL '.Configure::read('GGMailToAlertOrderOpen').' DAY',
                        'Orders.mail_open_send' => 'Y'
                    ]];
        // debug($where);          
        $orders = $ordersTable->find()
                            ->contain(['Deliveries'  => 
                                ['conditions' => ['Deliveries.isVisibleFrontEnd' => 'Y']],  
                            'SuppliersOrganizations' => 
                                ['conditions' => ['SuppliersOrganizations.stato' => 'Y',
                                                  'SuppliersOrganizations.mail_order_open' => 'Y'],
                                                  'Suppliers']])
                            ->where($where)
                            ->order(['Deliveries.data', 'Suppliers.name'])
                            ->all();
                            
        if($orders->count()==0) {
            if($debug) echo "non ci sono ordini che apriranno tra ".(Configure::read('GGMailToAlertOrderOpen')+1)." giorni \n";
            return false;
        }

        if($debug) echo "Trovati ".$orders->count()." ordini \n";

        /*
        * estraggo tutti gli UTENTI, per ogni ordine verifico che 
        *   e' tra quelli bookmarks_mails
        *   e' un ordine GasGroups, lo user fa parte del gruppo
        */
        $usersTable = TableRegistry::get('Users');
        $where = ['username NOT LIKE' => '%portalgas.it'];    
        // debug barbara.pergreffi@alice.it
        // $where = ['id' => 6798]; 

        $users = $usersTable->gets($_user, $_user->organization->id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }

        foreach($users as $numResult => $user) {
           
            if($this->_debug) {
                if($numResult==1)
                    break;
            };

            if(empty($user->email))
                continue;

            $to = $user->email;
            $addTo = '';
            if(isset($user->user_profiles['email']) && !empty($user->user_profiles['email'])) 
                $addTo = $user->user_profiles['email'];

            if($this->_debug) {
                $to = 'francesco.actis@gmail.com';
                $addTo = 'francesco.actis@gmail.com';    
            }

            $user_orders = $this->_getAclUserOrders('MailUsersOrdersOpen', $_user, $organization_id, $user, $orders); // array con gli ordine dell'utente
            if(empty($user_orders)) 
                continue;

            /*
             * subject
             */
            if(count($user_orders)==1) 
                $subject = trim(current($user_orders)->suppliers_organization->name).", ordine che si apre oggi";
            else 
                $subject = trim($_user->organization->name).", ordini che si aprono oggi";												

            $email = new Email();
            if($this->_debug) 
                $email->setTransport('aws-prod');
            else
                $email->setTransport('aws');
            $email->viewBuilder()->setHelpers(['Html', 'Text'])
                                   ->setTemplate('users_orders_open', 'default'); // template / layout
            $email->setEmailFormat('html')
                    ->setFrom($this->_from);
            $email->setViewVars(['orders' => $user_orders,
                                 'user' => $user,
                                'organization' => $_user->organization]);
                       
            try {
                $results = $email
                                ->setSubject($subject) 
                                ->setTo($to);
                if(!empty($addTo)) 
                    $email->addTo($addTo);
                $email->send('');

                Log::debug('mailUsersOrdersOpen send to '.$to.' '.$subject);
            } catch (\Exception $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
                Log::error('mailUsersOrdersOpen');
                Log::error('Exception : ',  $e->getMessage());
            }  

        } // foreach($users as $user)

        /*
        * per gli ordini trovati 
        * UPDATE Order.mail_open_send, Order.mail_open_data
        */        
        foreach($orders as $order) {
            
            $order_update = $ordersTable->find()
                        ->where(['organization_id' => $order->organization_id,
                                 'id' => $order->id])
                        ->first();;
            $datas = [];
            $datas['mail_open_send'] = 'N';
            $datas['mail_open_data'] = new Time(date('Y-m-d H:i:s'));
            $order_update = $ordersTable->patchEntity($order_update, $datas);
            if(!$ordersTable->save($order_update)) {
                debug($order_update->getErrors());
                Log::error('mailUsersOrdersOpen');
                Log::error($order_update->getErrors());
            }            
        } // end foreach($orders as $order) 
    }

    public function mailUsersOrdersClose($organization_id, $debug=false) {

        $_user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($_user)) 
            return; 

        if($debug) echo "Estraggo gli ordini che chiuderanno tra ".(Configure::read('GGMailToAlertOrderClose')+1)." giorni \n";

        /*
        * estraggo ordini
        */
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $_user->organization->id,
                  'Orders.isVisibleFrontEnd' => 'Y',     
                  'Orders.order_type_id NOT IN ' => [Configure::read('Order.type.gas_parent_groups')],     
                  'Orders.state_code NOT IN ' => ['CREATE-INCOMPLETE', 'CLOSE'],
                  'Orders.data_fine = CURDATE() - INTERVAL '.Configure::read('GGMailToAlertOrderClose').' DAY',
                   // 'Orders.data_fine' => 'CURDATE() - INTERVAL '.Configure::read('GGMailToAlertOrderClose').' DAY'
                ];
        // debug($where);          
        $orders = $ordersTable->find()
                            ->contain(['Deliveries'  => 
                                ['conditions' => ['Deliveries.isVisibleFrontEnd' => 'Y']],  
                            'SuppliersOrganizations' => 
                                ['conditions' => ['SuppliersOrganizations.stato' => 'Y',
                                                  'SuppliersOrganizations.mail_order_close' => 'Y'],
                                                  'Suppliers']])
                            ->where($where)
                            ->order(['Deliveries.data', 'Suppliers.name'])
                            ->all();
        if($orders->count()==0) {
            if($debug) echo "non ci sono ordini che chiuderanna tra ".(Configure::read('GGMailToAlertOrderClose')+1)." giorni \n";
            return false;
        }

        if($debug) echo "Trovati ".$orders->count()." ordini \n";

        /*
        * estraggo tutti gli UTENTI, per ogni ordine verifico che 
        *   e' tra quelli bookmarks_mails
        *   e' un ordine GasGroups, lo user fa parte del gruppo
        */
        $usersTable = TableRegistry::get('Users');
        $where = ['username NOT LIKE' => '%portalgas.it'];        
        $users = $usersTable->gets($_user, $_user->organization->id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }

        foreach($users as $numResult => $user) {
            
            if($this->_debug) {
                if($numResult==1)
                    break;
            };

            if(empty($user->email))
                continue;

            $to = $user->email;
            $addTo = '';
            if(isset($user->user_profiles['email']) && !empty($user->user_profiles['email'])) 
                $addTo = $user->user_profiles['email'];

            if($this->_debug) {
                $to = 'francesco.actis@gmail.com';
                $addTo = 'francesco.actis@gmail.com';    
            }

            $user_orders = $this->_getAclUserOrders('MailUsersOrdersClose', $_user, $organization_id, $user, $orders); // array con gli ordine dell'utente
            if(empty($user_orders)) 
                continue;

            /*
             * subject
             */
            if(count($user_orders)==1) 
                $subject = trim(current($user_orders)->suppliers_organization->name).", ordine che si chiuderà tra ".(Configure::read('GGMailToAlertOrderClose')+1)." giorni";
            else 
                $subject = trim($_user->organization->name).", ordini che si chiuderanno tra ".(Configure::read('GGMailToAlertOrderClose')+1)." giorni";												

            $email = new Email();
            if($this->_debug) 
                $email->setTransport('aws-prod');
            else 
                $email->setTransport('aws');
            $email->viewBuilder()->setHelpers(['Html', 'Text'])
                                   ->setTemplate('users_orders_close', 'default'); // template / layout
            $email->setEmailFormat('html')
                    ->setFrom($this->_from);
            $email->setViewVars(['orders' => $user_orders,
                                 'user' => $user,
                                'organization' => $_user->organization]);
                       
            try {
                $results = $email
                                ->setSubject($subject) 
                                ->setTo($to);
                if(!empty($addTo)) 
                    $email->addTo($addTo);
                $email->send('');
            } catch (\Exception $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
                Log::error('mailUsersOrdersClose');
                Log::error('Exception : ',  $e->getMessage());
            }  

        } // foreach($users as $user)

        /*
        * per gli ordini trovati 
        * UPDATE Order.mail_open_send, Order.mail_open_data
        */        
        foreach($orders as $order) {
            
            $order_update = $ordersTable->find()
                        ->where(['organization_id' => $order->organization_id,
                                 'id' => $order->id])
                        ->first();
            $datas = [];
            $datas['mail_close_data'] = new Time(date('Y-m-d H:i:s'));
            $order_update = $ordersTable->patchEntity($order_update, $datas);
            if(!$ordersTable->save($order_update)) {
                debug($order_update->getErrors());
                Log::error('mailUsersOrdersClose');
                Log::error($order_update->getErrors());
            }            
        } // end foreach($orders as $order)        
    }    

    /*
     *  invio mail x notificare la consegna
     */    
    public function mailUsersDelivery($organization_id, $debug=false) {

        $_user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($_user)) 
            return; 

        if($debug) echo "Estraggo le consegne che si apriranno domani \n";

        if(isset($_user->organization->paramsConfig['hasGasGroups']) && 
        $_user->organization->paramsConfig['hasGasGroups']=='Y') 
            $this->_mailUsersDeliveryGasGroups($_user, $debug);
        else 
            $this->_mailUsersDeliveryGas($_user, $debug);
    }

    /*
    * estraggo consegne per GAS
    */
    private function _mailUsersDeliveryGas($_user, $debug=false) {
        
        $deliveriesTable = TableRegistry::get('Deliveries');

        $where = ['Deliveries.organization_id' => $_user->organization->id,
                  'Deliveries.isVisibleFrontEnd' => 'Y',     
                  'Deliveries.stato_elaborazione' => 'OPEN',
                  'Deliveries.type' => 'GAS', // GAS-GROUP
                  'Deliveries.data = CURDATE() + INTERVAL '.Configure::read('GGMailToAlertDeliveryOn').' DAY',
                   // 'Deliveries.data' => 'CURDATE() + INTERVAL '.Configure::read('GGMailToAlertDeliveryOn').' DAY',
                   'Orders.organization_id' => $_user->organization->id,
                   'Orders.isVisibleBackOffice' => 'Y',
                   'Orders.state_code !=' => 'CREATE-INCOMPLETE'
                  ];
        // debug($where);          
        $deliveries = $deliveriesTable->find()
                            ->contain(['Orders' => ['SuppliersOrganizations' => 
                                            ['conditions' => ['SuppliersOrganizations.stato' => 'Y',
                                                              'SuppliersOrganizations.mail_order_close' => 'Y'],
                                              'Suppliers']]])
                            ->where($where)
                            ->all();

        if($deliveries->count()==0) {
            if($debug) echo "non ci sono consegne che apriranno tra ".Configure::read('GGMailToAlertDeliveryOn')." giorni \n";
            return false;
        }

        if($debug) echo "Trovati ".$deliveries->count()." consegne \n";
        
        /*
        * estraggo tutti gli UTENTI, dopo ctrl se hanno effettuato acquisti
        */
        $usersTable = TableRegistry::get('Users');
        $where = ['username NOT LIKE' => '%portalgas.it'];        
        $users = $usersTable->gets($_user, $_user->organization->id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }
        
        foreach($deliveries as $delivery) {
            
            if(count($delivery->orders)==0) 
                continue;

            foreach($users as $user) {
                if($this->_hasUserCartToDelivery($_user, $user, $delivery, $debug));
                    $this->_mailUsersDeliverySend($_user, $user, $delivery, $debug);
            } // foreach($users as $user)
        } // foreach($gasGroupDeliveries as $gasGroupDelivery)        
    }

    /*
    * consegne per GasGroups
    */
    private function _mailUsersDeliveryGasGroups($_user, $debug=false) {

        $gasGroupDeliveriesTable = TableRegistry::get('GasGroupDeliveries');

        $where = ['GasGroupDeliveries.organization_id' => $_user->organization->id,
                    'Deliveries.organization_id' => $_user->organization->id,
                    'Deliveries.isVisibleFrontEnd' => 'Y',     
                    'Deliveries.stato_elaborazione' => 'OPEN',
                    'Deliveries.type' => 'GAS-GROUP',
                    'Deliveries.data = CURDATE() + INTERVAL '.Configure::read('GGMailToAlertDeliveryOn').' DAY',
                    // 'Deliveries.data' => 'CURDATE() + INTERVAL '.Configure::read('GGMailToAlertDeliveryOn').' DAY',
                    ];
        // debug($where);  
        $gasGroupDeliveries = $gasGroupDeliveriesTable->find()
                            ->contain(['Deliveries' => 
                                ['Orders' => ['conditions' => 
                                    ['Orders.organization_id' => $_user->organization->id,
                                        'Orders.isVisibleBackOffice' => 'Y',
                                        'Orders.order_type_id' => Configure::read('Order.type.gas_groups'),
                                        'Orders.state_code !=' => 'CREATE-INCOMPLETE'],
                                    'SuppliersOrganizations' => 
                                            ['conditions' => ['SuppliersOrganizations.stato' => 'Y',
                                                'SuppliersOrganizations.mail_order_close' => 'Y'],
                                'Suppliers']]], 
                                'GasGroups' => ['GasGroupUsers' => ['Users']]])
                            ->where($where)
                            ->all();

        if($gasGroupDeliveries->count()==0) {
            if($debug) echo "non ci sono consegne che apriranno tra ".Configure::read('GGMailToAlertDeliveryOn')." giorni \n";
            return false;
        }

        if($debug) echo "Trovati ".$gasGroupDeliveries->count()." consegne \n";  
        
        foreach($gasGroupDeliveries as $gasGroupDelivery) {

            $delivery = $gasGroupDelivery->delivery;

            if(count($delivery->orders)==0) 
                continue;

            if(!empty($gasGroupDelivery->gas_group->gas_group_users))
            foreach($gasGroupDelivery->gas_group->gas_group_users as $gas_group_user) {
                $user = $gas_group_user->user;
                if($this->_hasUserCartToDelivery($_user, $user, $delivery, $debug));
                    $this->_mailUsersDeliverySend($_user, $user, $delivery, $debug);
            } // foreach($users as $user)
        } // foreach($gasGroupDeliveries as $gasGroupDelivery)

    }

    private function _hasUserCartToDelivery($_user, $user, $delivery, $debug) {
        
        $cartsTable = TableRegistry::get('Carts');

        $where = ['Carts.organization_id' => $_user->organization->id,
                'Carts.user_id' => $user->id,
                'Carts.stato' => 'Y',
                'deleteToReferent' => 'N',
                  'Orders.organization_id' => $_user->organization->id,
                  'Orders.delivery_id' => $delivery->id];
        // debug($where); 
        $carts = $cartsTable->find()
                            ->contain(['Orders'])
                            ->where($where)
                            ->first();
        if(empty($carts))
            return false;
        else 
            return true;
    }

    private function _mailUsersDeliverySend($_user, $user, $delivery, $debug=false) {
            
        if(empty($user->email))
            return;

        $usersTable = TableRegistry::get('Users');

        $username_crypted = $usersTable->getUsernameCrypted($user->username);
        $urlCartPreviewNoUsername = str_replace("{u}", urlencode($username_crypted), $usersTable->getUrlCartPreviewNoUsername($_user, $delivery->id));

        $to = $user->email;
        $addTo = '';
        if(isset($user->user_profiles['email']) && !empty($user->user_profiles['email'])) 
            $addTo = $user->user_profiles['email'];

        if($this->_debug) {
            $to = 'francesco.actis@gmail.com';
            $addTo = 'francesco.actis@gmail.com';    
        }

        /*
        * subject
        */
        $subject = trim($_user->organization->name).", consegna di ".$delivery->data->i18nFormat('eeee d MMMM');												

        $email = new Email();
        $email->setTransport('aws');
        $email->viewBuilder()->setHelpers(['Html', 'Text'])
                            ->setTemplate('users_delivery', 'default'); // template / layout
        $email->setEmailFormat('html')
                ->setFrom($this->_from);
        $email->setViewVars(['delivery' => $delivery,
                            'user' => $user,
                            'organization' => $_user->organization,
                            'urlCartPreviewNoUsername' => $urlCartPreviewNoUsername]);

        try {
            $results = $email
                            ->setSubject($subject) 
                            ->setTo($to);
            if(!empty($addTo)) 
                $email->addTo($addTo);
            $email->send('');
        } catch (\Exception $e) {
            echo 'Exception : ',  $e->getMessage(), "\n";
            Log::error('_mailUsersDeliverySend');
            Log::error('Exception : ',  $e->getMessage());
        }  

        if($this->_debug) exit;
    } 
    
    /*
     * $user = new UserLocal() e non new User() se no override App::import('Model', 'User');
     * type ENUM('GAS', 'PRODGAS', 'PROD', 'PACT')
     */
    private function _getObjUserLocal($organization_id, $type=['GAS']) {

        $user = new UserLocal();

        $organizationsTable = TableRegistry::get('Organizations');
        $organizationsTable->addBehavior('OrganizationsParams');

        $where = ['Organizations.id' => $organization_id,
                  'Organizations.stato' => 'Y',          
                  'Organizations.type IN ' => $type];
                  
        $organization = $organizationsTable->find()
                            ->contain(['Templates'])
                            ->where($where)
                            ->first();
        if(!empty($organization)) {
            $user->organization = $organization;
        }

        return $user;
    }    

    /*
     * referer = MailUsersOrdersOpen / MailUsersOrdersClose
     * return array con gli ordine dell'utente
     *  escludendo bookmarksMailsTable / gasGroupUsersTable
     */
    private function _getAclUserOrders($referer, $_user, $organization_id, $user, $orders) {

        $user_orders = [];

        if(!empty($orders)) {
            $bookmarksMailsTable = TableRegistry::get('BookmarksMails');
            $gasGroupUsersTable = TableRegistry::get('GasGroupUsers');
            $usersTable = TableRegistry::get('Users');

            $username_crypted = $usersTable->getUsernameCrypted($user->username);
        
            foreach($orders as $order) {
                /* 
                * ctrl se l'ordine e' escluso dallo user 
                */
                $where = ['organization_id' => $organization_id,
                            'user_id' => $user->id,
                            'supplier_organization_id' => $order->supplier_organization_id];
                switch($referer) {
                    case 'MailUsersOrdersOpen':
                        $where += ['order_open' => 'N'];
                    break;
                    case 'MailUsersOrdersClose':
                        $where += ['order_close' => 'N'];
                    break; 
                }         
                $bookmarksMail = $bookmarksMailsTable->find()
                                ->where($where)
                                ->first(); 
                if(empty($bookmarksMail)) {
                    $user_orders[$order->id] = $order;
                    $user_orders[$order->id]->urlCartPreviewNoUsername = str_replace("{u}", urlencode($username_crypted), $usersTable->getUrlCartPreviewNoUsername($_user, $order->delivery_id));
                }
                else 
                    continue;

                /* 
                * ctrl se l'ordine e' GasGroups, se si ctrl se l'utente appartiene al gruppo
                */
                $salta_loop = false; 
                switch($order->order_type_id) {
                    case Configure::read('Order.type.gas_groups'):
                        $gasGroupUser = $gasGroupUsersTable->find()
                                                        ->where(['organization_id' => $organization_id,
                                                                'user_id' => $user->id,
                                                                'gas_group_id' => $order->gas_group_id])
                                                        ->first();
                        if(empty($gasGroupUser)) {
                            if(isset($user_orders[$order->id]))
                                unset($user_orders[$order->id]);
                            $salta_loop = true; 
                        }
                        else {
                            if(!isset($user_orders[$order->id])) {
                                $user_orders[$order->id] = $order;  
                                $user_orders[$order->id]->urlCartPreviewNoUsername = str_replace("{u}", urlencode($username_crypted), $usersTable->getUrlCartPreviewNoUsername($_user, $order->delivery_id));    
                            }
                        }
                    break;
                } // switch($order->order_type_id)

                if($salta_loop)
                    continue;
            } // end foreach($orders as $order)
        } // end if(!empty($orders))

        return $user_orders;
    } 
}
class UserLocal {

    public $organization;

}