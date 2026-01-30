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
 * per il GAS dei gruppi elimina le consenge senza ordini
 * /var/www/neo.portalgas/src/Command/Sh/deliveriesTypeGasParentGroups.sh
 * /var/www/neo.portalgas/bin/cake DeliveriesTypeGasParentGroups {organization_id}
 * 
 * cron 
 * 45 0 * * * /var/www/neo.portalgas/src/Command/Sh/deliveriesTypeGasParentGroups.sh >> /var/portalgas/cron/log/$(date +\%Y\%m\%d)_deliveriesTypeGasParentGroupsNeo.log 2>&1
 */ 
class DeliveriesTypeGasParentGroupsCommand extends Command
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
            $this->io->out('DeliveriesTypeGasParentGroupsCommand start');
        }

        $organization_id = $args->getArgument('organization_id');
        if(empty($organization_id)) dd('organization_id required!');

        $modelOrganizations = $this->loadModel('Organizations');
        $organization = $modelOrganizations->get($organization_id);
        if(empty($organization)) dd('organization_id not found!');

        $where_deliveires = ['Deliveries.type' => 'GAS-GROUP', 
                             'Deliveries.sys' => 'N', 
                             'DATE(Deliveries.data) < CURDATE() - '.Configure::read('GGDeleteDeliveriesTypeGasParentGroups')
                            ];
        $organization_params_config = json_decode($organization->paramsConfig);
        if ($organization_params_config->hasStoreroom == 'Y' && $organization_params_config->hasStoreroomFrontEnd == 'Y')
            $where_deliveires +=  ['OR' => [
                                            ['Deliveries.isToStoreroom' => 'Y', 'Deliveries.isToStoreroomPay' => 'Y'],
                                            ['Deliveries.isToStoreroom' => 'N']
                                        ]
                                    ];

        $this->loadModel('GasGroupDeliveries');
        $gas_group_deliveries = $this->GasGroupDeliveries->find()
                                ->contain(['Deliveries' => ['conditions' => $where_deliveires, 'Orders']])
                                ->where(['Deliveries.organization_id' => $organization_id,
                                        'GasGroupDeliveries.organization_id' => $organization_id])
                                  //->limit(1)
                                  ->order(['GasGroupDeliveries.id' => 'desc'])
                                  ->all();


        if($gas_group_deliveries->count()>0) {
           foreach($gas_group_deliveries as $gas_group_delivery) {  
                $tot_orders = count($gas_group_delivery->delivery->orders);
                Log::info('Per la consegna di gruppo ['.$gas_group_delivery->id.'] consegna ['.$gas_group_delivery->delivery->luogo.'] ['.$gas_group_delivery->delivery->data.'] tot_orders '.$tot_orders, ['scope' => ['shell']]);

                //dd($gas_group_delivery->delivery); 
                if($tot_orders==0) {
                    $this->GasGroupDeliveries->delete($gas_group_delivery); // trigger elimina consegna
                    Log::info('consegna di gruppo ['.$gas_group_delivery->id.'] organization_id ['.$organization_id.'] e rispettiva consegna: eliminati', ['scope' => ['shell']]);
                }
                else 
                    Log::info('consegna di gruppo ['.$gas_group_delivery->id.'] organization_id ['.$organization_id.'] e rispettiva consegna: NON eliminati', ['scope' => ['shell']]);
                } // end foreach($gas_group_deliveries as $gas_group_delivery)
        } // end if($gas_group_deliveries->count()>0)

        if($debug) {
            $this->io = $io;
            $this->io->out('DeliveriesTypeGasParentGroupsCommand end');
        }
    }
}