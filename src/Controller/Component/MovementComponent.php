<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use Cake\I18n\FrozenTime;

class MovementComponent extends CartSuperComponent {

	private $_movement_type_invoice = 5;    // INVOICE Pagamento fattura a fornitore
	private $_movement_type_userimport = 8; // USERSIMPORT Saldo movimento di cassa
    private $_name_default = 'Saldo movimento di cassa';

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    /*
     * ribalta gli importi da k_cashes a movements
     */
    public function populateByCashes($user, $organization_id, $year, $debug=false) {
       
        // $debug = true;

        // id_user inseriti in Movements, quelli esclusi saranno eliminati
        $id_users = []; 

        $cashesTable = TableRegistry::get('Cashes');
        $movementsTable = TableRegistry::get('Movements');

        $where = ['Cashes.organization_id' => $organization_id,
                'Cashes.importo != ' => 0,
                'YEAR(Cashes.modified)' => $year];
        if($debug) debug($where);        
        $cashes = $cashesTable->find()
                            ->contain(['Users' => ['conditions' => ['Users.block' => 0]]])
                            ->where($where)
                            ->all();
        if($debug) debug($cashes->count());
        if($cashes->count()>0)
        foreach($cashes as $cash) {
            /*
             * verifico se esiste gia' in Movements
             */
            $where = ['Movements.organization_id' => $organization_id,
                      'Movements.year' => $year,
                      'Movements.user_id' => $cash->user_id,
                      'Movements.movement_type_id' => $this->_movement_type_userimport];
            $movement = $movementsTable->find()
                                        ->where($where)
                                        ->first();
            if(empty($movement)) 
                $movement = $movementsTable->newEntity();

            $datas = [];
            $datas['organization_id'] = $organization_id;
            $datas['user_id'] = $cash->user_id;
            $datas['year'] = $year;
            $datas['date'] = $cash->created;
            $datas['name'] = $this->_name_default;
            $datas['payment_type'] = 'CASSA';
            $datas['movement_type_id'] = $this->_movement_type_userimport; 
            $datas['importo'] = $cash->importo;
            $datas['is_system'] = 0;
            $datas['is_active'] = 1;
            $movement = $movementsTable->patchEntity($movement, $datas);
            if (!$movementsTable->save($movement)) {
                dd($movement->getErrors());
            }

            $id_users[] = $cash->user_id;
        } // foreach($cashes as $cash)

        /* 
         * elimino utenti non presenti in cassa
         */
        if(!empty($id_users)) {
            $where = ['organization_id' => $organization_id, 
            'year' => $year, 
            'movement_type_id' => $this->_movement_type_userimport,
            'user_id NOT IN' => $id_users];
            $movementsTable->deleteAll($where);
        }

        return true;
	}
}