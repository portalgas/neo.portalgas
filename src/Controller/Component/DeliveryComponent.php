<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class DeliveryComponent extends Component {

    protected $_registry;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function getListByResults($user, $deliveries, $debug=false) {

        $results = [];

        if(!empty($deliveries)) {
            if(!is_array($deliveries) && !$deliveries instanceof \Cake\ORM\ResultSet) {
                $label = $deliveries->luogo;
                $label .= ' ('.$deliveries->data->i18nFormat('eeee d MMMM yyyy').')';

                $results[$deliveries->id] = $label;
            }
            else {
                foreach ($deliveries as $delivery) {
                    $label = $delivery->luogo;
                    $label .= ' ('.$delivery->data->i18nFormat('eeee d MMMM yyyy').')';

                    $results[$delivery->id] = $label;
                }                
            }
        }

        return $results;
    }
}