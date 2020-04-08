<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CashiersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cashier');
        $this->loadComponent('Cart');
        $this->loadComponent('Cash');
        $this->loadComponent('SummaryOrder');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(!$this->Auth->isCassiere($this->user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => true]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }    

    public function deliveries()
    {   
        $debug = false;

        if ($this->request->is('post')) {
            
            if($debug) debug($this->request->getData());

            $delivery_id = $this->request->getData('delivery_id');
            /*
             * $is_cash = 1 considero la cassa
             * $is_cash = 0 non considero la cassa
             */
            $is_cash = $this->request->getData('is_cash');

            if(!empty($delivery_id)) {

                $options =  [];
                $options['where'] = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];
                $userResults = $this->Cart->getUsersByDelivery($this->user, $delivery_id, $options, $debug);

                if(!empty($userResults)) {
                    
                    foreach($userResults as $numResult => $userResult) {

                        /*
                         * dettaglio acquisto per user (SummaryOrders)
                         */
                        $summaryOrderResults = $this->SummaryOrder->getByUserByDelivery($this->user, $userResult->organization_id, $userResult->id, $delivery_id, $options, $debug);     

                        switch ($is_cash) {
                           case 0:
                                /*
                                 * per ogni ordine/user saldo il pgamento
                                 */
                                if(!empty($summaryOrderResults))
                                foreach($summaryOrderResults as $summaryOrderResult) {

                                    /* 
                                        $data['SummaryOrder']['order_id'] = $order_id;
                                        $data['SummaryOrder']['delivery_id'] = $delivery_id;
                                        $data['SummaryOrder']['user_id'] = $user_id;
                                        $data['SummaryOrder']['importo_pagato'] = $importo;
                                        $data['SummaryOrder']['modalita'] = 'CONTANTI';
                                        $data['SummaryOrder']['saldato_a'] = 'CASSIERE'; 
                                    */
                                }     
                           break;
                           case 1:
                                /*
                                 * somma degli importi di SummaryOrder.importo (SummaryDelivery)
                                 */
                                $summaryDeliveryResults = $this->SummaryOrder->getSummaryDeliveryByUser($this->user, $userResult->organization_id, $userResult->id, $delivery_id, $summaryOrderResults, $debug);

                                /*
                                 * ricerco la cassa per lo user
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
                           break;                           
                           default:
                               die("valore is_cash [$is_cash] non valido");
                           break;
                       } // switch ($is_cash)             
                    } // end foreach($userResults as $numResult => $userResult)
                } // if(!empty($userResults))

            } // end if(!empty($delivery_id))

            $delivery_id = '';
        } // if ($this->request->is('post'))

        $deliveries = $this->Cashier->getListDeliveries($this->user);
        
        $is_cashs = [1 => __('Si'), 0 => __('No')];
        $is_cash_default = 1;

        $this->set(compact('deliveries', 'is_cashs', 'is_cash_default'));                  
    }
}