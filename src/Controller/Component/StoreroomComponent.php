<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class StoreroomComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    /* 
     * da stampa carrello   
     * front-end - estrae gli articoli associati ad una consegna acquisti per user  
     */
    public function getArticlesByDeliveryId($user, $organization_id, $delivery_id, $options=[], $debug=false) {
        
        $results = [];

        $where = [];
        $where['Deliveries'] = ['Deliveries.id' => $delivery_id];
        $where['Users'] = ['Users.id' => $user->id];
        $storeroomsTable = TableRegistry::get('Storerooms');
        $results = $storeroomsTable->gets($user, $organization_id, $where, $debug);

        return $results;
    }   
}