<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Traits;

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

        if($debug)
            echo "Estraggo gli ordini che apriranno tra ".(Configure::read('GGMailToAlertOrderOpen')+1)." giorni o con mail_open_send = Y \n";

        /*
        * estraggo ordini
        */
        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $user->organization->id,
                  'Orders.isVisibleFrontEnd' => 'Y',          
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
        dd($orders->count());
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