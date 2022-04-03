<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\SitemapComponent;

/*
 * creo sitemap.xml
 * /var/www/neo.portalgas/src/Command/Sh/sitemap.sh
 * /var/www/neo.portalgas/bin/cake Sitemap
 */ 
class SitemapCommand extends Command
{
    private $_Sitemap;

    public function initialize() {
        $this->_Sitemap = new SitemapComponent(new ComponentRegistry());
    }
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
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
            $this->io->out('Sitemap start');
        }

        $this->_Sitemap->create();

        if($debug) {
            $this->io = $io;
            $this->io->out('Sitemap end');
        }
    }
}