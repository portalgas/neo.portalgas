<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Sluggable\Utility\Slug;

/*
 * /var/www/neo.portalgas/src/Command/Sh/supplierSlug.sh
 * /var/www/neo.portalgas/bin/cake SupplierSlug {supplier_id}
 */ 
class SupplierSlugCommand extends Command
{
    private $_modelSuppliers = null;

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->addArgument('supplier_id', [
                'help' => 'supplier_id'
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
            $this->io->out('SupplierSlug start');
        }

        $this->_modelSuppliers = $this->loadModel('Suppliers');

        $supplier_id = $args->getArgument('supplier_id');

        Log::info("SupplierSlug start supplier_id ".$supplier_id, ['scope' => ['shell']]);

        $suppliers = $this->_getSuppliers($supplier_id);

        foreach ($suppliers as $supplier) {
            
            $slug = Slug::generate($supplier->name);
            Log::info($supplier->id.' ['.$supplier->slug.'] '.$slug, ['scope' => ['shell']]);

            $datas = [];
            $datas['slug'] = $slug;

            $supplier = $this->_modelSuppliers->patchEntity($supplier, $datas);
            if (!$this->_modelSuppliers->save($supplier)) {
                Log::info($supplier->getErrors(), ['scope' => ['shell']]);
            }            
        } 

        /*
         * valore di ritorno $results = exec(...)
         */
        if($debug) {
            $esito = true;
            $this->io->out($esito);            
        }
    }

    private function _getSuppliers($supplier_id=0) {

        $where = [];
        if(!empty($supplier_id))
            $where += ['Suppliers.id' => $supplier_id];
        else
            $where += ['Suppliers.slug is null'];
        $suppliers = $this->_modelSuppliers->find()
                                        ->where($where)
                                        // ->limit(5)
                                        ->all();
        if($suppliers->count()==0) {
            Log::info("Suppliers empty!!", ['scope' => ['shell']]); 
            $this->abort();           
        }

        return $suppliers;
    }
}