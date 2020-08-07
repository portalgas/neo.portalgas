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
 * bin/cake CreateShMailOrdersClose
 * https://book.cakephp.org/4/en/console-commands/commands.html
 */ 
class CreateShMailOrdersCloseCommand extends MyCommand
{
    private $cron =  'mailUsersOrdersClose';
    private $file_name_sh =  'mailUsersOrdersClose-%s.sh';

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $debug = false;

        /*
         * lo passo alla classe super
         */
        $this->_setCron($this->cron);
        $this->_setFileNameSh($this->file_name_sh); 

        if($debug) {
            $this->io = $io;
            $this->io->out('CreateShMailUsersOrdersClose start');
            $this->io->out('creo gruppi ogni '.$this->mail_send_max);            
        }

        $GGMailToAlertOrderClose = (Configure::read('GGMailToAlertOrderClose')+1);

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        $organizations = $this->_getOrganizationsGas();

        /*
         * per ogni GAS ctrl se ha ordini che si chiuderanno da notificare per mail
         */            
        $modelOrders = $this->loadModel('Orders');
        Log::info("Organizations prima del controllo ordine che si chiuderanno tra ".$GGMailToAlertOrderClose."gg ".$organizations->count(), ['scope' => ['shell']]);
        $organizationResults = [];
        foreach ($organizations as $numResult => $organization) {

            $where = ['Orders.organization_id' => $organization->id,
                      'Orders.isVisibleFrontEnd' => 'Y',
                      'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'CLOSE'],
                      'Orders.data_fine = CURDATE() + INTERVAL '.$GGMailToAlertOrderClose.' DAY',
                        'SuppliersOrganizations.stato' => 'Y',
                        'SuppliersOrganizations.mail_order_close' => 'Y'
                      ];                                
            $orderResults = $modelOrders->find()
                                        ->contain(['SuppliersOrganizations'])
                                        ->where($where)
                                        ->all();                                        
            if($orderResults->count()>0) { 
                $organizationResults[] = $organization;
                Log::info('Organization '.$organization->name.' ha ordini che si chiuderanno', ['scope' => ['shell']]);
            }
        } 
        Log::info("Organizations dopo del controllo ordine aperti ".count($organizationResults), ['scope' => ['shell']]);         

        /*
         * estraggo i totali user per ogni GAS rimasto
         */
        $totResults = $this->_getOrganizationTotUsers($organizationResults, $debug);

        /*
         * creo gruppi da massimo users
         */
        $results = $this->_getArrayMailSendMax($totResults, $debug);
        Log::info($results, ['scope' => ['shell']]);
        Log::info('CreateShMailUsersOrdersClose end', ['scope' => ['shell']]);
        Log::info($results, ['scope' => ['shell']]);

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