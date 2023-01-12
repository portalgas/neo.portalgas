<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;

/*
 * creo ordini ricorsivi
 * /var/www/neo.portalgas/src/Command/Sh/loops_orders.sh
 * /var/www/neo.portalgas/bin/cake LoopsOrders {organization_id}
 */ 
class LoopsOrdersCommand extends Command
{
    public function initialize() {
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->addArgument('organization_id', [
                'help' => 'organization_id'
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $debug = false;

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        if($debug) {
            $this->io = $io;
            $this->io->out('LoopsOrders start');
        }

        $organization_id = $args->getArgument('organization_id');        
        
        /* 
         * estraggo le consegne ricorsive create oggi 
         */
        $this->loadModel('LoopsDeliveries');
        $loopsDeliveriesResults = $this->LoopsDeliveries->find()
                                  ->contain(['Deliveries'])
                                  ->where(['LoopsDeliveries.organization_id' => $organization_id,
                                           'LoopsDeliveries.delivery_id !=' => 0,
                                           'DATE(LoopsDeliveries.modified) = CURDATE()'])
                                  ->all();

        if($loopsDeliveriesResults->count()>0) {
            $this->loadModel('LoopsOrders');
            foreach($loopsDeliveriesResults as $loopsDelivery) {   
                // debug($loopsDelivery);
                
                // ctrl se ci sono ordini ricorsivi associati alla consegna ricorsiva
                $loopsOrdersResults = $this->LoopsOrders->find()
                                                ->contain(['SuppliersOrganizations'])
                                                ->where(['LoopsOrders.organization_id' => $organization_id,
                                                        'LoopsOrders.loops_delivery_id' => $loopsDelivery->id,
                                                        'LoopsOrders.order_id' => 0])
                                                ->all();  
                if($loopsOrdersResults->count()>0)                 
                foreach($loopsOrdersResults as $loopsOrder) { 
                    
                    // debug($loopsOrder);

                    $datas = [];
                    $datas['organization_id'] = $organization_id;
                    $datas['supplier_organization_id'] = $loopsOrder->supplier_organization_id;
                    $datas['owner_articles'] = $loopsOrder->suppliers_organization->owner_articles;
                    $datas['owner_organization_id'] = $loopsOrder->suppliers_organization->owner_organization_id;
                    $datas['owner_supplier_organization_id'] = $loopsOrder->suppliers_organization->owner_supplier_organization_id;
                    $datas['delivery_id'] = $loopsDelivery->delivery_id;
                    $datas['data_inizio'] = $loopsDelivery->delivery->data->subDays($loopsOrder->gg_data_inizio);
                    $datas['data_fine'] = $loopsDelivery->delivery->data->subDays($loopsOrder->gg_data_fine);

                    debug($datas);
                    
                    $this->loadModel('Orders');
                    // $this->_ordersTable = $this->Orders->factory($this->_user, $this->_organization->id, $order_type_id);
                    $this->Orders->addBehavior('Orders');

                    $order = $this->Orders->newEntity();
                    $order = $this->Orders->patchEntity($order, $datas);
                    if (!$this->Orders->save($order)) {
                        dd($order->getErrors());
                    }
                } // end foreach($loopsOrdersResults as $loopsOrder)
            } // end foreach($loopsDeliveriesResults as $loopsDelivery)
        } // end if($loopsDeliveriesResults->count()>0) 

        if($debug) {
            $this->io = $io;
            $this->io->out('LoopsOrders end');
        }
    }
}