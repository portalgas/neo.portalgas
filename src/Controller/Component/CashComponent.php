<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class CashComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	public function getHistoryByUser($user, $organization_id, $user_id, $debug=false) {

        $results = [];

        $cashesHistoriesTable = TableRegistry::get('CashesHistories');
        $cashesHistories = $cashesHistoriesTable->getByUser($user, $organization_id, $user_id);
        if($cashesHistories->count()==0)
            return $results; 

        $cashesHistories = $cashesHistories->toArray();

        /*
         * aggiungo ultimo movimento
         */
        $cashesTable = TableRegistry::get('Cashes');
        $cashes = $cashesTable->getByUser($user, $organization_id, $user_id);

        if(!empty($cashes))    
            $cashesHistories[count($cashesHistories)] = $cashes;
        $importo = 0;
        $importo_old = 0;
        
        /*
         * calcolo dei saldi alle operazioni
         */
        // debug($cashesHistories);
        foreach($cashesHistories as $numResult => $result) {
            if($numResult>0) {
                $importo_old = $cashesHistories[$numResult-1]->importo;
                $importo = $cashesHistories[$numResult]->importo;

                $operazione = (-1*($importo_old - $importo));
                $cashesHistories[$numResult-1]->operazione = $operazione;
                $cashesHistories[$numResult-1]->operazione_ = number_format($operazione,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $cashesHistories[$numResult-1]->operazione_e = number_format($operazione,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro;';

                if ($importo_old==0)
                    $cashesHistories[$numResult-1]->color_alert = 'trasparent';
                else
                if ($importo_old>0)
                    $cashesHistories[$numResult-1]->color_alert = 'green';
                else
                if ($importo_old<0)
                    $cashesHistories[$numResult-1]->color_alert = 'red';

                $cashesHistories[$numResult-1]->importo = $importo_old;
                $cashesHistories[$numResult-1]->importo_ = number_format($importo_old,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
                $cashesHistories[$numResult-1]->importo_e = number_format($importo_old,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro;';               
            }   
        }

        $cashesHistories[$numResult]->operazione = '';
        $cashesHistories[$numResult]->operazione_ = '';
        $cashesHistories[$numResult]->operazione_e = '';     

        /*
         * aggiungo la prima riga con partenza saldo a 0
         * porto nota / modified all'occorrenza dell'array precedente
         */
        $results = []; 
        if(!empty($cashesHistories)) {      
            $results[0]['importo'] = '0';
            $results[0]['importo_'] = '0,00';
            $results[0]['importo_e'] = '0,00 &euro;';
            $operazione = (-1*(0 - $cashesHistories[0]->importo));
            
            $results[0]['color_alert'] = 'trasparent';

            $results[0]['nota'] = $cashesHistories[0]->nota;
            $results[0]['modified'] = $cashesHistories[0]->modified;

            $results[0]['operazione'] = $operazione;
            $results[0]['operazione_'] = number_format($operazione,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
            $results[0]['operazione_e'] = number_format($operazione,2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia')).' &euro;';
        
            foreach($cashesHistories as $numResult => $cashesHistory) {

                $index_next = ((int)$numResult+1);

                $results[$index_next] = $cashesHistory;
                
                if(isset($cashesHistories[$index_next])) {
                    $results[$index_next]['nota'] = $cashesHistories[$index_next]->nota;
                    $results[$index_next]['modified'] = $cashesHistories[$index_next]['modified'];
                }
                else {
                    /*
                     * ultima riga non visualizzo la data perche' e' una riga di totale
                     */
                    $results[$index_next]['nota'] = "";
                    $results[$index_next]['modified'] = '';
                }
            }
        }

		if($debug) debug($results);
 
		return $results;
	}
}