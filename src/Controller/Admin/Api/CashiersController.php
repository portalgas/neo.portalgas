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
        $this->loadComponent('Auths');
        $this->loadComponent('Cart');
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

            $cashesTable = TableRegistry::get('Cashes');
            
            $options =  [];
            $options['where'] = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];
            $userResults = $this->Cart->getUsersByDelivery($this->Authentication->getIdentity(), $delivery_id, $options, $debug);

            if(!empty($userResults)) {
            
                $i=0;
                foreach($userResults as $userResult) {

                    /*
                     * associo dettaglio acquisto per user (SummaryOrders)
                     */
                    $options =  [];
                    $options['where'] = $this->SummaryOrder->getConditionIsNotSaldato($this->Authentication->getIdentity());
                    $options['where'] += ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];
                    $summaryOrderResults = $this->SummaryOrder->getByUserByDelivery($this->Authentication->getIdentity(), $userResult->organization_id, $userResult->id, $delivery_id, $options, $debug);
                    if(empty($summaryOrderResults) || $summaryOrderResults->count()==0) {
                        /*
                         * gasista ha gia' saldato
                         */
                    }  
                    else {
                        $results[$i] = $userResult;
                        $results[$i]['summary_orders'] = $summaryOrderResults;

                        /*
                         * somma degli importi di SummaryOrder.importo (SummaryDelivery)
                         */
                        $summaryDeliveryResults = $this->SummaryOrder->getSummaryDeliveryByUser($this->Authentication->getIdentity(), $userResult->organization_id, $userResult->id, $delivery_id, $summaryOrderResults, $debug);

                        $results[$i]['summary_delivery'] = $summaryDeliveryResults;

                        /*
                         * associo la cassa
                         */
                        $cashResults = $cashesTable->getByUser($this->Authentication->getIdentity(), $userResult->organization_id, $userResult->id, $options, $debug);
                        $results[$i]['cash'] = $cashResults;

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
                        $importo_new = $cashesTable->getNewImport($this->Authentication->getIdentity(), $importo_da_pagare, $cash_importo, $debug);
                        // debug('importo_new '.$importo_new);
                                  
                        $results[$i]['cash_importo_new'] = $importo_new;

                        $i++;
                    } // if(empty($summaryOrderResults))
                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        $results = json_encode($results);
        $this->response->withType('application/json');
        $body = $this->response->getBody();
        $body->write($results);        
        $this->response->withBody($body);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 
}