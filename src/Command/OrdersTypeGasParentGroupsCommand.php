<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\I18n\FrozenTime;

/*
 * creo ordini ricorsivi
 * /var/www/neo.portalgas/src/Command/Sh/ordersTypeGasParentGroups.sh
 * /var/www/neo.portalgas/bin/cake OrdersTypeGasParentGroups {organization_id}
 * 
 * cron 
 * 45 0 * * * /var/www/neo.portalgas/src/Command/Sh/ordersTypeGasParentGroups.sh >> /var/portalgas/cron/log/$(date +\%Y\%m\%d)_ordersTypeGasParentGroupsNeo.log 2>&1
 */ 
class OrdersTypeGasParentGroupsCommand extends Command
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
            $this->io->out('OrdersTypeGasParentGroupsCommand start');
        }

        $organization_id = $args->getArgument('organization_id');
        if(empty($organization_id)) 
            dd('organization_id required!');

        $giorniFa = new FrozenTime('-60 days');
        Log::info("Per organization_id [$organization_id] Elimino gli ordini titolari di gruppo ('Order.type.gas_parent_groups') piu' vecchi di ".$giorniFa." e che non hanno piu' ordini", ['scope' => ['shell']]);

        $this->loadModel('Orders');
        $order_parents = $this->Orders->find()
                                  ->where(['Orders.organization_id' => $organization_id,
                                           'Orders.order_type_id' => Configure::read('Order.type.gas_parent_groups'),
                                           'Orders.data_fine < ' => $giorniFa])
                                  ->order(['Orders.data_fine'])
                                  // ->limit(1)
                                  ->all();

        if($order_parents->count()>0) {
           foreach($order_parents as $order_parent) {   
                $tot_orders = $this->Orders->find()
                    ->where(['Orders.organization_id' => $organization_id,
                            'Orders.order_type_id' => Configure::read('Order.type.gas_groups'),
                            'Orders.parent_id' => $order_parent->id])
                    ->count();

                Log::info('Ordine titolare ['.$order_parent->id.'] organization_id ['.$organization_id.'] data_fine ['.$order_parent->data_fine.'] tot_orders '.$tot_orders, ['scope' => ['shell']]);
                if($tot_orders==0) {
                    $this->Orders->delete($order_parent);
                    Log::info('Ordine titolare ['.$order_parent->id.'] organization_id ['.$organization_id.'] eliminato', ['scope' => ['shell']]);
                }
                else 
                    Log::info('Ordine titolare ['.$order_parent->id.'] organization_id ['.$organization_id.'] NON eliminato', ['scope' => ['shell']]);
            } // end foreach($order_parents as $order_parent)
        } // end if($order_parents->count()>0) 

        if($debug) {
            $this->io = $io;
            $this->io->out('OrdersTypeGasParentGroupsCommand end');
        }
    }
}