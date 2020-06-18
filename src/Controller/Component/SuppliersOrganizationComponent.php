<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class SuppliersOrganizationComponent extends Component {

    protected $_registry;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function getListByResults($user, $suppliersOrganizations, $debug=false) {

        $results = [];

        if(!empty($suppliersOrganizations)) {
            if(!is_array($suppliersOrganizations)) {
                $label = $suppliersOrganizations->name;
                if(!empty($suppliersOrganizations->supplier->descri))
                    $label .= '('.$suppliersOrganizations->supplier->descri.')';

                $results[$suppliersOrganizations->id] = $label;

            }
            else {
                foreach ($suppliersOrganizations as $suppliersOrganization) {
                    $label = $suppliersOrganization->name;
                    if(!empty($suppliersOrganization->supplier->descri))
                        $label .= '('.$suppliersOrganization->supplier->descri.')';

                    $results[$suppliersOrganization->id] = $label;
                }                
            }
        }

        return $results;
    }
}