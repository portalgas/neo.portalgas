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

                    dd($datas);
                    
                /*  
    `k_orders`.`order_type_id`, 
    LifeCycleOrdersTable  
    $OrderLifeCycle->getType($user, $requestData);


    `k_orders`.`prod_gas_promotion_id`,
    `k_orders`.`des_order_id`,
    `k_orders`.`gas_group_id`,
    `k_orders`.`parent_id`,
    `k_orders`.`data_fine_validation`,
    `k_orders`.`data_incoming_order`,
    `k_orders`.`data_state_code_close`,
    `k_orders`.`nota`,
    `k_orders`.`hasTrasport`,
    `k_orders`.`trasport_type`,
    `k_orders`.`trasport`,
    `k_orders`.`hasCostMore`,
    `k_orders`.`cost_more_type`,
    `k_orders`.`cost_more`,
    `k_orders`.`hasCostLess`,
    `k_orders`.`cost_less_type`,
    `k_orders`.`cost_less`,
    `k_orders`.`typeGest`,
    `k_orders`.`state_code`,
    `k_orders`.`mail_open_send`,
    `k_orders`.`mail_open_data`,
    `k_orders`.`mail_close_data`,
    `k_orders`.`mail_open_testo`,
    `k_orders`.`type_draw`,
    `k_orders`.`tot_importo`,
    `k_orders`.`qta_massima`,
    `k_orders`.`qta_massima_um`,
    `k_orders`.`send_mail_qta_massima`,
    `k_orders`.`importo_massimo`,
    `k_orders`.`send_mail_importo_massimo`,
    `k_orders`.`tesoriere_nota`,
    `k_orders`.`tesoriere_fattura_importo`,
    `k_orders`.`tesoriere_doc1`,
    `k_orders`.`tesoriere_data_pay`,
    `k_orders`.`tesoriere_importo_pay`,
    `k_orders`.`tesoriere_stato_pay`,
    `k_orders`.`inviato_al_tesoriere_da`,
    `k_orders`.`isVisibleFrontEnd`,
    `k_orders`.`isVisibleBackOffice`,
                */
                } // end foreach($loopsOrdersResults as $loopsOrder)
            } // end foreach($loopsDeliveriesResults as $loopsDelivery)
        } // end if($loopsDeliveriesResults->count()>0) 

        if($debug) {
            $this->io = $io;
            $this->io->out('LoopsOrders end');
        }
    }
}