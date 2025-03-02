<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Table\OrganizationsTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class OrganizationsController extends ApiAppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets']);
    }

    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $gas = Cache::read('organizations-gas');
        if ($gas !== false) {
            $results['results'] =  $gas;
        }
        else {
            $organizationTable = TableRegistry::get('OrganizationsGas');

            $organizations = $organizationTable->find('list', [
                                    'keyField' => 'j_seo',
                                    'valueField' => function ($organization) {
                                        return $organization->name . ' - ' . $organization->localita . ' (' . $organization->provincia.')';
                                    }
                                ])
                                ->where(['type' => 'GAS', 'stato' => 'Y'])
                                ->order(['name' => 'asc'])
                                ->all();

            Cache::write('organizations', $organizations);

            $results['results'] = $organizations;
        }

        return $this->_response($results);
    }
}
