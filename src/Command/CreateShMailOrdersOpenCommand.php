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
 * bin/cake CreateShMailOrdersOpen
 * https://book.cakephp.org/4/en/console-commands/commands.html
 * 
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersOrdersOpen-1.sh ... 7.sh
 */ 
class CreateShMailOrdersOpenCommand extends MyCommand
{
    private $cron =  'mailUsersOrdersOpen';

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
            $this->io->out('CreateShMailOrderOpen start');
            $this->io->out('creo gruppi ogni '.$this->mail_send_max);            
        }
        Log::info("CreateShMailOrderOpen start", ['scope' => ['shell']]);
        Log::info('creo gruppi ogni '.$this->mail_send_max, ['scope' => ['shell']]);

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        $organizations = $this->_getOrganizationsGas();

        /*
         * per ogni GAS ctrl se ha ordini aperti da notificare per mail
         */            
        $modelOrders = $this->loadModel('Orders');
        Log::info("Organizations prima del controllo ordine aperti ".$organizations->count(), ['scope' => ['shell']]);
        $organizationResults = [];
        foreach ($organizations as $numResult => $organization) {

            $where = ['Orders.organization_id' => $organization->id,
                      'Orders.isVisibleFrontEnd' => 'Y',
                      'Orders.state_code NOT IN' => ['CREATE-INCOMPLETE', 'CLOSE'],
                      'or' => ['Orders.data_inizio = CURDATE() - INTERVAL '.Configure::read('GGMailToAlertOrderOpen').' DAY',
                               'Orders.mail_open_send' => 'Y'],
                        'SuppliersOrganizations.stato' => 'Y',
                        'SuppliersOrganizations.mail_order_open' => 'Y'       
                      ];                                
            $orderResults = $modelOrders->find()
                                        ->contain(['SuppliersOrganizations'])
                                        ->where($where)
                                        ->all();                                        
            if($orderResults->count()>0) { 
                $organizationResults[] = $organization;
                Log::info('Organization '.$organization->name.' ha ordini aperti', ['scope' => ['shell']]);
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
        Log::info('CreateShMailOrderOpen end', ['scope' => ['shell']]);
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
        $esito = true;
        if($debug) $this->io->out($esito);
    }
}