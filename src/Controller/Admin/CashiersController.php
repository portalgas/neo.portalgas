<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class CashiersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cashier');
        $this->loadComponent('Cart');
        $this->loadComponent('SummaryOrder');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isCassiere']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }    

    public function deliveries()
    {   
        $debug = false;

        if ($this->request->is('post')) {
            
            /*
             * LifeCycleOrdersTable
             * registro tutti gli order_id trattati per poi verificare stato successivo
             */
            $order_ids = [];

            if($debug) debug($this->request->getData());

            $delivery_id = $this->request->getData('delivery_id');
            $nota = $this->request->getData('nota');
            
            /*
             * $is_cash = 1 considero la cassa
             * $is_cash = 0 non considero la cassa
             */
            $is_cash = $this->request->getData('is_cash');

            if(!empty($delivery_id)) {

                $options =  [];
                $options['where'] = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];
                $userResults = $this->Cart->getUsersByDelivery($this->_user, $delivery_id, $options, $debug);

                if(!empty($userResults)) {

                    $summaryOrdersTable = TableRegistry::get('SummaryOrders');
                    
                    foreach($userResults as $numResult => $userResult) {

                        /*
                         * dettaglio acquisto per user (SummaryOrders)
                         */
                        $summaryOrderResults = $this->SummaryOrder->getByUserByDelivery($this->_user, $userResult->organization_id, $userResult->id, $delivery_id, $options, $debug);     

                        /*
                         * per ogni ordine/user saldo il pagamento
                         */
                        if(!empty($summaryOrderResults)) {
                            /*
                             * somma degli importi di SummaryOrder.importo (SummaryDelivery)
                             * lo faccio prina di salvare summaryOrders se no importo_pagato = importo
                             */
                            $summaryDeliveryResults = $this->SummaryOrder->getSummaryDeliveryByUser($this->_user, $userResult->organization_id, $userResult->id, $delivery_id, $summaryOrderResults, $debug);
                            // debug($summaryDeliveryResults);                            

                            foreach($summaryOrderResults as $summaryOrderResult) {

                                unset($summaryOrderResult->order);
                                unset($summaryOrderResult->user);

                                $order_ids[$summaryOrderResult->order_id] = $summaryOrderResult->order_id; 

                                $data = [];
                                $data['importo_pagato'] = $summaryOrderResult->importo;
                                $data['modalita'] = $this->SummaryOrder::MODALITA_CONTANTI;
                                $data['saldato_a'] = $this->SummaryOrder::SALDATO_A_CASSIERE; 

                                $summaryOrderResult = $summaryOrdersTable->patchEntity($summaryOrderResult, $data);
                                // debug($summaryOrderResult);
                                if (!$summaryOrdersTable->save($summaryOrderResult)) {
                                    debug($summaryOrderResult->getErrors());
                                }
                            } // foreach($summaryOrderResults as $summaryOrderResult)
                        } // end if(!empty($summaryOrderResults))

                        /*
                         * C A S S A
                         */
                        if($is_cash==1) {

                            $cashesTable = TableRegistry::get('Cashes');

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

                            $importo_da_pagare = ($tot_importo - $tot_importo_pagato);

                            $data = [];
                            $data['organization_id'] = $userResult->organization_id;
                            $data['user_id'] = $userResult->id;
                            $data['importo_da_pagare'] = $importo_da_pagare;
                            $data['nota'] = $nota;
                            $cashesTable->insert($this->_user, $data, $debug);
                                          
                       } // end if($is_cash==1)             
                    } // end foreach($userResults as $numResult => $userResult)
                } // if(!empty($userResults))

                /* 
                 * se tutti i gasisti hanno saldato aggiorno stato dell'ordine
                 */
                // debug($order_ids);
                if(!empty($order_ids)) {

                    $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');

                    foreach($order_ids as $order_id) {
                        $state_code_next = $lifeCycleOrdersTable->stateCodeAfter($this->_user, $order_id, 'PROCESSED-ON-DELIVERY', $debug);
                        
                        $lifeCycleOrdersTable->stateCodeUpdate($this->_user, $order_id, $state_code_next, [], $debug);
                    } // foreach($order_ids as $order_id)      
                }

            } // end if(!empty($delivery_id))

            $delivery_id = '';
        } // if ($this->request->is('post'))
      
        $deliveries = $this->Cashier->getListDeliveries($this->_user);
        
        $is_cashs = [1 => __('Si'), 0 => __('No')];
        $is_cash_default = 1;

        $this->set(compact('deliveries', 'is_cashs', 'is_cash_default'));                  
    }

    public function massive() {

        $debug = false;

        if ($this->request->is('post')) {
           if($debug) debug($this->request->getData());

           $validator = new Validator();
           $validator->requirePresence('user_ids', true, __('Parameters required'));
           $errors = $validator->errors($this->request->getData());
           if (!empty($errors)) {
                $this->Flash->error(__('Parameters required'));
                return $this->redirect(['action' => 'massive']);
           }

           $user_ids = $this->request->getData('user_ids');
           $minus = $this->request->getData('minus');
           $plus = $this->request->getData('plus');
           $note = $this->request->getData('note');

           if(strpos($user_ids, ',')!==false)
               $user_ids = explode(',', $user_ids);
            else 
                $user_ids = [$user_ids];
                
           foreach($user_ids as $user_id) {

                $datas = [];
                $datas['organization_id'] = $this->_organization->id;
                $datas['user_id'] = $user_id;
                /*
                 * il (-1) * e' invertito perche' in $cashesTable->insert sottrae sempre l'importo passato
                 */
                $datas['importo_da_pagare'] = (!empty($minus)) ? $minus: ((-1) * $plus);
                $datas['nota'] = $note;
                // debug($datas);
                $cashesTable = TableRegistry::get('Cashes');
                $results = $cashesTable->insert($this->_user, $datas, $debug);
                // debug($results);
           }

           $this->Flash->success("Inserito correttamente il movimento di cassa ai gas scelti");
        } // if ($this->request->is('post'))

        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->getList($this->_user, $this->_organization->id);        
        $this->set(compact('users'));
    }
}