<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\CronMailsComponent;

/*
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersDelivery.sh
 * /var/www/neo.portalgas/bin/cake MailUsersDelivery {organization_id}
 * 
 * in /var/www/neo.portalgas/bin/cake MailUsersDelivery 156 gli passo organization_id = 156 (gas gruppi)
 */ 
class MailUsersDeliveryCommand extends Command
{
    private $_CronMails;
    
    public function initialize() {
        $this->_CronMails = new CronMailsComponent(new ComponentRegistry());
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
        $debug = true;

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        if($debug) {
            $this->io = $io;
            $this->io->out('CronMails mailUsersDelivery start');
        }

        $organization_id = $args->getArgument('organization_id');
        if(empty($organization_id)) dd('organization_id required!');

        $this->_CronMails->mailUsersDelivery($organization_id, $debug);

        if($debug) {
            $this->io = $io;
            $this->io->out('CronMails mailUsersDelivery end');
        }
    }
}