<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;

/*
 * /var/www/neo.portalgas/src/Command/Sh/supplierVotes.sh
 * /var/www/neo.portalgas/bin/cake SupplierVotes {supplier_id}
 */ 
class SupplierVotesCommand extends Command
{
    private $_modelSuppliersVotes = null;

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
            $this->io->out('SupplierVoto start');
        }

        $this->_modelSuppliersVotes = $this->loadModel('SuppliersVotes');

        $supplier_id = $args->getArgument('supplier_id');
        
        Log::info("SupplierVoto start supplier_id ".$supplier_id, ['scope' => ['shell']]);

        if(empty($supplier_id))
            $this->_runBySuppliersVotes();
        else
            $this->_runBySuppliers($supplier_id);

    }

    /*
     * parto dalla tabella SuppliersVote
     */
    private function _runBySuppliersVotes() {
        Log::info("SupplierVoto _runBySuppliersVotes()", ['scope' => ['shell']]);

        $suppliersVotesResults = $this->_modelSuppliersVotes->find()
                                  ->select(['SuppliersVotes.supplier_id'])
                                  ->group(['SuppliersVotes.supplier_id'])
                                  ->all();

        if($suppliersVotesResults->count()>0) {
            foreach($suppliersVotesResults as $suppliersVotesResult) {
                
                $supplier_id = $suppliersVotesResult->supplier_id;

                $voto = $this->_getAvgSuppliersVotesBySupplierId($supplier_id);
                
                $this->_updateSupplier($supplier_id, $voto);

                Log::info("SupplierVotes supplier_id [".$supplier_id."] voto ".$voto, ['scope' => ['shell']]);
            }
        }

    }

    /*
     * parto dalla tabella Suppliers filtrando per supplier_id
     */
    private function _runBySuppliers($supplier_id) {
        Log::info("SupplierVotes _runBySuppliers()", ['scope' => ['shell']]);

        $voto = $this->_getAvgSuppliersVotesBySupplierId($supplier_id);
        
        $this->_updateSupplier($supplier_id, $voto);

        Log::info("SupplierVotes supplier_id [".$supplier_id."] voto ".$voto, ['scope' => ['shell']]);
    }

    private function _getAvgSuppliersVotesBySupplierId($supplier_id) {

        $results = null;

        $where = ['SuppliersVotes.supplier_id' => $supplier_id];

        $query = $this->_modelSuppliersVotes
                                    ->find()
                                    ->where($where);

        $results = $query->select(['avg' => $query->func()->avg('voto')])->first();

        $voto = ceil($results->avg); /* arrotonda per eccesso */

        return $voto;
    }

    private function _updateSupplier($supplier_id, $voto) {
        
        $modelSuppliers = $this->loadModel('Suppliers');

        $supplier = $modelSuppliers->get($supplier_id);

        $datas = [];
        $datas['voto'] = $voto;

        $supplier = $modelSuppliers->patchEntity($supplier, $datas);
        if (!$modelSuppliers->save($supplier)) {
            Log::info($supplier->getErrors(), ['scope' => ['shell']]);
        }
    }
}