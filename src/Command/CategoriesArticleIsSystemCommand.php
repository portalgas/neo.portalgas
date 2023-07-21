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
 *          
 * bin/cake CategoriesArticleIsSystem
 * https://book.cakephp.org/4/en/console-commands/commands.html 
 *
 * /var/www/neo.portalgas/src/Command/Sh/categoriesArticleIsSystem.sh >> /var/portalgas/cron/log/$(date +\%Y\%m\%d)_categoriesArticleIsSystem.log 2>&1
 */ 
class CategoriesArticleIsSystemCommand extends Command
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
        $debug = true;

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        if($debug) {
            $this->io = $io;
            $this->io->out('CategoriesArticleIsSystem start');
        }

        $organization_id = $args->getArgument('organization_id');
        if(empty($organization_id)) dd('organization_id required!');
        else 
        $this->io->out('elaboro organization_id ['.$organization_id.']');
        $this->loadModel('CategoriesArticles');
        $results = $this->CategoriesArticles->getIsSystem(null, $organization_id, $truncate=true, $debug=true);
                                  
        if($debug) {
            $this->io = $io;
            $this->io->out('CategoriesArticleIsSystem end');
        }
    }
}