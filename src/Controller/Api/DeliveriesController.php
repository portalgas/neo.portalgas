<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class DeliveriesController extends ApiAppController
{
    private $_has_cache = false;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Gas');
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['gets']);
        $this->Authorization->skipAuthorization(['gets']);
    }

      /* 
     * front-end - estrae le consegne anche senza ordine e l'eventuale "da definire" con ordini
     * consegne
     */
    public function gets() {

        $user = $this->Authentication->getIdentity();

        $organization_id = $this->request->getData('organization_id');

        $results = [];
        $deliveriesTable = TableRegistry::get('Deliveries');

        $where = ['Deliveries.organization_id' => $organization_id,
                'Deliveries.isVisibleFrontEnd' => 'Y',
                'Deliveries.stato_elaborazione' => 'OPEN',
                'Deliveries.sys' => 'N',
                'DATE(Deliveries.data) >= CURDATE() - INTERVAL ' . Configure::read('GGinMenoPerEstrarreDeliveriesInTabs') . ' DAY'
        ];
        
        $deliveries = $deliveriesTable->find()->where($where)->order(['Deliveries.data'])->all();
        if($deliveries->count()>0) {
            foreach($deliveries as $delivery) {
                /*
                 * https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
                 * key array non per id, nel json perde l'ordinamento della data
                 * $results[$delivery->id] = $delivery->data->i18nFormat('eeee d MMMM yyyy');
                 */
                $results[] = ['id' => $delivery->id, 'label' => $delivery->data->i18nFormat('eeee d MMMM').' - '.$delivery->luogo];
            }
        }

        $sysDelivery = $deliveriesTable->getDeliverySys($user, $organization_id, []);
        $results[] = ['id' => $sysDelivery->id, 'label' => $sysDelivery->luogo];
        
        return $this->_response($results);
    } 
}
