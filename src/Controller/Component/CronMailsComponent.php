<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Traits;
use Cake\Network\Email\Email;

class CronMailsComponent extends Component {

    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();

        date_default_timezone_set('Europe/Rome');
    }

    /*
     * $debug = true perche' quando e' richiamato dal Cron deve scrivere sul file di log
     * invio mail 
     *      ordini che si aprono oggi
     *      ctrl data_inizio con data_oggi
     *      mail_open_send = Y (perche' in Order::add data_inizio = data_oggi)
     */    
    public function mailUsersOrdersOpen($organization_id, $debug=false) {

        $user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($user)) 
            return; 

        if($debug) echo "Estraggo gli ordini che apriranno tra ".(Configure::read('GGMailToAlertOrderOpen')+1)." giorni o con mail_open_send = Y \n";

        /*
        * estraggo ordini
        */
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $user->organization->id,
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
        $users = $usersTable->gets($user, $organization_id, $where);
        if($users->count()==0) {
            if($debug) echo "non ci sono utenti! \n";
            return false;
        }

        $bookmarksMailsTable = TableRegistry::get('BookmarksMails');
        $gasGroupUsersTable = TableRegistry::get('GasGroupUsers');
            
        foreach($users as $user) {
            
            $user_orders = []; // array con gli ordine dell'utente
            foreach($orders as $order) {
                /* 
                 * ctrl se l'ordine e' escluso dallo user 
                 */
                $bookmarksMail = $bookmarksMailsTable->find()
                                    ->where(['organization_id' => $organization_id,
                                            'user_id' => $user->id,
                                            'supplier_organization_id' => $order->supplier_organization_id,
                                            'order_open' => 'N'])
                                    ->first(); 
                if(empty($bookmarksMail)) {
                    $user_orders[$order->id] = $order;
                }
                else 
                    continue;

                /* 
                 * ctrl se l'ordine e' GasGroups, s si ctrl se l'utente appartiene al gruppo
                 */
                switch($order->order_type_id) {
                    case Configure::read('Order.type.gas_groups'):
                        $gasGroupUser = $gasGroupUsersTable->find()
                                                        ->where(['gas_group_id' => $order->gas_group_id,
                                                                'user_id' => $user->id])
                                                        ->first();
                        if(empty($gasGroupUser)) {
                            if(isset($user_orders[$order->id]))
                                unset($user_orders[$order->id]);
                            continue;
                        }
                        else 
                            $user_orders[$order->id] = $order;
                    break;
                } // switch($order->order_type_id)
            } // end foreach($orders as $order)

            if(!empty($user_orders)) {
                $email = new Email();
                $email->transport('default');
        
                $email->setViewVars(['verbal' => $verbal, 
                                    'offer' => $offer,
                                    'site_url' => $site_url]);
                                    
                $email->helpers(['Html', 'Text']);
        
                try {
                    $results = $email
                        ->emailFormat('html')
                        ->template('verbal-state', 'default') // template / layout
                        ->from($from)
                        ->to($to)
                        // ->addTo($to)
                        ->subject($subject)                   
                        ->send($msg);
                } catch (Exception $e) {
                    echo 'Exception : ',  $e->getMessage(), "\n";
                }        
            }
        } // foreach($users as $user)
    }    

    public function mailUsersOrdersClose($organization_id, $debug=false) {

        $user = $this->_getObjUserLocal($organization_id, ['GAS']);
        if(empty($user)) 
            return;         
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
}

class UserLocal {

    public $organization;

}