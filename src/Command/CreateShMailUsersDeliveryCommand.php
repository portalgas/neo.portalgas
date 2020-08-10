<?php
namespace App\Command;

use \App\Command\MyCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Core\Configure;

/*
 *          
 * bin/cake CreateShMailUsersDelivery
 * https://book.cakephp.org/4/en/console-commands/commands.html
 * 
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersDelivery-1.sh ... 7.sh
 */ 
class CreateShMailUsersDeliveryCommand extends MyCommand
{
    private $cron =  'mailUsersDelivery';

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $debug = false;

        /*
         * lo passo alla classe super
         */
        $this->_setCron($this->cron);
        $this->_setFileNameSh($this->file_name_shs[$this->cron]); 

        if($debug) {
            $this->io = $io;
            $this->io->out('CreateShMailUsersDelivery start');
            $this->io->out('creo gruppi ogni '.$this->mail_send_max);
        }
        
        $GGMailToAlertDeliveryOn = Configure::read('GGMailToAlertDeliveryOn');

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        $organizations = $this->_getOrganizationsGas();

        /*
         * per ogni GAS ctrl se ha consegne che si apriranno domani
         */            
        $modelDeliveries = $this->loadModel('Deliveries');
        Log::info("Organizations prima del controllo consegne che si aprono domani ".$organizations->count(), ['scope' => ['shell']]);
        $organizationResults = [];
        foreach ($organizations as $numResult => $organization) {

            $where = ['Deliveries.organization_id' => $organization->id,
                      'Deliveries.isVisibleFrontEnd' => 'Y',
                      'Deliveries.stato_elaborazione' => 'OPEN',
                      'DATE(Deliveries.data) = CURDATE() + INTERVAL '.Configure::read('GGMailToAlertDeliveryOn').' DAY '];                                
            $deliveriesResults = $modelDeliveries->find()
                                        ->where($where)
                                        ->all();                                        
            if($deliveriesResults->count()>0) { 
                $organizationResults[] = $organization;
                Log::info('Organization '.$organization->name.' ha consegne che si aprono domani', ['scope' => ['shell']]);
            }
        } 
        Log::info("Organizations dopo del controllo consegne che si aprono domani ".count($organizationResults), ['scope' => ['shell']]);         

        /*
         * estraggo i totali user per ogni GAS rimasto
         */
        $totResults = $this->_getOrganizationTotUsers($organizationResults, $debug);

        /*
         * creo gruppi da massimo users
         */
        $results = $this->_getArrayMailSendMax($totResults, $debug);
        Log::info($results, ['scope' => ['shell']]);
        Log::info('CreateShMailUsersDelivery end', ['scope' => ['shell']]);
        Log::info($results, ['scope' => ['shell']]);

        $this->_deleteOldFileSh($this->cron);

        /*
         * per ogni gruppo da massimo users
         * creo file .sh che sara' richiamato dal cron
         */
        $this->_writeFilesSh($results, $debug);

        /*
         * valore di ritorno $results = exec(...)
         */
        if($debug) {
            $esito = true;
            $this->io->out($esito);            
        }
    }
}