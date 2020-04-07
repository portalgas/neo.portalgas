<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CashiersController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auth');
        $this->loadComponent('Cart');
        $this->loadComponent('Cash');
        $this->loadComponent('SummaryOrder');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * estrae solo gli users che hanno effettuato acquisti in base alla consegna
     */
    public function getCompleteUsersByDelivery() {

        $debug = false;
        $results = [];

        $delivery_id = $this->request->getData('delivery_id');
        if(!empty($delivery_id)) {

            $options =  [];
            $options['where'] = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];
            $userResults = $this->Cart->getUsersByDelivery($this->user, $delivery_id, $options, $debug);

            if(!empty($userResults)) {
                
                foreach($userResults as $numResult => $userResult) {

                    $results[$numResult] = $userResult;

                    /*
                     * associo dettaglio acquisto per user (SummaryOrders)
                     */
                    $summaryOrderResults = $this->SummaryOrder->getByUserByDelivery($this->user, $userResult->organization_id, $userResult->id, $delivery_id, $options, $debug);                    
                    $results[$numResult]['summary_orders'] = $summaryOrderResults;

                    /*
                     * somma degli importi di SummaryOrder.importo (SummaryDelivery)
                     */
                    $summaryDeliveryResults = $this->SummaryOrder->getSummaryDeliveryByUser($this->user, $userResult->organization_id, $userResult->id, $delivery_id, $summaryOrderResults, $debug);

                    $results[$numResult]['summary_delivery'] = $summaryDeliveryResults;

                    /*
                     * associo la cassa
                     */
                    $cashResults = $this->Cash->getByUser($this->user, $userResult->organization_id, $userResult->id, $options, $debug);
                    $results[$numResult]['cash'] = $cashResults;

                    /*
                     * nuovo valore in cassa
                     */
                    if(isset($summaryDeliveryResults['tot_importo']))
                        $tot_importo = $summaryDeliveryResults['tot_importo'];
                    else
                        $tot_importo = 0;
                    if(isset($summaryDeliveryResults['tot_importo_pagato']))
                        $tot_importo_pagato = $summaryDeliveryResults['tot_importo_pagato'];
                    else
                        $tot_importo_pagato = 0;
                    if(isset($cashResults['importo']))
                        $cash_importo = $cashResults['importo'];
                    else
                        $cash_importo = 0;
                    $importo_da_pagare = ($tot_importo - $tot_importo_pagato);
                    // debug('tot_importo '.$tot_importo.' tot_importo_pagato '.$tot_importo_pagato.' importo_da_pagare '.$importo_da_pagare.' cash_importo '.$cash_importo);
                    $importo_new = $this->Cash->getNewImport($this->user, $importo_da_pagare, $cash_importo, $debug);
                    // debug('importo_new '.$importo_new);
                                  
                    $results[$numResult]['cash_importo_new'] = $importo_new;

                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        
        return $this->response; 
    } 
}