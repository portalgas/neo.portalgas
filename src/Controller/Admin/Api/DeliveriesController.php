<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use App\Decorator\ArticleDecorator;
use App\Decorator\ApiArticleDecorator;

class DeliveriesController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
        $this->loadComponent('Csrf');
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * front-end - estrae le consegne anche senza ordine e l'eventuale "da definire" con ordini
     */
    public function gets() {

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id;

        /*
         * elenco consegne
         */
        $deliveriesTable = TableRegistry::get('Deliveries');

        $where = [];
        $where['Deliveries'] = ['Deliveries.isVisibleFrontEnd' => 'Y',
                                'Deliveries.stato_elaborazione' => 'OPEN',
                                'Deliveries.sys' => 'N',
                                'DATE(Deliveries.data) >= CURDATE()'];
        $where['Orders'] = ['Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];
        $deliveries = $deliveriesTable->gets($user, $organization_id, $where);
        if(!empty($deliveries)) {
            foreach($deliveries as $delivery) {
                $results[$delivery->id] = $delivery->data->format('d F Y');
            }
        }

        /*
         * ctrl se ci sono ordini con la consegna ancora da definire (Delivery.sys = Y)
         */
        $where = [];
        $where['Orders'] = ['Orders.organization_id' => $organization_id,
                            'Orders.state_code in ' => ['OPEN', 'RI-OPEN-VALIDATE']];
        $sysDelivery = $deliveriesTable->getDeliverySys($user, $organization_id, $where);
        // debug($sysDelivery);
        
        
        if($sysDelivery->has('orders') && !empty($sysDelivery->orders)) {
            $results[$sysDelivery->id] = $sysDelivery->luogo;
        }

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}