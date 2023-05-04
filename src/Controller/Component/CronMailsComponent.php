<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Traits;
Use Cake\Mailer\Email;

class CronMailsComponent extends Component {

    use Traits\SqlTrait;
    use Traits\UtilTrait;

    private $_from; // info@portalgas.it
    private $_debug = true; // se true invio 1 email a francesco.actis@gmail.com

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
        $users = $usersTable->gets($_user, $organization_id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }

        foreach($users as $user) {
            
            if(empty($user->email))
                continue;

            $to = $user->email;
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
            } catch (Exception $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
                Log::error('Exception : ',  $e->getMessage());
            }  

            if($this->_debug) exit;
        } // foreach($users as $user)

        /*
        * per gli ordini trovati 
        * UPDATE Order.mail_open_send, Order.mail_open_data
        */        
        foreach($orders as $order) {
            
            $order = $ordersTable->find()
                        ->where(['organization_id' => $order->organization_id,
                                 'id' => $order->id])
                        ->first();
            $datas = [];
            $datas['mail_open_send'] = 'N';
            $datas['mail_open_data'] = date('Y-m-d H:i:s');
            $order = $ordersTable->patchEntity($order, $datas);
            if(!$ordersTable->save($order)) {
                debug($order->getErrors());
                Log::error($order->getErrors());
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
        $users = $usersTable->gets($_user, $organization_id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }

        foreach($users as $user) {
            
            if(empty($user->email))
                continue;

            $to = $user->email;
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
            } catch (Exception $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
                Log::error('Exception : ',  $e->getMessage());
            }  

            if($this->_debug) exit;
        } // foreach($users as $user)

        /*
        * per gli ordini trovati 
        * UPDATE Order.mail_open_send, Order.mail_open_data
        */        
        foreach($orders as $order) {
            
            $order = $ordersTable->find()
                        ->where(['organization_id' => $order->organization_id,
                                 'id' => $order->id])
                        ->first();
            $datas = [];
            $datas['mail_close_data'] = date('Y-m-d H:i:s');
            $order = $ordersTable->patchEntity($order, $datas);
            if(!$ordersTable->save($order)) {
                debug($order->getErrors());
                Log::error($order->getErrors());
            }            
        } // end foreach($orders as $order)        
    }    

    /*
     *  invio mail x notificare la consegna
     */    
    public function mailUsersDelivery($organization_id, $debug=false) {

        $user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($user)) 
            return; 
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
                * ctrl se l'ordine e' GasGroups, s si ctrl se l'utente appartiene al gruppo
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